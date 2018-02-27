<?php
/**
 * October CMS plugin: Adrenth.Redirect
 *
 * Copyright (c) 2016 - 2018 Alwin Drenth
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute,
 * sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or
 * substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

declare(strict_types=1);

namespace Adrenth\Redirect\Classes;

use Adrenth\Redirect\Classes\Exceptions\InvalidScheme;
use Adrenth\Redirect\Classes\Exceptions\RulesPathNotReadable;
use Adrenth\Redirect\Models\Client;
use Adrenth\Redirect\Models\Redirect;
use Adrenth\Redirect\Models\RedirectLog;
use BadMethodCallException;
use Carbon\Carbon;
use Cms;
use Cms\Classes\Controller;
use Cms\Classes\Theme;
use DB;
use Exception;
use InvalidArgumentException;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use League\Csv\Reader;
use Log;
use Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RedirectManager
 *
 * @package Adrenth\Redirect\Classes
 */
class RedirectManager
{
    /** @var string */
    private $redirectRulesPath;

    /** @var RedirectRule[] */
    private $redirectRules;

    /** @var Carbon */
    private $matchDate;

    /** @var string */
    private $basePath;

    /** @var bool */
    private $loggingEnabled = true;

    /** @var bool */
    private $statisticsEnabled = true;

    /**
     * HTTP 1.1 headers
     *
     * @var array
     */
    private static $headers = [
        301 => 'HTTP/1.1 301 Moved Permanently',
        302 => 'HTTP/1.1 302 Found',
        303 => 'HTTP/1.1 303 See Other',
        404 => 'HTTP/1.1 404 Not Found',
        410 => 'HTTP/1.1 410 Gone',
    ];

    /**
     * Constructs a RedirectManager instance.
     */
    protected function __construct()
    {
        $this->matchDate = Carbon::now();
        $this->basePath = Request::getBasePath();
    }

    /**
     * Creates an instance of the RedirectManager with the default rules path.
     *
     * @return RedirectManager
     * @throws RulesPathNotReadable
     */
    public static function createWithDefaultRulesPath(): RedirectManager
    {
        $rulesPath = storage_path('app/redirects.csv');

        if (!file_exists($rulesPath) || !is_readable($rulesPath)) {
            throw RulesPathNotReadable::withPath($rulesPath);
        }

        return self::createWithRulesPath($rulesPath);
    }

    /**
     * Create an instance of the RedirectManager with a specific rules path.
     *
     * @param string $redirectRulesPath
     * @return RedirectManager
     */
    public static function createWithRulesPath(string $redirectRulesPath): RedirectManager
    {
        $instance = new self();
        $instance->redirectRulesPath = $redirectRulesPath;
        return $instance;
    }

    /**
     * Create an instance of the RedirectManager with a redirect rule.
     *
     * @param RedirectRule $rule
     * @return RedirectManager
     */
    public static function createWithRule(RedirectRule $rule): RedirectManager
    {
        $instance = new self();
        $instance->redirectRules[] = $rule;
        return $instance;
    }

    /**
     * Enable or disable logging.
     *
     * @param bool $loggingEnabled
     * @return RedirectManager
     */
    public function setLoggingEnabled(bool $loggingEnabled): RedirectManager
    {
        $this->loggingEnabled = $loggingEnabled;
        return $this;
    }

    /**
     * Enable or disable gathering of statistics.
     *
     * @param bool $statisticsEnabled
     * @return RedirectManager
     */
    public function setStatisticsEnabled(bool $statisticsEnabled): RedirectManager
    {
        $this->statisticsEnabled = $statisticsEnabled;
        return $this;
    }

    /**
     * @param string $basePath
     * @return RedirectManager
     */
    public function setBasePath(string $basePath): RedirectManager
    {
        $this->basePath = rtrim($basePath, '/');
        return $this;
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Find a match based on given URL.
     *
     * @param string $requestPath
     * @param string $scheme 'http' or 'https'
     * @return RedirectRule|false
     * @throws InvalidScheme
     */
    public function match(string $requestPath, string $scheme)
    {
        if ($scheme !== Redirect::SCHEME_HTTP && $scheme !== Redirect::SCHEME_HTTPS) {
            throw InvalidScheme::withScheme($scheme);
        }

        $requestPath = urldecode($requestPath);

        $this->loadRedirectRules();

        foreach ($this->redirectRules as $rule) {
            if ($matchedRule = $this->matchesRule($rule, $requestPath, $scheme)) {
                return $matchedRule;
            }
        }

        return false;
    }

    /**
     * Find a match based on given URL (uses caching).
     *
     * @param string $requestPath
     * @param string $scheme 'http' or 'https'
     * @return RedirectRule|false|mixed
     * @throws InvalidScheme
     * @throws BadMethodCallException
     */
    public function matchCached(string $requestPath, string $scheme)
    {
        $cacheManager = CacheManager::instance();
        $cacheKey = $cacheManager->cacheKey($requestPath, $scheme);

        if ($cacheManager->has($cacheKey)) {
            $cachedItem = $cacheManager->get($cacheKey);

            // Verify the data from cache. In some cases a cache driver can not unserialize
            // (due to invalid php configuration) the cached data which causes this function to return invalid data.
            //
            // E.g. memcache:
            // - Memcached::get(): could not unserialize value, no igbinary support in ...
            // - Memcached::get(): could not unserialize value, no msgpack support in ...
            if ($cachedItem === false || $cachedItem instanceof RedirectRule) {
                return $cachedItem;
            }
        }

        $matchedRule = $this->match($requestPath, $scheme);

        return $cacheManager->putMatch($cacheKey, $matchedRule);
    }

    /**
     * Redirect with specific rule.
     *
     * @param RedirectRule $rule
     * @param string $requestUri
     * @return void
     */
    public function redirectWithRule(RedirectRule $rule, string $requestUri)//: void
    {
        $this->updateStatistics($rule->getId());

        $statusCode = $rule->getStatusCode();

        if ($statusCode === 404 || $statusCode === 410) {
            header(self::$headers[$statusCode], true, $statusCode);
            $this->addLogEntry($rule, $requestUri, '');
            exit(0);
        }

        $toUrl = $this->getLocation($rule);

        if (!$toUrl || empty($toUrl)) {
            return;
        }

        $this->addLogEntry($rule, $requestUri, $toUrl);

        header(self::$headers[$statusCode], true, $statusCode);
        header('X-Redirect-By: Adrenth.Redirect');
        header('Location: ' . $toUrl, true, $statusCode);

        exit(0);
    }

    /**
     * Get Location URL to redirect to.
     *
     * @param RedirectRule $rule
     * @return bool|string
     */
    public function getLocation(RedirectRule $rule)
    {
        $toUrl = false;

        // Determine the URL to redirect to
        switch ($rule->getTargetType()) {
            case Redirect::TARGET_TYPE_PATH_URL:
                $toUrl = $this->redirectToPathOrUrl($rule);

                // Check if $toUrl is a relative path, if so, we need to add the base path to it.
                // Refs: https://github.com/adrenth/redirect/issues/21
                if (is_string($toUrl)
                    && $toUrl[0] !== '/'
                    && substr($toUrl, 0, 7) !== 'http://'
                    && substr($toUrl, 0, 8) !== 'https://'
                ) {
                    $toUrl = $this->basePath . '/' . $toUrl;
                }

                if ($toUrl[0] === '/') {
                    $toUrl = Cms::url($toUrl);
                }

                break;
            case Redirect::TARGET_TYPE_CMS_PAGE:
                $toUrl = $this->redirectToCmsPage($rule);
                break;
            case Redirect::TARGET_TYPE_STATIC_PAGE:
                $toUrl = $this->redirectToStaticPage($rule);
                break;
        }

        if ($rule->getToScheme() !== Redirect::SCHEME_AUTO) {
            $toUrl = str_replace(['https://', 'http://'], $rule->getToScheme() . '://', $toUrl);
        }

        return $toUrl;
    }

    /**
     * @param RedirectRule $rule
     * @return string
     */
    private function redirectToPathOrUrl(RedirectRule $rule): string
    {
        if ($rule->isExactMatchType()) {
            return $rule->getToUrl();
        }

        $placeholderMatches = $rule->getPlaceholderMatches();

        return str_replace(
            array_keys($placeholderMatches),
            array_values($placeholderMatches),
            $rule->getToUrl()
        );
    }

    /**
     * @param RedirectRule $rule
     * @return string
     */
    private function redirectToCmsPage(RedirectRule $rule): string
    {
        $controller = new Controller(Theme::getActiveTheme());

        $parameters = [];

        // Strip curly braces from keys
        foreach ($rule->getPlaceholderMatches() as $placeholder => $value) {
            $parameters[str_replace(['{', '}'], '', $placeholder)] = $value;
        }

        return $controller->pageUrl($rule->getCmsPage(), $parameters);
    }

    /**
     * @param RedirectRule $rule
     * @return string|bool
     */
    private function redirectToStaticPage(RedirectRule $rule)
    {
        if (class_exists('\RainLab\Pages\Classes\Page')) {
            /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
            return \RainLab\Pages\Classes\Page::url($rule->getStaticPage());
        }

        return false;
    }

    /**
     * Change the match date; can be used to perform tests.
     *
     * @param Carbon $matchDate
     * @return RedirectManager
     */
    public function setMatchDate(Carbon $matchDate): RedirectManager
    {
        $this->matchDate = $matchDate;
        return $this;
    }

    /**
     * Check if rule matches against request path and scheme.
     *
     * @param RedirectRule $rule
     * @param string $requestPath
     * @param string $scheme
     * @return RedirectRule|bool
     */
    private function matchesRule(RedirectRule $rule, string $requestPath, string $scheme)
    {
        if (!$this->matchesScheme($rule, $scheme)
            || !$this->matchesPeriod($rule)
        ) {
            return false;
        }

        // Perform exact match if applicable
        if ($rule->isExactMatchType()) {
            return $this->matchExact($rule, $requestPath);
        }

        // Perform placeholders match if applicable
        if ($rule->isPlaceholdersMatchType()) {
            return $this->matchPlaceholders($rule, $requestPath);
        }

        return false;
    }

    /**
     * Perform an exact URL match.
     *
     * @param RedirectRule $rule
     * @param string $url
     * @return RedirectRule|bool
     */
    private function matchExact(RedirectRule $rule, string $url)
    {
        return $url === $rule->getFromUrl() ? $rule : false;
    }

    /**
     * Perform a placeholder URL match.
     *
     * @param RedirectRule $rule
     * @param string $url
     * @return RedirectRule|bool
     */
    private function matchPlaceholders(RedirectRule $rule, string $url)
    {
        $route = new Route($rule->getFromUrl());

        foreach ($rule->getRequirements() as $requirement) {
            try {
                $route->setRequirement(
                    str_replace(['{', '}'], '', $requirement['placeholder']),
                    $requirement['requirement']
                );
            } catch (InvalidArgumentException $e) {
                // Catch empty requirement / placeholder
            }
        }

        $routeCollection = new RouteCollection();
        $routeCollection->add($rule->getId(), $route);

        try {
            $matcher = new UrlMatcher($routeCollection, new RequestContext('/'));
            $match = $matcher->match($url);

            $items = array_except($match, '_route');

            foreach ($items as $key => $value) {
                $placeholder = '{' . $key . '}';
                $replacement = $this->findReplacementForPlaceholder($rule, $placeholder);
                $items[$placeholder] = $replacement ?? $value;
                unset($items[$key]);
            }

            $rule->setPlaceholderMatches($items);
        } catch (Exception $e) {
            return false;
        }

        return $rule;
    }

    /**
     * Check if rule matches a period.
     *
     * @param RedirectRule $rule
     * @return bool
     */
    private function matchesPeriod(RedirectRule $rule): bool
    {
        if ($rule->getFromDate() instanceof Carbon
            && $rule->getToDate() instanceof Carbon
        ) {
            return $this->matchDate->between($rule->getFromDate(), $rule->getToDate());
        }

        if ($rule->getFromDate() instanceof Carbon
            && $rule->getToDate() === null
        ) {
            return $this->matchDate->gte($rule->getFromDate());
        }

        if ($rule->getToDate() instanceof Carbon
            && $rule->getFromDate() === null
        ) {
            return $this->matchDate->lte($rule->getToDate());
        }

        return true;
    }

    /**
     * @param RedirectRule $rule
     * @param string $scheme
     * @return bool
     */
    private function matchesScheme(RedirectRule $rule, string $scheme): bool
    {
        if ($rule->getFromScheme() === Redirect::SCHEME_AUTO) {
            return true;
        }

        return $rule->getFromScheme() === $scheme;
    }

    /**
     * Find replacement value for placeholder.
     *
     * @param RedirectRule $rule
     * @param string $placeholder
     * @return string|null
     */
    private function findReplacementForPlaceholder(RedirectRule $rule, string $placeholder)//: ?string
    {
        foreach ($rule->getRequirements() as $requirement) {
            if ($requirement['placeholder'] === $placeholder && !empty($requirement['replacement'])) {
                return (string) $requirement['replacement'];
            }
        }

        return null;
    }

    /**
     * Load definitions into memory.
     *
     * @return void
     */
    private function loadRedirectRules()//: void
    {
        if ($this->redirectRules !== null) {
            return;
        }

        $rules = [];

        try {
            /** @var Reader $reader */
            $reader = Reader::createFromPath($this->redirectRulesPath);

            // WARNING: this is deprecated method in league/csv:8.0, when league/csv is upgraded to version 9 we should
            // follow the instructions on this page: http://csv.thephpleague.com/upgrading/9.0/
            $results = $reader->fetchAssoc(0);

            foreach ($results as $row) {
                $rule = new RedirectRule($row);

                if ($this->matchesPeriod($rule)) {
                    $rules[] = $rule;
                }
            }
        } catch (Exception $e) {
            Log::error($e);
        }

        $this->redirectRules = $rules;
    }

    /**
     * Update database statistics.
     *
     * @param int $redirectId
     */
    private function updateStatistics(int $redirectId)//: void
    {
        if (!$this->statisticsEnabled) {
            return;
        }

        /** @var Redirect $redirect */
        $redirect = Redirect::find($redirectId);

        if ($redirect === null) {
            return;
        }

        $now = Carbon::now();

        /** @noinspection PhpUndefinedClassInspection */
        $redirect->update([
            'hits' => DB::raw('hits + 1'),
            'last_used_at' => $now,
        ]);

        $crawlerDetect = new CrawlerDetect();

        Client::create([
            'redirect_id' => $redirectId,
            'timestamp' => $now,
            'day' => $now->day,
            'month' => $now->month,
            'year' => $now->year,
            'crawler' => $crawlerDetect->isCrawler() ? $crawlerDetect->getMatches() : null,
        ]);
    }

    /**
     * Adds a log entry to the database.
     *
     * @param RedirectRule $rule
     * @param string $requestUri
     * @param string $toUrl
     * @return void
     */
    private function addLogEntry(RedirectRule $rule, string $requestUri, string $toUrl)//: void
    {
        if (!$this->loggingEnabled) {
            return;
        }

        /** @var Redirect $redirect */
        $redirect = Redirect::find($rule->getId());

        if ($redirect === null) {
            return;
        }

        $now = Carbon::now();

        RedirectLog::create([
            'redirect_id' => $rule->getId(),
            'from_url' => $requestUri,
            'to_url' => $toUrl,
            'status_code' => $rule->getStatusCode(),
            'day' => $now->day,
            'month' => $now->month,
            'year' => $now->year,
            'date_time' => $now,
        ]);
    }
}

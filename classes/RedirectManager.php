<?php

namespace Adrenth\Redirect\Classes;

use Adrenth\Redirect\Classes\Exceptions\RulesPathNotReadable;
use Adrenth\Redirect\Models\Client;
use Adrenth\Redirect\Models\Redirect;
use Adrenth\Redirect\Models\RedirectLog;
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
     * @return RedirectManager
     * @throws RulesPathNotReadable
     */
    public static function createWithDefaultRulesPath()
    {
        $rulesPath = storage_path('app/redirects.csv');

        if (!file_exists($rulesPath) || !is_readable($rulesPath)) {
            throw RulesPathNotReadable::withPath($rulesPath);
        }

        return RedirectManager::createWithRulesPath($rulesPath);
    }

    /**
     * @param $redirectRulesPath
     * @return RedirectManager
     */
    public static function createWithRulesPath($redirectRulesPath)
    {
        $instance = new self();
        $instance->redirectRulesPath = $redirectRulesPath;
        return $instance;
    }

    /**
     * @param RedirectRule $rule
     * @return RedirectManager
     */
    public static function createWithRule(RedirectRule $rule)
    {
        $instance = new self();
        $instance->redirectRules[] = $rule;
        return $instance;
    }

    /**
     * @param bool $loggingEnabled
     * @return RedirectManager
     */
    public function setLoggingEnabled($loggingEnabled)
    {
        $this->loggingEnabled = (bool) $loggingEnabled;
        return $this;
    }

    /**
     * @param bool $statisticsEnabled
     * @return RedirectManager
     */
    public function setStatisticsEnabled($statisticsEnabled)
    {
        $this->statisticsEnabled = (bool) $statisticsEnabled;
        return $this;
    }

    /**
     * @param string $basePath
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '/');
        return $this;
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Find a match based on given URL.
     *
     * @param string $requestPath
     * @param string $scheme 'http' or 'https'
     * @return RedirectRule|false
     */
    public function match($requestPath, $scheme)
    {
        $requestPath = urldecode($requestPath);

        // TODO: Validate $scheme

        $this->loadRedirectRules();

        foreach ($this->redirectRules as $rule) {
            if ($matchedRule = $this->matchesRule($rule, $requestPath, $scheme)) {
                return $matchedRule;
            }
        }

        return false;
    }

    /**
     * Redirect with specific rule.
     *
     * @param RedirectRule $rule
     * @param string $requestUri
     * @return void
     */
    public function redirectWithRule(RedirectRule $rule, $requestUri)
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
    private function redirectToPathOrUrl(RedirectRule $rule)
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
    private function redirectToCmsPage(RedirectRule $rule)
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
     * @return $this
     */
    public function setMatchDate(Carbon $matchDate)
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
    private function matchesRule(RedirectRule $rule, $requestPath, $scheme)
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
    private function matchExact(RedirectRule $rule, $url)
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
    private function matchPlaceholders(RedirectRule $rule, $url)
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
                $items[$placeholder] = $replacement === null ? $value : $replacement;
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
    private function matchesPeriod(RedirectRule $rule)
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
    private function matchesScheme(RedirectRule $rule, $scheme)
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
    private function findReplacementForPlaceholder(RedirectRule $rule, $placeholder)
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
    private function loadRedirectRules()
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
    private function updateStatistics($redirectId)
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
     * @param RedirectRule $rule
     * @param $requestUri
     * @param $toUrl
     * @return void
     */
    private function addLogEntry(RedirectRule $rule, $requestUri, $toUrl)
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

<?php
/**
 * OctoberCMS plugin: Adrenth.Redirect
 *
 * Copyright (c) Alwin Drenth 2017.
 *
 * Licensing information:
 * https://octobercms.com/help/license/regular
 * https://octobercms.com/help/license/extended
 * https://octobercms.com/help/license/faqs
 */

namespace Adrenth\Redirect\Classes;

use Adrenth\Redirect\Classes\Exceptions\RulesPathNotReadable;
use Adrenth\Redirect\Models\Settings;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Log;

/**
 * Class RedirectMiddleware
 *
 * @package Adrenth\Redirect\Classes
 */
class RedirectMiddleware
{
    /**
     * Run the request filter.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Only handle specific request methods
        if (!in_array($request->method(), ['GET', 'POST', 'HEAD'], true)) {
            return $next($request);
        }

        // Create the redirect manager if redirect rules are readable.
        try {
            $manager = RedirectManager::createWithDefaultRulesPath();
        } catch (RulesPathNotReadable $e) {
            return $next($request);
        }

        $manager->setLoggingEnabled(Settings::isLoggingEnabled())
            ->setStatisticsEnabled(Settings::isStatisticsEnabled());

        if ($request->header('X-Adrenth-Redirect') === 'Tester') {
            $manager->setStatisticsEnabled(false)
                ->setLoggingEnabled(false);
        }

        $rule = false;

        $requestUri = str_replace($request->getBasePath(), '', $request->getRequestUri());

        try {
            if (CacheManager::cachingEnabledAndSupported()) {
                $rule = $manager->matchCached($requestUri, $request->getScheme());
            } else {
                $rule = $manager->match($requestUri, $request->getScheme());
            }
        } catch (Exception $e) {
            Log::error("Could not perform redirect for $requestUri: " . $e->getMessage());
        }

        if ($rule) {
            $manager->redirectWithRule($rule, $requestUri);
        }

        return $next($request);
    }
}

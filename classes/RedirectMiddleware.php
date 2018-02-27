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

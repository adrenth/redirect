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

namespace Adrenth\Redirect\Models;

use October\Rain\Database\Model;
use System\Behaviors\SettingsModel;

/**
 * Class Settings
 *
 * @property array implement
 * @package Adrenth\Redirect\Models
 * @mixin SettingsModel
 */
class Settings extends Model
{
    /**
     * The settings code which to save the settings under.
     *
     * @var string
     */
    public $settingsCode = 'adrenth_redirect_settings';

    /**
     * Form fields definition file.
     *
     * @var string
     */
    public $settingsFields = 'fields.yaml';

    /**
     * {@inheritdoc}
     */
    public function __construct(array $attributes = [])
    {
        $this->implement = ['System.Behaviors.SettingsModel'];

        parent::__construct($attributes);
    }

    /**
     * Whether logging is enabled.
     *
     * @return bool
     */
    public static function isLoggingEnabled(): bool
    {
        // Please properly document your API/code OctoberCMS!
        /** @noinspection DynamicInvocationViaScopeResolutionInspection */
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return (bool) self::get('logging_enabled', true);
    }

    /**
     * Whether gathering of statistics are enabled.
     *
     * @return bool
     */
    public static function isStatisticsEnabled(): bool
    {
        // Please properly document your API/code OctoberCMS!
        /** @noinspection DynamicInvocationViaScopeResolutionInspection */
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return (bool) self::get('statistics_enabled', true);
    }

    /**
     * Whether the Test Lab functionality is enabled.
     *
     * @return bool
     */
    public static function isTestLabEnabled(): bool
    {
        // Please properly document your API/code OctoberCMS!
        /** @noinspection DynamicInvocationViaScopeResolutionInspection */
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return (bool) self::get('test_lab_enabled', true);
    }

    /**
     * Whether redirect caching is enabled.
     *
     * @return bool
     */
    public static function isCachingEnabled(): bool
    {
        // Please properly document your API/code OctoberCMS!
        /** @noinspection DynamicInvocationViaScopeResolutionInspection */
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return (bool) self::get('caching_enabled', false);
    }
}

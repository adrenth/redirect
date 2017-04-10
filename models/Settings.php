<?php

namespace Adrenth\Redirect\Models;

use October\Rain\Database\Model;
use System\Behaviors\SettingsModel;

/**
 * Class Settings
 *
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
    public static function isLoggingEnabled()
    {
        /** @noinspection DynamicInvocationViaScopeResolutionInspection */
        return (bool) self::get('logging_enabled', true);
    }

    /**
     * Whether gathering of statistics are enabled.
     *
     * @return bool
     */
    public static function isStatisticsEnabled()
    {
        /** @noinspection DynamicInvocationViaScopeResolutionInspection */
        return (bool) self::get('statistics_enabled', true);
    }

    /**
     * Whether the Test Lab functionality is enabled.
     *
     * @return bool
     */
    public static function isTestLabEnabled()
    {
        /** @noinspection DynamicInvocationViaScopeResolutionInspection */
        return (bool) self::get('test_lab_enabled', true);
    }

    /**
     * Whether redirect caching is enabled.
     *
     * @return bool
     */
    public static function isCachingEnabled()
    {
        /** @noinspection DynamicInvocationViaScopeResolutionInspection */
        return (bool) self::get('caching_enabled', false);
    }
}

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
    /** @var string */
    public $settingsCode = 'adrenth_redirect_settings';

    /** @var string */
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
     * @return bool
     */
    public static function isLoggingEnabled()
    {
        /** @noinspection DynamicInvocationViaScopeResolutionInspection */
        return (bool) self::get('logging_enabled', true);
    }

    /**
     * @return bool
     */
    public static function isStatisticsEnabled()
    {
        /** @noinspection DynamicInvocationViaScopeResolutionInspection */
        return (bool) self::get('statistics_enabled', true);
    }

    /**
     * @return bool
     */
    public static function isTestLabEnabled()
    {
        /** @noinspection DynamicInvocationViaScopeResolutionInspection */
        return (bool) self::get('test_lab_enabled', true);
    }
}

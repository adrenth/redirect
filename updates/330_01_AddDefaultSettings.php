<?php

namespace Adrenth\Redirect\Updates;

use Adrenth\Redirect\Models\Settings;
use October\Rain\Database\Updates\Migration;

/**
 * Class AddTestLabPathToRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class AddDefaultSettings extends Migration
{
    public function up()
    {
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $settings = Settings::instance();
        $settings->logging_enabled = '1';
        $settings->statistics_enabled = '1';
        $settings->test_lab_enabled = '1';
        $settings->save();
    }

    public function down()
    {
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $settings = Settings::instance();
        $settings->resetDefault();
    }
}

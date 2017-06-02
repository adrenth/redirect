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

declare(strict_types=1);

namespace Adrenth\Redirect\Updates;

use Adrenth\Redirect\Models\Settings;
use October\Rain\Database\Updates\Migration;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class AddTestLabPathToRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class AddDefaultSettings extends Migration
{
    public function up(): void
    {
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $settings = Settings::instance();
        $settings->logging_enabled = '1';
        $settings->statistics_enabled = '1';
        $settings->test_lab_enabled = '1';
        $settings->save();
    }

    public function down(): void
    {
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $settings = Settings::instance();
        $settings->resetDefault();
    }
}

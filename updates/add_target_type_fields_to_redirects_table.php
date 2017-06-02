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

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class AddTargetTypeColumnsToRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class AddTargetTypeColumnsToRedirectsTable extends Migration
{
    public function up(): void
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->string('target_type', 12)
                ->default('path_or_url')
                ->after('match_type');
            $table->string('cms_page')
                ->nullable()
                ->after('test_url');
            $table->string('static_page')
                ->nullable()
                ->after('cms_page');
        });
    }

    public function down(): void
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->dropColumn(['cms_page', 'static_page']);
        });
    }
}

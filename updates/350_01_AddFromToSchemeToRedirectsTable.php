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

use Adrenth\Redirect\Models\Redirect;
use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class AddFromToSchemeToRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class AddFromToSchemeToRedirectsTable extends Migration
{
    public function up(): void
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->string('from_scheme', 5)
                ->default(Redirect::SCHEME_AUTO)
                ->after('target_type');

            $table->string('to_scheme', 5)
                ->default(Redirect::SCHEME_AUTO)
                ->after('from_url');
        });
    }

    public function down(): void
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->dropColumn(['from_scheme', 'to_scheme']);
        });
    }
}

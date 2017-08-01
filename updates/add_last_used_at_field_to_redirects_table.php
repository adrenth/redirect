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
 * Class AddLastUsedAtFieldToRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class AddLastUsedAtFieldToRedirectsTable extends Migration
{
    public function up()//: void
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->timestamp('last_used_at')
                ->nullable()
                ->after('system');
        });
    }

    public function down()//: void
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->dropColumn('last_used_at');
        });
    }
}

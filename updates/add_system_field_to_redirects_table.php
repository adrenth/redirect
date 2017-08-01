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

namespace Adrenth\Redirect\Updates;

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class AddSystemFieldToRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class AddSystemFieldToRedirectsTable extends Migration
{
    public function up()//: void
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->boolean('system')
                ->default(false)
                ->after('publish_status');
        });
    }

    public function down()//: void
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->dropColumn('system');
        });
    }
}

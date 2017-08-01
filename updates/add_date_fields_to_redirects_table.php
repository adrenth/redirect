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
 * Class AddDateFieldsToRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class AddDateFieldsToRedirectsTable extends Migration
{
    public function up()//: void
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->date('from_date')
                ->nullable()
                ->after('hits');
            $table->date('to_date')
                ->nullable()
                ->after('from_date');
        });
    }

    public function down()//: void
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->dropColumn(['from_date', 'to_date']);
        });
    }
}

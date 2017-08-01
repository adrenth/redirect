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
 * Class ChangeTypeUrlColumnsRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class ChangeTypeUrlColumnsRedirectsTable extends Migration
{
    public function up()//: void
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->mediumText('to_url')
                ->change();
            $table->mediumText('from_url')
                ->change();
            $table->string('match_type', 12)
                ->change();
        });
    }

    public function down()//: void
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->string('to_url')
                ->change();
            $table->string('from_url')
                ->change();
        });
    }
}

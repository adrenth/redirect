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

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class CreateClientsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class CreateClientsTable extends Migration
{
    public function up()//: void
    {
        Schema::create('adrenth_redirect_clients', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('redirect_id');
            $table->timestamp('timestamp')
                ->nullable();
            $table->unsignedTinyInteger('day');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->string('crawler')
                ->nullable();

            $table->index(['redirect_id', 'day', 'month', 'year'], 'redirect_dmy');
            $table->index(['redirect_id', 'month', 'year'], 'redirect_my');

            $table->foreign('redirect_id', 'redirect_client')
                ->references('id')
                ->on('adrenth_redirect_redirects')
                ->onDelete('cascade');
        });
    }

    public function down()//: void
    {
        Schema::table('adrenth_redirect_clients', function (Blueprint $table) {
            $table->dropForeign('redirect_client');
        });

        Schema::dropIfExists('adrenth_redirect_clients');
    }
}

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

use DB;
use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class ChangeToUrlToUrlColumnRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class ChangeToUrlToUrlColumnRedirectsTable extends Migration
{
    public function up()//: void
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->mediumText('to_url')
                ->nullable()
                ->change();
        });
    }

    public function down()//: void
    {
        // Fixes exception when refreshing plugin on PostgreSQL:
        // Doctrine\DBAL\DBALException: Unknown database type json requested,
        // Doctrine\DBAL\Platforms\PostgreSqlPlatform may not support it.
        DB::getDoctrineSchemaManager()
            ->getDatabasePlatform()
            ->registerDoctrineTypeMapping('json', 'text');

        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->mediumText('to_url')
                ->change();
        });
    }
}

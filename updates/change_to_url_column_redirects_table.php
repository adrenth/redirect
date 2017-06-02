<?php

namespace Adrenth\Redirect\Updates;

use DB;
use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * Class ChangeToUrlToUrlColumnRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class ChangeToUrlToUrlColumnRedirectsTable extends Migration
{
    public function up()
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->mediumText('to_url')
                ->nullable()
                ->change();
        });
    }

    public function down()
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

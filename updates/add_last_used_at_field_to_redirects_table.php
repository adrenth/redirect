<?php

namespace Adrenth\Redirect\Updates;

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * Class AddLastUsedAtFieldToRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class AddLastUsedAtFieldToRedirectsTable extends Migration
{
    public function up()
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->timestamp('last_used_at')
                ->nullable()
                ->after('system');
        });
    }

    public function down()
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->dropColumn('last_used_at');
        });
    }
}

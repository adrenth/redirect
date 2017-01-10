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
    const TABLE = 'adrenth_redirect_redirects';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->timestamp('last_used_at')->nullable()->after('system');
        });
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('last_used_at');
        });
    }
}

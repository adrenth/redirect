<?php

namespace Adrenth\Redirect\Updates;

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * Class AddMonthYearIndexToClientsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class AddMonthYearIndexToClientsTable extends Migration
{
    public function up()
    {
        Schema::table('adrenth_redirect_clients', function (Blueprint $table) {
            $table->index(['month', 'year'], 'month_year');
        });
    }

    public function down()
    {
        Schema::table('adrenth_redirect_clients', function (Blueprint $table) {
            $table->dropIndex('month_year');
        });
    }
}

<?php

namespace Adrenth\Redirect\Updates;

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * Class AddDateFieldsToRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class AddDateFieldsToRedirectsTable extends Migration
{
    public function up()
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

    public function down()
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->dropColumn(['from_date', 'to_date']);
        });
    }
}

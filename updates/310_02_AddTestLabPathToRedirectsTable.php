<?php

namespace Adrenth\Redirect\Updates;

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * Class AddTestLabPathToRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class AddTestLabPathToRedirectsTable extends Migration
{
    public function up()
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->string('test_lab_path')
                ->after('test_lab')
                ->nullable();
        });
    }

    public function down()
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->dropColumn('test_lab_path');
        });
    }
}

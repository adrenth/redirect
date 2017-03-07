<?php

namespace Adrenth\Redirect\Updates;

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * Class AddTestLabToRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class AddTestLabToRedirectsTable extends Migration
{
    public function up()
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->boolean('test_lab')
                ->after('is_enabled')
                ->default(false);
        });
    }

    public function down()
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->dropColumn('test_lab');
        });
    }
}

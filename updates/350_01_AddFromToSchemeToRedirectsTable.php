<?php

namespace Adrenth\Redirect\Updates;

use Adrenth\Redirect\Models\Redirect;
use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * Class AddFromToSchemeToRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class AddFromToSchemeToRedirectsTable extends Migration
{
    public function up()
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->char('from_scheme', 5)
                ->default(Redirect::SCHEME_AUTO)
                ->after('target_type');

            $table->char('to_scheme', 5)
                ->default(Redirect::SCHEME_AUTO)
                ->after('from_url');
        });
    }

    public function down()
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->dropColumn(['from_scheme', 'to_scheme']);
        });
    }
}

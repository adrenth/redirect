<?php

namespace Adrenth\Redirect\Updates;

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * Class RemovePublishStatusColumnRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class RemovePublishStatusColumnRedirectsTable extends Migration
{
    public function up()
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->dropColumn('publish_status');
        });
    }

    public function down()
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->boolean('publish_status')
                ->default(false);
        });
    }
}

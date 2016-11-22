<?php

namespace Adrenth\Redirect\Updates;

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * Class AddCategoryIdToRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class AddCategoryIdToRedirectsTable extends Migration
{
    public function up()
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->unsignedInteger('category_id')->after('id')->nullable();

            $table->foreign('category_id')
                ->references('id')
                ->on('adrenth_redirect_categories')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->dropForeign('adrenth_redirect_redirects_category_id_foreign');
            $table->dropColumn('category_id');
        });
    }
}

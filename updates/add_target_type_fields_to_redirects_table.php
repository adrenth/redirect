<?php

namespace Adrenth\Redirect\Updates;

use DB;
use Illuminate\Database\Schema\Blueprint;
use Schema;
use October\Rain\Database\Updates\Migration;

/**
 * Class AddTargetTypeColumnsToRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class AddTargetTypeColumnsToRedirectsTable extends Migration
{
    const TABLE = 'adrenth_redirect_redirects';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->char('target_type', 12)->default('path_or_url')->after('match_type');
            $table->string('cms_page')->nullable()->after('test_url');
            $table->string('static_page')->nullable()->after('cms_page');
        });
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('target_type');
            $table->dropColumn('cms_page');
            $table->dropColumn('static_page');
        });
    }
}

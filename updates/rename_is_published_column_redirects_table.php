<?php

namespace Adrenth\Redirect\Updates;

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * Class RenameIsPublishedColumnRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class RenameIsPublishedColumnRedirectsTable extends Migration
{
    const TABLE = 'adrenth_redirect_redirects';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->renameColumn('is_published', 'publish_status');
        });
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->renameColumn('publish_status', 'is_published');
        });
    }
}

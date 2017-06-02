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
    public function up()
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->renameColumn('is_published', 'publish_status');
        });
    }

    public function down()
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->renameColumn('publish_status', 'is_published');
        });
    }
}

<?php
/**
 * OctoberCMS plugin: Adrenth.Redirect
 *
 * Copyright (c) Alwin Drenth 2017.
 *
 * Licensing information:
 * https://octobercms.com/help/license/regular
 * https://octobercms.com/help/license/extended
 * https://octobercms.com/help/license/faqs
 */

declare(strict_types=1);

namespace Adrenth\Redirect\Updates;

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class AddCategoryIdToRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class AddCategoryIdToRedirectsTable extends Migration
{
    public function up(): void
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->unsignedInteger('category_id')
                ->after('id')
                ->nullable();

            $table->foreign('category_id')
                ->references('id')
                ->on('adrenth_redirect_categories')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->dropForeign('adrenth_redirect_redirects_category_id_foreign');
            $table->dropColumn('category_id');
        });
    }
}

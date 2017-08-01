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

use Adrenth\Redirect\Models\Category;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class CreateCategoriesTable
 *
 * @package Adrenth\Redirect\Updates
 */
class CreateCategoriesTable extends Migration
{
    public function up()//: void
    {
        Schema::create('adrenth_redirect_categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Category::create(['name' => 'General']);
    }

    public function down()//: void
    {
        Schema::dropIfExists('adrenth_redirect_categories');
    }
}

<?php

namespace Adrenth\Redirect\Updates;

use Adrenth\Redirect\Models\Category;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * Class CreateCategoriesTable
 *
 * @package Adrenth\Redirect\Updates
 */
class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('adrenth_redirect_categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Category::create(['name' => 'General']);
    }

    public function down()
    {
        Schema::dropIfExists('adrenth_redirect_categories');
    }
}

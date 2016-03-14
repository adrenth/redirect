<?php

namespace Adrenth\Redirect\Updates;

use Illuminate\Database\Schema\Blueprint;
use Schema;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreateRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class CreateRedirectsTable extends Migration
{
    public function up()
    {
        Schema::create('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->enum('match_type', ['exact', 'placeholders']);
            $table->string('from_url');
            $table->string('to_url');
            $table->json('requirements')->nullable();
            $table->char('status_code', 3);
            $table->integer('hits')->default(0)->unsigned();
            $table->integer('sort_order')->default(0)->unsigned()->index();
            $table->boolean('is_enabled')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('adrenth_redirect_redirects');
    }
}

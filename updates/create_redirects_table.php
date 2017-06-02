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
 * Class CreateRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class CreateRedirectsTable extends Migration
{
    public function up(): void
    {
        Schema::create('adrenth_redirect_redirects', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('match_type', '12');
            $table->string('from_url');
            $table->string('to_url');
            $table->json('requirements')
                ->nullable();
            $table->char('status_code', 3);
            $table->integer('hits')
                ->default(0)
                ->unsigned();
            $table->integer('sort_order')
                ->default(0)
                ->unsigned()
                ->index();
            $table->boolean('is_enabled')
                ->default(false)
                ->index();
            $table->boolean('is_published')
                ->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adrenth_redirect_redirects');
    }
}

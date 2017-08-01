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

use October\Rain\Database\Updates\Migration;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class Publish
 *
 * @package Adrenth\Redirect\Updates
 */
class Publish extends Migration
{
    public function up()//: void
    {
        // See commit: 249c783fe73c602549f4e9d69789844341b9b5b2 (1.1.0)
        // This migration was removed, but fails when uninstalling plugin
    }

    public function down()//: void
    {
        // See commit: 249c783fe73c602549f4e9d69789844341b9b5b2 (1.1.0)
        // This migration was removed, but fails when uninstalling plugin
    }
}

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

use Adrenth\Redirect\Classes\PublishManager;
use Exception;
use October\Rain\Database\Updates\Migration;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class PublishRedirectsToPreventCsvReaderIssues
 *
 * @package Adrenth\Redirect\Updates
 */
class PublishRedirectsToPreventCsvReaderIssues extends Migration
{
    public function up()//: void
    {
        try {
            PublishManager::instance()->publish();
        } catch (Exception $e) {
            // ..
        }
    }

    public function down()//: void
    {
    }
}

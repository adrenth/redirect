<?php

namespace Adrenth\Redirect\Updates;

use Adrenth\Redirect\Classes\PublishManager;
use Exception;
use October\Rain\Database\Updates\Migration;

/**
 * Class PublishRedirectsToPreventCsvReaderIssues
 *
 * @package Adrenth\Redirect\Updates
 */
class PublishRedirectsToPreventCsvReaderIssues extends Migration
{
    public function up()
    {
        try {
            PublishManager::instance()->publish();
        } catch (Exception $e) {
            // ..
        }
    }

    public function down()
    {
    }
}

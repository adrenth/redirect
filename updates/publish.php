<?php

namespace Adrenth\Redirect\Updates;

use Adrenth\Redirect\Classes\PublishManager;
use October\Rain\Database\Updates\Migration;

/**
 * Class Publish
 *
 * @package Adrenth\Redirect\Updates
 */
class Publish extends Migration
{
    public function up()
    {
        $manager = new PublishManager();
        $manager->publish();
    }

    public function down()
    {
        $manager = new PublishManager();
        $manager->publish();
    }
}

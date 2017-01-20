<?php

namespace Adrenth\Redirect\Tests;

use Adrenth\Redirect\Classes\OptionHelper;
use Adrenth\Redirect\Models\Redirect;
use PluginTestCase;

/**
 * Class OptionHelperTest
 *
 * @package Adrenth\Redirect\Tests
 */
class OptionHelperTest extends PluginTestCase
{
    public function testTargetTypeOptions()
    {
        self::assertNotCount(0, OptionHelper::getTargetTypeOptions());
        self::assertArrayHasKey(Redirect::TARGET_TYPE_PATH_URL, OptionHelper::getTargetTypeOptions());
        self::assertArrayHasKey(Redirect::TARGET_TYPE_CMS_PAGE, OptionHelper::getTargetTypeOptions());
    }
}

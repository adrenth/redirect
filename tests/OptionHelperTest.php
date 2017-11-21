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
        self::assertCount(1, OptionHelper::getTargetTypeOptions(404));
        self::assertCount(3, OptionHelper::getTargetTypeOptions(301));

        self::assertArrayHasKey(Redirect::TARGET_TYPE_NONE, OptionHelper::getTargetTypeOptions(404));
        self::assertArrayHasKey(Redirect::TARGET_TYPE_NONE, OptionHelper::getTargetTypeOptions(410));

        self::assertArrayHasKey(Redirect::TARGET_TYPE_PATH_URL, OptionHelper::getTargetTypeOptions(301));
        self::assertArrayHasKey(Redirect::TARGET_TYPE_CMS_PAGE, OptionHelper::getTargetTypeOptions(301));
        self::assertArrayHasKey(Redirect::TARGET_TYPE_STATIC_PAGE, OptionHelper::getTargetTypeOptions(301));

        self::assertArrayHasKey(Redirect::TARGET_TYPE_PATH_URL, OptionHelper::getTargetTypeOptions(302));
        self::assertArrayHasKey(Redirect::TARGET_TYPE_CMS_PAGE, OptionHelper::getTargetTypeOptions(302));
        self::assertArrayHasKey(Redirect::TARGET_TYPE_STATIC_PAGE, OptionHelper::getTargetTypeOptions(302));

        self::assertArrayHasKey(Redirect::TARGET_TYPE_PATH_URL, OptionHelper::getTargetTypeOptions(303));
        self::assertArrayHasKey(Redirect::TARGET_TYPE_CMS_PAGE, OptionHelper::getTargetTypeOptions(303));
        self::assertArrayHasKey(Redirect::TARGET_TYPE_STATIC_PAGE, OptionHelper::getTargetTypeOptions(303));
    }
}

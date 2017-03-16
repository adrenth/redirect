<?php

namespace Adrenth\Redirect\Tests;

use Adrenth\Redirect\Classes\RedirectRule;
use Adrenth\Redirect\Models\Redirect;
use Carbon\Carbon;
use PluginTestCase;

/**
 * Class RedirectRuleTest
 *
 * @package Adrenth\Redirect\Tests
 */
class RedirectRuleTest extends PluginTestCase
{
    public function testInstance()
    {
        $rule = new RedirectRule([
            'id' => 1,
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_scheme' => Redirect::SCHEME_AUTO,
            'from_url' => '/from/url',
            'to_url' => '/to/url',
            'to_scheme' => Redirect::SCHEME_HTTPS,
            'status_code' => 301,
            'from_date' => Carbon::today(),
            'to_date' => Carbon::tomorrow(),
        ]);

        self::assertEquals(1, $rule->getId());
        self::assertEquals(Redirect::TYPE_EXACT, $rule->getMatchType());
        self::assertEquals(Redirect::TARGET_TYPE_PATH_URL, $rule->getTargetType());
        self::assertEquals('/from/url', $rule->getFromUrl());
        self::assertEquals('/to/url', $rule->getToUrl());
        self::assertEquals(301, $rule->getStatusCode());
        self::assertEquals(Carbon::today(), $rule->getFromDate());
        self::assertEquals(Carbon::tomorrow(), $rule->getToDate());
        self::assertEquals(Redirect::SCHEME_AUTO, $rule->getFromScheme());
        self::assertEquals(Redirect::SCHEME_HTTPS, $rule->getToScheme());
    }

    public function testModel()
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_scheme' => Redirect::SCHEME_AUTO,
            'from_url' => '/this-should-be-source',
            'to_url' => '/this-should-be-target',
            'to_scheme' => Redirect::SCHEME_AUTO,
            'requirements' => null,
            'status_code' => 302,
        ]);

        $rule = RedirectRule::createWithModel($redirect);

        self::assertInstanceOf(RedirectRule::class, $rule);
        self::assertEquals(Redirect::TYPE_EXACT, $rule->getMatchType());
        self::assertEquals(Redirect::TARGET_TYPE_PATH_URL, $rule->getTargetType());
        self::assertEquals('/this-should-be-source', $rule->getFromUrl());
        self::assertEquals('/this-should-be-target', $rule->getToUrl());
        self::assertEquals(302, $rule->getStatusCode());
        self::assertEquals(Redirect::SCHEME_AUTO, $rule->getToScheme());
        self::assertEquals(Redirect::SCHEME_AUTO, $rule->getFromScheme());
    }
}

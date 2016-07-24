<?php

namespace Adrenth\Redirect\Tests;

use Adrenth\Redirect\Classes\RedirectManager;
use Adrenth\Redirect\Classes\RedirectRule;
use Adrenth\Redirect\Models\Redirect;
use Carbon\Carbon;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use PluginTestCase;

/**
 * Class RedirectManagerTest
 *
 * @package Adrenth\Redirect\Tests
 */
class RedirectManagerTest extends PluginTestCase
{
    public function testExactRedirect()
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_url' => '/this-should-be-source',
            'to_url' => '/this-should-be-target',
            'requirements' => null,
            'status_code' => 302,
        ]);

        self::assertTrue($redirect->save());

        $rule = RedirectRule::createWithModel($redirect);
        self::assertInstanceOf(RedirectRule::class, $rule);

        $manager = RedirectManager::createWithRule($rule);
        self::assertInstanceOf(RedirectManager::class, $manager);

        $test = '/this-should-be-source';
        $result = $manager->match($test);

        self::assertInstanceOf(RedirectRule::class, $result);
        self::assertEquals('/this-should-be-target', $manager->getLocation($result));

        $test = '/this-is-something-totally-different';
        self::assertEquals(false, $manager->match($test));
    }

    public function testPlaceholderRedirect()
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_PLACEHOLDERS,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_url' => '/blog.php?cat={category}&section={section}&id={id}',
            'to_url' => '/blog/{category}/{section}/{id}',
            'requirements' => [
                [
                    'placeholder' => '{category}',
                    'requirement' => '(octobercms|wordpress|drupal)',
                    'replacement' => null,
                ],
                [
                    'placeholder' => '{section}',
                    'requirement' => '[a-z]+',
                    'replacement' => null,
                ],
                [
                    'placeholder' => '{id}',
                    'requirement' => '[0-9]{2}',
                    'replacement' => null,
                ],
            ],
            'status_code' => 301,
        ]);

        self::assertTrue($redirect->save());

        $rule = RedirectRule::createWithModel($redirect);
        self::assertInstanceOf(RedirectRule::class, $rule);

        $manager = RedirectManager::createWithRule($rule);
        self::assertInstanceOf(RedirectManager::class, $manager);

        $test = '/blog.php?cat=octobercms&section=test&id=1337';
        self::assertFalse($manager->match($test));

        $test = '/blog.php?cat=octobercms&section=test&id=13';
        $result = $manager->match($test);
        self::assertInstanceOf(RedirectRule::class, $result);
        self::assertEquals('/blog/octobercms/test/13', $manager->getLocation($result));

        $test = '/blog.php?cat=wordpress&section=test&id=99';
        $result = $manager->match($test);
        self::assertInstanceOf(RedirectRule::class, $result);
        self::assertEquals('/blog/wordpress/test/99', $manager->getLocation($result));

        $test = '/blog.php?cat=joomla&section=test&id=99';
        self::assertFalse($manager->match($test));

        $test = '/blog.php?cat=drupal&section=test&id=e9';
        self::assertFalse($manager->match($test));
    }

    public function testTargetCmsPageRedirect()
    {
        $page = Page::load(Theme::getActiveTheme(), 'adrenth-redirect-testpage');

        if ($page === null) {
            $page = new Page();
            $page->title = 'Testpage';
            $page->url = '/adrenth/redirect/testpage';
            $page->setFileNameAttribute('adrenth-redirect-testpage');
            $page->save();
        }

        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_CMS_PAGE,
            'from_url' => '/this-should-be-source',
            'cms_page' => 'adrenth-redirect-testpage',
            'requirements' => null,
            'status_code' => 302,
            'from_date' => Carbon::now(),
            'to_date' => Carbon::now()->addWeek(),
        ]);

        self::assertTrue($redirect->save());

        $rule = RedirectRule::createWithModel($redirect);
        self::assertInstanceOf(RedirectRule::class, $rule);

        $manager = RedirectManager::createWithRule($rule);
        self::assertInstanceOf(RedirectManager::class, $manager);

        $result = $manager->match('/this-should-be-source');

        self::assertInstanceOf(RedirectRule::class, $result);
        self::assertEquals('http://localhost/adrenth/redirect/testpage', $manager->getLocation($result));

        self::assertTrue($page->delete());
    }

    public function testScheduledRedirectPeriod()
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_url' => '/this-should-be-source',
            'to_url' => '/this-should-be-target',
            'requirements' => null,
            'status_code' => 302,
            'from_date' => Carbon::now(),
            'to_date' => Carbon::now()->addWeek(),
        ]);

        self::assertTrue($redirect->save());

        $rule = RedirectRule::createWithModel($redirect);
        self::assertInstanceOf(RedirectRule::class, $rule);

        $manager = RedirectManager::createWithRule($rule);
        self::assertInstanceOf(RedirectManager::class, $manager);

        // Test date between `from_date` and `end_date`
        self::assertInstanceOf(
            RedirectRule::class,
            $manager->setMatchDate(Carbon::now()->addDay(2))
                ->match('/this-should-be-source')
        );

        // Test date equals `from_date`
        self::assertInstanceOf(
            RedirectRule::class,
            $manager->setMatchDate(Carbon::now())
                ->match('/this-should-be-source')
        );

        // Test date equals `to_date`
        self::assertInstanceOf(
            RedirectRule::class,
            $manager->setMatchDate(Carbon::now()->addWeek())
                ->match('/this-should-be-source')
        );

        // Test date greater than `to_date`
        self::assertFalse(
            $manager->setMatchDate(Carbon::now()->addWeek()->addDay())
                ->match('/this-should-be-source')
        );

        // Test date less than `from_date`
        self::assertFalse(
            $manager->setMatchDate(Carbon::now()->subDay())
                ->match('/this-should-be-source')
        );
    }

    public function testScheduledRedirectOnlyFromDate()
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_url' => '/this-should-be-source',
            'to_url' => '/this-should-be-target',
            'requirements' => null,
            'status_code' => 302,
            'is_enabled' => 1,
            'from_date' => Carbon::today()->addMonth(),
            'to_date' => null,
        ]);

        self::assertTrue($redirect->save());

        $rule = RedirectRule::createWithModel($redirect);
        self::assertInstanceOf(RedirectRule::class, $rule);

        $manager = RedirectManager::createWithRule($rule);
        self::assertInstanceOf(RedirectManager::class, $manager);

        // Test date equals `from_date`
        self::assertInstanceOf(
            RedirectRule::class,
            $manager->setMatchDate(Carbon::today()->addMonth())
                ->match('/this-should-be-source')
        );

        // Test date greater than `from_date`
        self::assertInstanceOf(
            RedirectRule::class,
            $manager->setMatchDate(Carbon::today()->addMonth()->addDay())
                ->match('/this-should-be-source')
        );

        // Test date less than `from_date`
        self::assertFalse(
            $manager->setMatchDate(Carbon::today()->addMonth()->subDay())
                ->match('/this-should-be-source')
        );
    }

    public function testScheduledRedirectOnlyToDate()
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_url' => '/this-should-be-source',
            'to_url' => '/this-should-be-target',
            'requirements' => null,
            'status_code' => 302,
            'is_enabled' => 1,
            'from_date' => null,
            'to_date' => Carbon::today()->addMonth(),
        ]);

        self::assertTrue($redirect->save());

        $rule = RedirectRule::createWithModel($redirect);
        self::assertInstanceOf(RedirectRule::class, $rule);

        $manager = RedirectManager::createWithRule($rule);
        self::assertInstanceOf(RedirectManager::class, $manager);

        // Test date equals `to_date`
        self::assertInstanceOf(
            RedirectRule::class,
            $manager->setMatchDate(Carbon::today()->addMonth())
                ->match('/this-should-be-source')
        );

        // Test date less than `to_date`
        self::assertInstanceOf(
            RedirectRule::class,
            $manager->setMatchDate(Carbon::today()->addMonth()->subDay())
                ->match('/this-should-be-source')
        );

        // Test date greater than `to_date`
        self::assertFalse(
            $manager->setMatchDate(Carbon::today()->addMonth()->addDay())
                ->match('/this-should-be-source')
        );
    }

    public function testRelativeRedirect()
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_url' => '/this-should-be-source',
            'to_url' => 'relative/path/to',
            'requirements' => null,
            'status_code' => 302,
            'is_enabled' => 1,
            'from_date' => null,
            'to_date' => null,
        ]);

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        self::assertEquals('/relative/path/to', $manager->getLocation($rule));

        $manager->setBasePath('/subdirectory');

        self::assertEquals('/subdirectory/relative/path/to', $manager->getLocation($rule));

        $manager->setBasePath('/subdirectory/sub/sub//');

        self::assertEquals('/subdirectory/sub/sub', $manager->getBasePath());
        self::assertEquals('/subdirectory/sub/sub/relative/path/to', $manager->getLocation($rule));
    }

    public function testAbsoluteRedirect()
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_url' => '/this-should-be-source',
            'to_url' => '/absolute/path/to',
            'requirements' => null,
            'status_code' => 302,
            'is_enabled' => 1,
            'from_date' => null,
            'to_date' => null,
        ]);

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        self::assertEquals('/absolute/path/to', $manager->getLocation($rule));

        $manager->setBasePath('/subdirectory');

        self::assertEquals('/absolute/path/to', $manager->getLocation($rule));

        $manager->setBasePath('/subdirectory/sub/sub');

        self::assertEquals('/absolute/path/to', $manager->getLocation($rule));
    }
}

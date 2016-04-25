<?php

namespace Adrenth\Redirect\Tests;

use Adrenth\Redirect\Classes\RedirectManager;
use Adrenth\Redirect\Classes\RedirectRule;
use Adrenth\Redirect\Models\Redirect;
use Carbon\Carbon;
use PluginTestCase;

class RedirectManagerTest extends PluginTestCase
{
    public function testPlaceholderRedirect()
    {
        $redirect = new Redirect([
            'match_type' => 'placeholders',
            'target_type' => 'path_or_url',
            'from_url' => '/blog.php?cat={category}&section={section}&id={id}',
            'to_url' => '/blog/{category}/{section}/{id}',
            'requirements' => [
                [
                    'placeholder' => '',
                    'requirement' => '(octobercms|wordpress|drupal)',
                    'replacement' => '',
                ]
            ],
            'status_code' => 301,
            'is_enabled' => 1,
            'publish_status' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $rule = RedirectRule::createWithModel($redirect);

        $manager = RedirectManager::createWithRule($rule);

    }
}

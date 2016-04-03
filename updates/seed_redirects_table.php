<?php

namespace Adrenth\Redirect\Updates;

use October\Rain\Database\Updates\Seeder;
use Adrenth\Redirect\Models\Redirect;

/**
 * Class SeedRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class SeedRedirectsTable extends Seeder
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        /*
        Redirect::create([
            'match_type' => Redirect::TYPE_EXACT,
            'from_url' => '/example/source/path',
            'to_url' => '/example/target/path',
            'test_url' => '/example/source/path',
            'sort_order' => 99990,
            'is_enabled' => false,
            'is_published' => false,
            'status_code' => 301,
        ]);
        Redirect::create([
            'match_type' => Redirect::TYPE_PLACEHOLDERS,
            'from_url' => '/example/blog/{category}/{id}',
            'to_url' => '/example/{category}/id/{id}',
            'test_url' => '/example/blog/cat/1337',
            'sort_order' => 99991,
            'is_enabled' => false,
            'is_published' => false,
            'status_code' => 301,
            'requirements' => [
                1 => [
                    'placeholder' => '{category}',
                    'requirement' => '(cat|dog|mouse)',
                    'replacement' => 'animals',
                ],
                2 => [
                    'placeholder' => '{id}',
                    'requirement' => '[0-9]{1,4}',
                    'replacement' => null,
                ]
            ]
        ]);
        */
    }
}

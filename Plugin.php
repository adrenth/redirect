<?php

namespace Adrenth\Redirect;

use App;
use Adrenth\Redirect\Classes\RedirectManager;
use Backend;
use Request;
use System\Classes\PluginBase;

/**
 * Class Plugin
 *
 * @package Adrenth\Redirect
 */
class Plugin extends PluginBase
{
    /**
     * {@inheritdoc}
     */
    public function pluginDetails()
    {
        return [
            'name' => 'adrenth.redirect::lang.plugin.name',
            'description' => 'adrenth.redirect::lang.plugin.description',
            'author' => 'Alwin Drenth',
            'icon' => 'icon-link',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if (App::runningInBackend() || App::runningUnitTests()) {
            return;
        }

        // Check for running in console or backend before route matching
        $rulesPath = storage_path('app/redirects.csv');

        if (!file_exists($rulesPath) || !is_readable($rulesPath)) {
            return;
        }

        $manager = RedirectManager::createWithRulesPath($rulesPath);
        $rule = $manager->match(Request::getRequestUri());

        if ($rule) {
            $manager->redirectWithRule($rule);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function registerPermissions()
    {
        return [
            'adrenth.redirect.access_redirects' => [
                'label' => 'adrenth.redirect::lang.permission.access_redirects.label',
                'tab' => 'adrenth.redirect::lang.permission.access_redirects.tab',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerNavigation()
    {
        return [
            'redirect' => [
                'label' => 'adrenth.redirect::lang.navigation.menu_label',
                'icon' => 'icon-link',
                'url' => Backend::url('adrenth/redirect/redirects'),
                'order' => 50,
                'permissions' => [
                    'adrenth.redirect.access_redirects',
                ],
                'sideMenu' => [
                    'index' => [
                        'icon' => 'icon-link',
                        'label' => 'adrenth.redirect::lang.navigation.menu_label',
                        'url' => Backend::url('adrenth/redirect/redirects'),
                        'permissions' => [
                            'adrenth.redirect.access_redirects',
                        ],
                    ],
                    'reorder' => [
                        'label' => 'adrenth.redirect::lang.buttons.reorder_redirects',
                        'url' => Backend::url('adrenth/redirect/redirects/reorder'),
                        'icon' => 'icon-sort-amount-asc',
                        'permissions' => [
                            'adrenth.redirect.access_redirects',
                        ],
                    ],
                    'import' => [
                        'label' => 'adrenth.redirect::lang.buttons.import',
                        'url' => Backend::url('adrenth/redirect/redirects/import'),
                        'icon' => 'icon-download',
                        'permissions' => [
                            'adrenth.redirect.access_redirects',
                        ],
                    ],
                    'export' => [
                        'label' => 'adrenth.redirect::lang.buttons.export',
                        'url' => Backend::url('adrenth/redirect/redirects/export'),
                        'icon' => 'icon-upload',
                        'permissions' => [
                            'adrenth.redirect.access_redirects',
                        ],
                    ],
                ]
            ],
        ];
    }
}

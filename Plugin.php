<?php

namespace Adrenth\Redirect;

use Adrenth\Redirect\Classes\PageHandler;
use Adrenth\Redirect\Classes\PublishManager;
use Adrenth\Redirect\Classes\RedirectManager;
use App;
use Backend;
use Cms\Classes\Page;
use Event;
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
            'homepage' => 'https://github.com/adrenth/redirect',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if (App::runningInBackend()
            && !App::runningInConsole()
            && !App::runningUnitTests()
        ) {
            $this->bootBackend();
        }

        if (!App::runningInBackend()
            && !App::runningUnitTests()
            && !App::runningInConsole()
        ) {
            $this->bootFrontend();
        }
    }

    /**
     * Boot stuff for Frontend
     *
     * @return void
     */
    public function bootFrontend()
    {
        // Check for running in console or backend before route matching
        $rulesPath = storage_path('app/redirects.csv');

        if (!file_exists($rulesPath) || !is_readable($rulesPath)) {
            return;
        }

        $requestUri = str_replace(Request::getBasePath(), '', Request::getRequestUri());
        $manager = RedirectManager::createWithRulesPath($rulesPath);
        $rule = $manager->match($requestUri);

        if ($rule) {
            $manager->redirectWithRule($rule);
        }
    }

    /**
     * Boot stuff for Backend
     *
     * @return void
     */
    public function bootBackend()
    {
        Page::extend(function (Page $page) {
            $handler = new PageHandler($page);

            $page->bindEvent('model.beforeUpdate', function () use ($handler) {
                $handler->onBeforeUpdate();
            });

            $page->bindEvent('model.afterDelete', function () use ($handler) {
                $handler->onAfterDelete();
            });
        });

        Event::listen('redirects.changed', function () {
            PublishManager::instance()->publish();
        });
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
                ],
            ],
        ];
    }
}

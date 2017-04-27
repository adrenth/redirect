<?php

namespace Adrenth\Redirect;

use Adrenth\Redirect\Classes\CacheManager;
use Adrenth\Redirect\Classes\Exceptions\InvalidScheme;
use Adrenth\Redirect\Classes\Exceptions\RulesPathNotReadable;
use Adrenth\Redirect\Classes\PageHandler;
use Adrenth\Redirect\Classes\PublishManager;
use Adrenth\Redirect\Classes\RedirectManager;
use Adrenth\Redirect\Classes\StaticPageHandler;
use Adrenth\Redirect\Models\Redirect;
use Adrenth\Redirect\Models\Settings;
use Adrenth\Redirect\ReportWidgets\CreateRedirect;
use Adrenth\Redirect\ReportWidgets\TopTenRedirects;
use App;
use Backend;
use BadMethodCallException;
use Cms\Classes\Page;
use Event;
use Exception;
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
            'homepage' => 'http://octobercms.com/plugin/adrenth-redirect',
        ];
    }

    /**
     * {@inheritdoc}
     * @throws Exception
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
     * @throws BadMethodCallException If cache tags are not supported.
     */
    public function bootFrontend()
    {
        // Only handle specific request methods
        if (!in_array(Request::method(), ['GET', 'POST', 'HEAD'], true)) {
            return;
        }

        // Create the redirect manager if redirect rules are readable.
        try {
            $manager = RedirectManager::createWithDefaultRulesPath();
        } catch (RulesPathNotReadable $e) {
            return;
        }

        $manager->setLoggingEnabled(Settings::isLoggingEnabled())
            ->setStatisticsEnabled(Settings::isStatisticsEnabled());

        if (Request::header('X-Adrenth-Redirect') === 'Tester') {
            $manager->setStatisticsEnabled(false)
                ->setLoggingEnabled(false);
        }

        $requestUri = str_replace(Request::getBasePath(), '', Request::getRequestUri());

        try {
            if (CacheManager::cachingEnabledAndSupported()) {
                $rule = $manager->matchCached($requestUri, Request::getScheme());
            } else {
                $rule = $manager->match($requestUri, Request::getScheme());
            }
        } catch (InvalidScheme $e) {
            $rule = false;
        }

        if ($rule) {
            $manager->redirectWithRule($rule, $requestUri);
        }
    }

    /**
     * Boot stuff for Backend
     *
     * @return void
     * @throws Exception
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

        if (class_exists('\RainLab\Pages\Classes\Page')) {
            \RainLab\Pages\Classes\Page::extend(function (\RainLab\Pages\Classes\Page $page) {
                $handler = new StaticPageHandler($page);

                $page->bindEvent('model.beforeUpdate', function () use ($handler) {
                    $handler->onBeforeUpdate();
                });

                $page->bindEvent('model.afterDelete', function () use ($handler) {
                    $handler->onAfterDelete();
                });
            });
        }

        // When one or more redirects have been changed.
        Event::listen('redirects.changed', function () {
            if (CacheManager::cachingEnabledAndSupported()) {
                CacheManager::instance()->flush();
            }

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
        $navigation = [
            'redirect' => [
                'label' => 'adrenth.redirect::lang.navigation.menu_label',
                'icon' => 'icon-link',
                'url' => Backend::url('adrenth/redirect/redirects'),
                'order' => 50,
                'permissions' => [
                    'adrenth.redirect.access_redirects',
                ],
                'sideMenu' => [
                    'redirects' => [
                        'icon' => 'icon-link',
                        'label' => 'adrenth.redirect::lang.navigation.menu_label',
                        'url' => Backend::url('adrenth/redirect/redirects'),
                        'order' => 0,
                        'permissions' => [
                            'adrenth.redirect.access_redirects',
                        ],
                    ],
                    'categories' => [
                        'label' => 'adrenth.redirect::lang.buttons.categories',
                        'url' => Backend::url('adrenth/redirect/categories'),
                        'icon' => 'icon-tag',
                        'order' => 60,
                        'permissions' => [
                            'adrenth.redirect.access_redirects',
                        ],
                    ],
                    'import' => [
                        'label' => 'adrenth.redirect::lang.buttons.import',
                        'url' => Backend::url('adrenth/redirect/redirects/import'),
                        'icon' => 'icon-download',
                        'order' => 70,
                        'permissions' => [
                            'adrenth.redirect.access_redirects',
                        ],
                    ],
                    'export' => [
                        'label' => 'adrenth.redirect::lang.buttons.export',
                        'url' => Backend::url('adrenth/redirect/redirects/export'),
                        'icon' => 'icon-upload',
                        'order' => 80,
                        'permissions' => [
                            'adrenth.redirect.access_redirects',
                        ],
                    ],
                    'settings' => [
                        'label' => 'adrenth.redirect::lang.buttons.settings',
                        'url' => Backend::url('system/settings/update/adrenth/redirect/config'),
                        'icon' => 'icon-cogs',
                        'order' => 90,
                        'permissions' => [
                            'adrenth.redirect.access_redirects',
                        ],
                    ],
                ],
            ],
        ];

        if (Settings::isStatisticsEnabled()) {
            $navigation['redirect']['sideMenu']['statistics'] = [
                'icon' => 'icon-bar-chart',
                'label' => 'adrenth.redirect::lang.title.statistics',
                'url' => Backend::url('adrenth/redirect/statistics'),
                'order' => 10,
                'permissions' => [
                    'adrenth.redirect.access_redirects',
                ],
            ];
        }

        if (Settings::isTestLabEnabled()) {
            $navigation['redirect']['sideMenu']['test_lab'] = [
                'icon' => 'icon-flask',
                'label' => 'adrenth.redirect::lang.title.test_lab',
                'url' => Backend::url('adrenth/redirect/testlab'),
                'order' => 30,
                'permissions' => [
                    'adrenth.redirect.access_redirects',
                ],
            ];
        }

        if (Settings::isLoggingEnabled()) {
            $navigation['redirect']['sideMenu']['logs'] = [
                'label' => 'adrenth.redirect::lang.buttons.logs',
                'url' => Backend::url('adrenth/redirect/logs'),
                'icon' => 'icon-file-text-o',
                'visible' => false,
                'order' => 50,
                'permissions' => [
                    'adrenth.redirect.access_redirects',
                ],
            ];
        }

        return $navigation;
    }

    /**
     * {@inheritdoc}
     */
    public function registerSettings()
    {
        /** @noinspection ClassConstantCanBeUsedInspection */
        return [
            'config' => [
                'label' => 'adrenth.redirect::lang.settings.menu_label',
                'description' => 'adrenth.redirect::lang.settings.menu_description',
                'icon' => 'icon-link',
                'class' => 'Adrenth\Redirect\Models\Settings',
                'order' => 600,
                'permissions' => [
                    'adrenth.redirect.access_redirects',
                ],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerReportWidgets()
    {
        return [
            CreateRedirect::class => [
                'label' => 'adrenth.redirect::lang.buttons.create_redirect',
                'context' => 'dashboard'
            ],
            TopTenRedirects::class => [
                'label' => trans('adrenth.redirect::lang.statistics.top_redirects_this_month', ['top' => 10]),
                'context' => 'dashboard',
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerListColumnTypes()
    {
        return [
            'redirect_switch_color' => function ($value) {
                $format = '<div class="oc-icon-circle" style="color: %s">%s</div>';

                if ((int) $value === 1) {
                    return sprintf($format, '#95b753', e(trans('backend::lang.list.column_switch_true')));
                }

                return sprintf($format, '#cc3300', e(trans('backend::lang.list.column_switch_false')));
            },
            'redirect_match_type' => function ($value) {
                switch ($value) {
                    case Redirect::TYPE_EXACT:
                        return e(trans('adrenth.redirect::lang.redirect.exact'));
                    case Redirect::TYPE_PLACEHOLDERS:
                        return e(trans('adrenth.redirect::lang.redirect.placeholders'));
                    default:
                        return $value;
                }
            },
            'redirect_status_code' => function ($value) {
                switch ($value) {
                    case 301:
                        return e(trans('adrenth.redirect::lang.redirect.permanent'));
                    case 302:
                        return e(trans('adrenth.redirect::lang.redirect.temporary'));
                    case 303:
                        return e(trans('adrenth.redirect::lang.redirect.see_other'));
                    case 404:
                        return e(trans('adrenth.redirect::lang.redirect.not_found'));
                    case 410:
                        return e(trans('adrenth.redirect::lang.redirect.gone'));
                    default:
                        return $value;
                }
            },
            'redirect_target_type' => function ($value) {
                switch ($value) {
                    case Redirect::TARGET_TYPE_PATH_URL:
                        return e(trans('adrenth.redirect::lang.redirect.target_type_path_or_url'));
                    case Redirect::TARGET_TYPE_CMS_PAGE:
                        return e(trans('adrenth.redirect::lang.redirect.target_type_cms_page'));
                    case Redirect::TARGET_TYPE_STATIC_PAGE:
                        return e(trans('adrenth.redirect::lang.redirect.target_type_static_page'));
                    default:
                        return $value;
                }
            },
            'redirect_from_url' => function ($value) {
                $maxChars = 40;
                $textLength = strlen($value);
                if ($textLength > $maxChars) {
                    return '<span title="' . e($value) . '">'
                        . substr_replace($value, '...', $maxChars / 2, $textLength - $maxChars)
                        . '</span>';
                }
                return $value;
            },
            'redirect_system' => function ($value) {
                return sprintf(
                    '<span class="%s" title="%s"></span>',
                    $value ? 'oc-icon-magic' : 'oc-icon-user',
                    e(trans('adrenth.redirect::lang.redirect.system_tip'))
                );
            },
        ];
    }
}

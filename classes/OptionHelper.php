<?php

namespace Adrenth\Redirect\Classes;

use Adrenth\Redirect\Models\Category;
use Adrenth\Redirect\Models\Redirect;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use System\Classes\PluginManager;

/**
 * Class OptionHelper
 *
 * @package Adrenth\Redirect\Classes
 */
class OptionHelper
{
    /**
     * @param int $statusCode
     * @return array
     */
    public static function getTargetTypeOptions($statusCode)
    {
        if ($statusCode === 404 || $statusCode === 410) {
            return [
                Redirect::TARGET_TYPE_NONE => 'adrenth.redirect::lang.redirect.target_type_none',
            ];
        }

        return [
            Redirect::TARGET_TYPE_PATH_URL => 'adrenth.redirect::lang.redirect.target_type_path_or_url',
            Redirect::TARGET_TYPE_CMS_PAGE => 'adrenth.redirect::lang.redirect.target_type_cms_page',
            Redirect::TARGET_TYPE_STATIC_PAGE => 'adrenth.redirect::lang.redirect.target_type_static_page',
        ];
    }

    /**
     * Get all CMS pages as an option array
     *
     * @return array
     */
    public static function getCmsPageOptions()
    {
        return ['' => '-- ' . trans('adrenth.redirect::lang.redirect.none') . ' --' ] + Page::getNameList();
    }

    /**
     * Get all Static Pages as an option array
     *
     * @return array
     */
    public static function getStaticPageOptions()
    {
        $options = ['' => '-- ' . trans('adrenth.redirect::lang.redirect.none') . ' --' ];

        $hasPagesPlugin = PluginManager::instance()->hasPlugin('RainLab.Pages');

        if (!$hasPagesPlugin) {
            return $options;
        }

        $pages = \RainLab\Pages\Classes\Page::listInTheme(Theme::getActiveTheme());

        /** @var \RainLab\Pages\Classes\Page $page */
        foreach ($pages as $page) {
            if (array_key_exists('title', $page->viewBag)) {
                $options[$page->getFileName()] = $page->viewBag['title'];
            }
        }

        return $options;
    }

    /**
     * @return array
     */
    public static function getCategoryOptions()
    {
        return (array) Category::all(['id', 'name'])->lists('name', 'key');
    }
}

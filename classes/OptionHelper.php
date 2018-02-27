<?php
/**
 * October CMS plugin: Adrenth.Redirect
 *
 * Copyright (c) 2016 - 2018 Alwin Drenth
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute,
 * sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or
 * substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

declare(strict_types=1);

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
     * Returns available target type options based on given status code.
     *
     * @param int $statusCode
     * @return array
     */
    public static function getTargetTypeOptions($statusCode): array
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
     * Get all CMS pages as an option array.
     *
     * @return array
     */
    public static function getCmsPageOptions(): array
    {
        return ['' => '-- ' . trans('adrenth.redirect::lang.redirect.none') . ' --' ] + Page::getNameList();
    }

    /**
     * Get all Static Pages as an option array.
     *
     * @return array
     */
    public static function getStaticPageOptions(): array
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
                $options[$page->getBaseFileName()] = $page->viewBag['title'];
            }
        }

        return $options;
    }

    /**
     * Get all categories as an option array.
     *
     * @return array
     */
    public static function getCategoryOptions(): array
    {
        return (array) Category::all(['id', 'name'])->lists('name', 'key');
    }
}

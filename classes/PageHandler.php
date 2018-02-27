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

use Adrenth\Redirect\Models\Redirect;
use Cms\Classes\CmsCompoundObject;
use Event;
use Exception;

/**
 * Class PageHandler
 *
 * @package Adrenth\Redirect\Classes
 */
class PageHandler
{
    /** @var CmsCompoundObject */
    protected $page;

    /**
     * @param CmsCompoundObject $page
     */
    public function __construct(CmsCompoundObject $page)
    {
        $this->page = $page;
    }

    /**
     * Triggered before the Page is stored to filesystem.
     *
     * @return void
     */
    public function onBeforeUpdate()//: void
    {
        if ($this->page->getAttribute('is_hidden')) {
            return;
        }

        // Url hasn't change
        if (!$this->hasUrlChanged()) {
            return;
        }

        // Parameters and regex are not supported
        if ($this->newUrlContainsParams()) {
            return;
        }

        // Don't create a redirect loop; that would be silly ;-)
        if ($this->getNewUrl() === $this->getOriginalUrl()) {
            return;
        }

        $this->createRedirect();

        Event::fire('redirects.changed');
    }

    /**
     * Triggered after a Page has been deleted.
     *
     * @return void
     * @throws Exception
     */
    public function onAfterDelete()//: void
    {
        Redirect::where($this->getTargetType(), '=', $this->page->getBaseFileName())
            ->where('system', '=', 1)
            ->delete();

        Redirect::where($this->getTargetType(), '=', $this->page->getBaseFileName())
            ->where('system', '=', 0)
            ->update([
                $this->getTargetType() => null,
                'is_enabled' => false,
            ]);

        Event::fire('redirects.changed');
    }

    /**
     * @return bool
     */
    protected function hasUrlChanged(): bool
    {
        return array_key_exists('url', $this->page->getDirty());
    }

    /**
     * @return bool
     */
    protected function newUrlContainsParams(): bool
    {
        return strpos($this->getNewUrl(), ':') !== false;
    }

    /**
     * @return string
     */
    protected function getOriginalUrl(): string
    {
        return (string) $this->page->getOriginal('url');
    }

    /**
     * @return string
     */
    protected function getNewUrl(): string
    {
        $dirty = $this->page->getDirty();

        if (array_key_exists('url', $dirty)) {
            return $dirty['url'];
        }

        return (string) $this->page->getOriginal('url');
    }

    /**
     * @return string
     */
    protected function getTargetType(): string
    {
        return Redirect::TARGET_TYPE_CMS_PAGE;
    }

    /**
     * Create CMS page type
     *
     * @return void
     */
    protected function createRedirect()//: void
    {
        Redirect::create([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => $this->getTargetType(),
            'from_url' => $this->getOriginalUrl(),
            'to_url' => null,
            $this->getTargetType() => $this->page->getBaseFileName(),
            'status_code' => 301,
            'is_enabled' => true,
            'system' => true,
        ]);
    }
}

<?php

namespace Adrenth\Redirect\Classes;

use Adrenth\Redirect\Models\Redirect;
use Cms\Classes\CmsCompoundObject;
use Event;

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
    public function onBeforeUpdate()
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
     * @throws \Exception
     */
    public function onAfterDelete()
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
    protected function hasUrlChanged()
    {
        return array_key_exists('url', $this->page->getDirty());
    }

    /**
     * @return bool
     */
    protected function newUrlContainsParams()
    {
        return strpos($this->getNewUrl(), ':') !== false;
    }

    /**
     * @return array
     */
    protected function getOriginalUrl()
    {
        return $this->page->getOriginal('url');
    }

    /**
     * @return array
     */
    protected function getNewUrl()
    {
        $dirty = $this->page->getDirty();

        if (array_key_exists('url', $dirty)) {
            return $dirty['url'];
        }

        return $this->page->getOriginal('url');
    }

    /**
     * @return string
     */
    protected function getTargetType()
    {
        return Redirect::TARGET_TYPE_CMS_PAGE;
    }

    /**
     * Create CMS page type
     *
     * @return void
     */
    protected function createRedirect()
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

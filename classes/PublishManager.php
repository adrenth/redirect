<?php

namespace Adrenth\Redirect\Classes;

use Adrenth\Redirect\Models\Redirect;
use DB;
use Illuminate\Database\Eloquent\Collection;
use League\Csv\Writer;
use October\Rain\Support\Traits\Singleton;

/**
 * Class PublishManager
 *
 * @package Adrenth\Redirect\Classes
 */
class PublishManager
{
    use Singleton;

    /** @type string */
    private $redirectsFile;

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->redirectsFile = storage_path('app/redirects.csv');
    }

    /**
     * Publish applicable redirects
     *
     * @return int Number of published redirects
     */
    public function publish()
    {
        if (file_exists($this->redirectsFile)) {
            unlink($this->redirectsFile);
        }

        /** @type Collection $redirects */
        $redirects = Redirect::query()
            ->where('is_enabled', '=', 1)
            ->orderBy('sort_order')
            ->get([
                'id',
                'match_type',
                'target_type',
                'from_url',
                'to_url',
                'cms_page',
                'static_page',
                'status_code',
                'requirements',
                'from_date',
                'to_date',
            ]);

        // TODO: Throw proper exception
        try {
            $writer = Writer::createFromPath($this->redirectsFile, 'w+');
            $writer->insertAll($redirects->toArray());
        } catch (\Exception $e) {
            // ..
        }

        return $redirects->count();
    }
}

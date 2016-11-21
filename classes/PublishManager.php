<?php

namespace Adrenth\Redirect\Classes;

use Adrenth\Redirect\Models\Redirect;
use Illuminate\Database\Eloquent\Collection;
use League\Csv\Writer;
use Log;
use October\Rain\Support\Traits\Singleton;

/**
 * Class PublishManager
 *
 * @package Adrenth\Redirect\Classes
 */
class PublishManager
{
    use Singleton;

    /** @var string */
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

        /** @var Collection $redirects */
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

        try {
            $writer = Writer::createFromPath($this->redirectsFile, 'w+');

            foreach ($redirects->toArray() as $row) {
                if (array_key_exists('requirements', $row)) {
                    $row['requirements'] = json_encode($row['requirements']);
                }

                $writer->insertOne($row);
            }
        } catch (\Exception $e) {
            Log::critical($e);
        }

        return $redirects->count();
    }
}

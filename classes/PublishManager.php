<?php

namespace Adrenth\Redirect\Classes;

use Adrenth\Redirect\Models\Redirect;
use DB;
use Illuminate\Database\Eloquent\Collection;
use League\Csv\Writer;
use Log;

/**
 * Class PublishManager
 *
 * @package Adrenth\Redirect\Classes
 */
class PublishManager
{
    /** @type string */
    private $redirectsFile;

    public function __construct()
    {
        $this->redirectsFile = storage_path('app/redirects.csv');

        // Make sure that all redirects are marked un-published if redirect file is not present
        if (!file_exists($this->redirectsFile)) {
            Redirect::unpublishAll();
        }
    }

    /**
     * Publish applicable redirects
     *
     * @return int Number of published redirects
     */
    public function publish()
    {
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

        $writer = Writer::createFromPath($this->redirectsFile, 'w+');
        $writer->insertAll($redirects->toArray());

        try {
            $table = (new Redirect())->table;

            DB::table($table)->where('is_enabled', '=', 1)
                ->update(['publish_status' => Redirect::STATUS_PUBLISHED]);

            DB::table($table)->where('is_enabled', '=', 0)
                ->update(['publish_status' => Redirect::STATUS_NOT_PUBLISHED]);
        } catch (\InvalidArgumentException $e) {
            Log::error($e->getMessage());
        }

        return $redirects->count();
    }

    /**
     * @return int
     */
    public function getUnpublishedCount()
    {
        $total = 0;

        /** @type Redirect $redirect */
        $redirect = Redirect::select([DB::raw('COUNT(id) AS redirect_count')])
            ->where('publish_status', '<>', Redirect::STATUS_PUBLISHED)
            ->where('is_enabled', '=', 1)
            ->first(['redirect_count']);

        $total += (int) $redirect->getAttribute('redirect_count');

        $redirect = Redirect::select([DB::raw('COUNT(id) AS redirect_count')])
            ->where('publish_status', '=', Redirect::STATUS_CHANGED)
            ->where('is_enabled', '=', 0)
            ->first(['redirect_count']);

        $total += (int) $redirect->getAttribute('redirect_count');

        return $total;
    }

    /**
     * @param int $status
     * @return int
     */
    public function getCount($status)
    {
        $redirect = Redirect::select([DB::raw('COUNT(id) AS redirect_count')])
            ->where('publish_status', '=', (int) $status)
            ->first(['redirect_count']);

        return (int) $redirect->getAttribute('redirect_count');
    }
}

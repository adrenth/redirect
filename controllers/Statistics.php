<?php

namespace Adrenth\Redirect\Controllers;

use Adrenth\Redirect\Models\Client;
use Adrenth\Redirect\Models\Redirect;
use BackendMenu;
use Backend\Classes\Controller;
use Carbon\Carbon;
use October\Rain\Database\Collection;

/**
 * Class Statistics
 *
 * @package Adrenth\Redirect\Controllers
 */
class Statistics extends Controller
{
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Adrenth.Redirect', 'redirect', 'statistics');

        $this->pageTitle = 'adrenth.redirect::lang.title.statistics';

        $this->addJs('https://cdnjs.cloudflare.com/ajax/libs/vis/4.18.1/vis.min.js');
        $this->addJs('/plugins/adrenth/redirect/assets/javascript/statistics.js');

        $this->addCss('https://cdnjs.cloudflare.com/ajax/libs/vis/4.18.1/vis.min.css');
        $this->addCss('/plugins/adrenth/redirect/assets/css/statistics.css');
    }

    public function index()
    {
        $this->vars = [
            'redirectHitsPerMonth' => $this->getRedirectHitsPerMonth(),
            'topTenCrawlersThisMonth' => $this->getTopTenCrawlersThisMonth(),
            'topTenRedirectsThisMonth' => $this->getTopTenRedirectsThisMonth(),
            'totalActiveRedirects' => $this->getTotalActiveRedirects(),
            'activeRedirects' => $this->getActiveRedirects(),
            'totalRedirectsServed' => $this->getTotalRedirectsServed(),
            'totalThisMonth' => $this->getTotalThisMonth(),
            'totalLastMonth' => $this->getTotalLastMonth(),
            'latestClient' => $this->getLatestClient(),
        ];
    }

    /**
     * @return int
     */
    public function getTotalRedirectsServed()
    {
        return Client::count();
    }

    /**
     * @return Client|null
     */
    public function getLatestClient()
    {
        return Client::orderBy('timestamp', 'desc')->limit(1)->first();
    }


    /**
     * @return int
     */
    public function getTotalThisMonth()
    {
        return Client::where('month', '=', date('m'))
            ->where('year', '=', date('Y'))
            ->count();
    }

    /**
     * @return int
     */
    public function getTotalLastMonth()
    {
        $lastMonth = Carbon::today();
        $lastMonth->subMonthNoOverflow();

        return Client::where('month', '=', $lastMonth->month)
            ->where('year', '=', $lastMonth->year)
            ->count();
    }

    /**
     * @return array
     */
    public function getActiveRedirects()
    {
        $groupedRedirects = [];

        /** @var Collection $redirects */
        $redirects = Redirect::enabled()
            ->get()
            ->filter(function (Redirect $redirect) {
                return $redirect->isActiveOnDate(Carbon::today());
            });

        /** @var Redirect $redirect */
        foreach ($redirects as $redirect) {
            $groupedRedirects[$redirect->getAttribute('status_code')][] = $redirect;
        }

        return $groupedRedirects;
    }

    /**
     * @return int
     */
    public function getTotalActiveRedirects()
    {
        return Redirect::enabled()
            ->get()
            ->filter(function (Redirect $redirect) {
                return $redirect->isActiveOnDate(Carbon::today());
            })
            ->count();
    }

    /**
     * @param bool $crawler
     * @return array
     */
    public function getRedirectHitsPerDay($crawler = false)
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $result = Client::selectRaw('COUNT(id) AS hits')
            ->addSelect('day', 'month', 'year')
            ->groupBy('day', 'month', 'year')
            ->orderByRaw('year ASC, month ASC, day ASC');

        if ($crawler) {
            $result->whereNotNull('crawler');
        } else {
            $result->whereNull('crawler');
        }

        return $result->limit(365)
            ->get()
            ->toArray();
    }

    /**
     * @return array
     */
    public function getRedirectHitsPerMonth()
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return (array) Client::selectRaw('COUNT(id) AS hits')
            ->addSelect('month', 'year')
            ->groupBy('month', 'year')
            ->orderByRaw('year DESC, month DESC')
            ->limit(12)
            ->get()
            ->toArray();
    }

    /**
     * @return array
     */
    public function getTopTenCrawlersThisMonth()
    {
        return (array) Client::selectRaw('COUNT(id) AS hits')
            ->addSelect('crawler')
            ->whereNotNull('crawler')
            ->whereMonth('timestamp', '=', date('m'))
            ->whereYear('timestamp', '=', date('Y'))
            ->groupBy('crawler')
            ->orderByRaw('hits DESC')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * @return array
     */
    public function getTopTenRedirectsThisMonth()
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return (array) Client::selectRaw('COUNT(redirect_id) AS hits')
            ->addSelect('redirect_id', 'r.from_url')
            ->join('adrenth_redirect_redirects AS r', 'r.id', '=', 'redirect_id')
            ->whereMonth('timestamp', '=', date('m'))
            ->whereYear('timestamp', '=', date('Y'))
            ->groupBy('redirect_id')
            ->orderByRaw('hits DESC')
            ->limit(10)
            ->get()
            ->toArray();
    }

    // @codingStandardsIgnoreStart

    public function index_onRedirectHitsPerDay()
    {
        $crawlerHits = $this->getRedirectHitsPerDay(true);

        $data = [];

        foreach ($crawlerHits as $hit) {
            $data[] = [
                'x' => date('Y-m-d', mktime(0, 0, 0, $hit['month'], $hit['day'], $hit['year'])),
                'y' => (int) $hit['hits'],
                'group' => 0
            ];
        }

        $notCrawlerHits = $this->getRedirectHitsPerDay(false);

        foreach ($notCrawlerHits as $hit) {
            $data[] = [
                'x' => date('Y-m-d', mktime(0, 0, 0, $hit['month'], $hit['day'], $hit['year'])),
                'y' => (int) $hit['hits'],
                'group' => 1
            ];
        }


        return json_encode($data);
    }

    // @codingStandardsIgnoreEnd
}

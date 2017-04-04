<?php

namespace Adrenth\Redirect\Controllers;

use Adrenth\Redirect\Classes\StatisticsHelper;
use BackendMenu;
use Backend\Classes\Controller;

/**
 * Class Statistics
 *
 * @package Adrenth\Redirect\Controllers
 */
class Statistics extends Controller
{
    /**
     * {@inheritdoc}
     */
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

    /**
     * @return void
     */
    public function index()
    {
        $helper = new StatisticsHelper();

        $this->vars = [
            'redirectHitsPerMonth' => $helper->getRedirectHitsPerMonth(),
            'topTenCrawlersThisMonth' => $helper->getTopTenCrawlersThisMonth(),
            'topTenRedirectsThisMonth' => $helper->getTopTenRedirectsThisMonth(),
            'totalActiveRedirects' => $helper->getTotalActiveRedirects(),
            'activeRedirects' => $helper->getActiveRedirects(),
            'totalRedirectsServed' => $helper->getTotalRedirectsServed(),
            'totalThisMonth' => $helper->getTotalThisMonth(),
            'totalLastMonth' => $helper->getTotalLastMonth(),
            'latestClient' => $helper->getLatestClient(),
        ];
    }

    // @codingStandardsIgnoreStart

    /**
     * @return string
     */
    public function index_onRedirectHitsPerDay()
    {
        $helper = new StatisticsHelper();

        $crawlerHits = $helper->getRedirectHitsPerDay(true);

        $data = [];

        foreach ($crawlerHits as $hit) {
            $data[] = [
                'x' => date('Y-m-d', mktime(0, 0, 0, $hit['month'], $hit['day'], $hit['year'])),
                'y' => (int) $hit['hits'],
                'group' => 0
            ];
        }

        $notCrawlerHits = $helper->getRedirectHitsPerDay(false);

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

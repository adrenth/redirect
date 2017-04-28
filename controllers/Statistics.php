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
    /** @var StatisticsHelper */
    private $helper;

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

        $this->helper = new StatisticsHelper();
    }

    /**
     * @return void
     */
    public function index()
    {
    }

    // @codingStandardsIgnoreStart

    /**
     * @return string
     */
    public function index_onRedirectHitsPerDay()
    {
        $crawlerHits = $this->helper->getRedirectHitsPerDay(true);

        $data = [];

        foreach ($crawlerHits as $hit) {
            $data[] = [
                'x' => date('Y-m-d', mktime(0, 0, 0, $hit['month'], $hit['day'], $hit['year'])),
                'y' => (int) $hit['hits'],
                'group' => 0
            ];
        }

        $notCrawlerHits = $this->helper->getRedirectHitsPerDay(false);

        foreach ($notCrawlerHits as $hit) {
            $data[] = [
                'x' => date('Y-m-d', mktime(0, 0, 0, $hit['month'], $hit['day'], $hit['year'])),
                'y' => (int) $hit['hits'],
                'group' => 1
            ];
        }


        return json_encode($data);
    }

    /**
     * @return array
     */
    public function index_onLoadTopRedirectsThisMonth()
    {
        return [
            '#topRedirectsThisMonth' => $this->makePartial('top-redirects-this-month', [
                'topTenRedirectsThisMonth' => $this->helper->getTopRedirectsThisMonth(),
            ]),
        ];
    }

    /**
     * @return array
     */
    public function index_onLoadTopCrawlersThisMonth()
    {
        return [
            '#topCrawlersThisMonth' => $this->makePartial('top-crawlers-this-month', [
                'topTenCrawlersThisMonth' => $this->helper->getTopTenCrawlersThisMonth(),
            ]),
        ];
    }

    /**
     * @return array
     */
    public function index_onLoadRedirectHitsPerMonth()
    {
        return [
            '#redirectHitsPerMonth' => $this->makePartial('redirect-hits-per-month', [
                'redirectHitsPerMonth' => $this->helper->getRedirectHitsPerMonth(),
            ]),
        ];
    }

    /**
     * @return array
     */
    public function index_onLoadScoreBoard()
    {
        return [
            '#scoreBoard' => $this->makePartial('score-board', [
                'redirectHitsPerMonth' => $this->helper->getRedirectHitsPerMonth(),
                'totalActiveRedirects' => $this->helper->getTotalActiveRedirects(),
                'activeRedirects' => $this->helper->getActiveRedirects(),
                'totalRedirectsServed' => $this->helper->getTotalRedirectsServed(),
                'totalThisMonth' => $this->helper->getTotalThisMonth(),
                'totalLastMonth' => $this->helper->getTotalLastMonth(),
                'latestClient' => $this->helper->getLatestClient(),
            ]),
        ];
    }

    // @codingStandardsIgnoreEnd
}

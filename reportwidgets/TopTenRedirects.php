<?php
/**
 * OctoberCMS plugin: Adrenth.Redirect
 *
 * Copyright (c) Alwin Drenth 2017.
 *
 * Licensing information:
 * https://octobercms.com/help/license/regular
 * https://octobercms.com/help/license/extended
 * https://octobercms.com/help/license/faqs
 */

declare(strict_types=1);

namespace Adrenth\Redirect\ReportWidgets;

use Adrenth\Redirect\Classes\StatisticsHelper;
use Backend\Classes\Controller;
use Backend\Classes\ReportWidgetBase;

/** @noinspection LongInheritanceChainInspection */

/**
 * Class TopTenRedirects
 *
 * @property string alias
 * @package Adrenth\Redirect\ReportWidgets
 */
class TopTenRedirects extends ReportWidgetBase
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Controller $controller, array $properties = [])
    {
        $this->alias = 'redirectTopTenRedirects';

        parent::__construct($controller, $properties);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $helper = new StatisticsHelper();

        return $this->makePartial('widget', [
            'topTenRedirectsThisMonth' => $helper->getTopRedirectsThisMonth()
        ]);
    }
}

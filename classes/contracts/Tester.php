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

namespace Adrenth\Redirect\Classes\Contracts;

use Adrenth\Redirect\Classes\TesterResult;

/**
 * Interface Tester
 *
 * @package Adrenth\Redirect\Classes\Contracts
 */
interface Tester
{
    /**
     * Execute the test
     *
     * @return TesterResult
     */
    public function execute(): TesterResult;

    /**
     * The testers' test path. E.g /test/path
     *
     * @return string
     */
    public function getTestPath(): string;

    /**
     * The testers' full test URL. E.g. https://test.com/test/path
     *
     * @return string
     */
    public function getTestUrl(): string;
}

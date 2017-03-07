<?php

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
    public function execute();

    /**
     * The testers' test path. E.g /test/path
     *
     * @return string
     */
    public function getTestPath();

    /**
     * The testers' full test URL. E.g. https://test.com/test/path
     *
     * @return string
     */
    public function getTestUrl();
}

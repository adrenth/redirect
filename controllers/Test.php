<?php

namespace Adrenth\Redirect\Controllers;

use Adrenth\Redirect\Classes\RedirectManager;
use Adrenth\Redirect\Classes\RedirectTester;
use Backend\Classes\Controller;
use BackendMenu;
use Cms;
use Flash;
use Input;

/**
 * Class Test
 *
 * @package Adrenth\Redirect\Controllers
 */
class Test extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Adrenth.Redirect', 'redirect', 'test');
    }

    public function index()
    {
    }

    // @codingStandardsIgnoreStart

    public function index_onTest()
    {
        $testPath = Input::get('testPath');

        if (empty($testPath)) {
            Flash::error('Cannot start tests with an empty path.');
            return [];
        }

        $tester = new RedirectTester($testPath);

        return [
            '#testResults' => $this->makePartial('test_results', [
                'maxRedirectsResult' => $tester->testMaxRedirects(),
                'matchedRedirectResult' => $tester->testMatchRedirect(),
                'responseCodeResult' => $tester->testResponseCode(),
                'redirectCountResult' => $tester->testRedirectCount(),
            ])
        ];
    }

    // @codingStandardsIgnoreEnd
}

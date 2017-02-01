<?php

namespace Adrenth\Redirect\Controllers;

use Adrenth\Redirect\Classes\RedirectManager;
use Adrenth\Redirect\Classes\RedirectTester;
use Backend\Classes\Controller;
use BackendMenu;
use Cms;
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
        $this->vars['maxRedirectsError'] = '';
        $this->vars['matchedRedirect'] = false;
    }

    // @codingStandardsIgnoreStart

    public function index_onTest()
    {
        $testPath = Input::get('testPath', '/');

        $tester = new RedirectTester($testPath);

        $tester->testMatchRedirect();

        return [
            '#testResults' => $this->makePartial('test_results', [
                'maxRedirectsError' => $tester->testMaxRedirects(),
                'matchedRedirect' => $tester->testMatchRedirect(),
            ])
        ];
    }

    // @codingStandardsIgnoreEnd
}

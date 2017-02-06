<?php

namespace Adrenth\Redirect\Controllers;

use Adrenth\Redirect\Classes\Testers\RedirectCount;
use Adrenth\Redirect\Classes\Testers\RedirectLoop;
use Adrenth\Redirect\Classes\Testers\RedirectMatch;
use Adrenth\Redirect\Classes\Testers\ResponseCode;
use Adrenth\Redirect\Models\Redirect;
use Backend\Classes\Controller;
use BackendMenu;
use Flash;
use Input;

/**
 * Class Test
 *
 * @package Adrenth\Redirect\Controllers
 */
class TestLab extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Adrenth.Redirect', 'redirect', 'test_lab');
    }

    public function index()
    {
        $this->pageTitle = trans('adrenth.redirect::lang.title.test_lab');
    }

//    private function loadRedirectsForTestLab()
//    {
//        // loads all redirects
//    }
//
//    private function getNextRedirect()
//    {
//        // get the next redirect to start
//
//        return new Redirect();
//    }
//
//    private function testRedirect(Redirect $redirect)
//    {
//
//    }

    // @codingStandardsIgnoreStart

    public function index_onTest()
    {
        $testPath = Input::get('testPath');

        if (empty($testPath)) {
            Flash::error('Cannot start tests with an empty path.');
            return [];
        }

        return [
            '#testResults' => $this->makePartial('test_results', [
                'maxRedirectsResult' => (new RedirectLoop($testPath))->execute(),
                'matchedRedirectResult' => (new RedirectMatch($testPath))->execute(),
                'responseCodeResult' => (new ResponseCode($testPath))->execute(),
                'redirectCountResult' => (new RedirectCount($testPath))->execute(),
            ])
        ];
    }

    // @codingStandardsIgnoreEnd
}

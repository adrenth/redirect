<?php

namespace Adrenth\Redirect\Controllers;

use Adrenth\Redirect\Classes\Testers\RedirectCount;
use Adrenth\Redirect\Classes\Testers\RedirectLoop;
use Adrenth\Redirect\Classes\Testers\RedirectMatch;
use Adrenth\Redirect\Classes\Testers\ResponseCode;
use Adrenth\Redirect\Models\Redirect;
use Backend\Classes\Controller;
use BackendMenu;
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
        $this->bodyClass = 'layout-relative';

        parent::__construct();

        BackendMenu::setContext('Adrenth.Redirect', 'redirect', 'test_lab');
    }

    public function index()
    {
        $this->pageTitle = trans('adrenth.redirect::lang.title.test_lab');

        $this->addCss('/plugins/adrenth/redirect/assets/css/test-lab.css', 'Adrenth.Redirect');

        $this->vars['redirectCount'] = Redirect::enabled()->testLabEnabled()->count();
    }

    /**
     * @param int $offset
     * @return Redirect|null
     */
    private function loadRedirect($offset)
    {
        return Redirect::enabled()
            ->testLabEnabled()
            ->offset($offset)
            ->limit(1)
            ->orderBy('sort_order')
            ->first();
    }

    // @codingStandardsIgnoreStart

    public function index_onTest()
    {
        $offset = Input::get('offset');

        $redirect = $this->loadRedirect($offset);

        if ($redirect === null) {
            return '';
        }

        $testPath = '/';

        if ($redirect->isMatchTypeExact()) {
            $testPath = $redirect->getAttribute('from_url');
        } elseif ($redirect->getAttribute('test_lab_path')) {
            $testPath = $redirect->getAttribute('test_lab_path');
        }

        return $this->makePartial(
            'tester_result', [
                'redirect' => $redirect,
                'testPath' => $testPath,
                'maxRedirectsResult' => (new RedirectLoop($testPath))->execute(),
                'matchedRedirectResult' => (new RedirectMatch($testPath))->execute(),
                'responseCodeResult' => (new ResponseCode($testPath))->execute(),
                'redirectCountResult' => (new RedirectCount($testPath))->execute(),
            ]
        );
    }

    // @codingStandardsIgnoreEnd
}

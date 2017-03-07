<?php

namespace Adrenth\Redirect\Controllers;

use Adrenth\Redirect\Classes\Testers\RedirectCount;
use Adrenth\Redirect\Classes\Testers\RedirectFinalDestination;
use Adrenth\Redirect\Classes\Testers\RedirectLoop;
use Adrenth\Redirect\Classes\Testers\RedirectMatch;
use Adrenth\Redirect\Classes\Testers\ResponseCode;
use Adrenth\Redirect\Models\Redirect;
use Backend\Classes\Controller;
use BackendMenu;
use Flash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        $this->addJs('/plugins/adrenth/redirect/assets/javascript/test-lab.js', 'Adrenth.Redirect');

        $this->vars['redirectCount'] = $this->getRedirectCount();
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

        return $this->makePartial(
            'tester_result', [
                'redirect' => $redirect,
                'testPath' => $this->getTestPath($redirect),
                'testResults' => $this->getTestResults($redirect),
            ]
        );
    }

    /**
     * @return array
     * @throws ModelNotFoundException
     */
    public function index_onReRun()
    {
        /** @var Redirect $redirect */
        $redirect = Redirect::findOrFail(post('id'));

        Flash::success('Test has been executed.');

        return [
            '#testerResult' . $redirect->getKey() => $this->makePartial(
                'tester_result_items',
                $this->getTestResults($redirect)
            )
        ];
    }

    /**
     * @return array
     * @throws ModelNotFoundException
     */
    public function index_onExclude()
    {
        /** @var Redirect $redirect */
        $redirect = Redirect::findOrFail(post('id'));
        $redirect->update(['test_lab' => false]);

        Flash::success('Redirect has been excluded from TestLab and will not show up on next test run.');

        return [
            '#testButtonWrapper' => $this->makePartial(
                'test_button',
                [
                    'redirectCount' => $this->getRedirectCount()
                ]
            )
        ];
    }

    // @codingStandardsIgnoreEnd

    /**
     * @param Redirect $redirect
     * @return string
     */
    public function getTestPath(Redirect $redirect)
    {
        $testPath = '/';

        if ($redirect->isMatchTypeExact()) {
            $testPath = (string) $redirect->getAttribute('from_url');
        } elseif ($redirect->getAttribute('test_lab_path')) {
            $testPath = (string) $redirect->getAttribute('test_lab_path');
        }

        return $testPath;
    }

    /**
     * @param Redirect $redirect
     * @return array
     */
    public function getTestResults(Redirect $redirect)
    {
        $testPath = $this->getTestPath($redirect);

        return [
            'maxRedirectsResult' => (new RedirectLoop($testPath))->execute(),
            'matchedRedirectResult' => (new RedirectMatch($testPath))->execute(),
            'responseCodeResult' => (new ResponseCode($testPath))->execute(),
            'redirectCountResult' => (new RedirectCount($testPath))->execute(),
            'finalDestinationResult' => (new RedirectFinalDestination($testPath))->execute(),
        ];
    }

    /**
     * @return int
     */
    private function getRedirectCount()
    {
        return (int) Redirect::enabled()->testLabEnabled()->count();
    }
}

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

namespace Adrenth\Redirect\Controllers;

use Adrenth\Redirect\Classes\Testers\RedirectCount;
use Adrenth\Redirect\Classes\Testers\RedirectFinalDestination;
use Adrenth\Redirect\Classes\Testers\RedirectLoop;
use Adrenth\Redirect\Classes\Testers\RedirectMatch;
use Adrenth\Redirect\Classes\Testers\ResponseCode;
use Adrenth\Redirect\Models\Redirect;
use Backend\Classes\Controller;
use BackendMenu;
use Carbon\Carbon;
use Exception;
use Flash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Input;
use October\Rain\Database\Collection;

/**
 * Class Test
 *
 * @property string bodyClass
 * @package Adrenth\Redirect\Controllers
 */
class TestLab extends Controller
{
    /** @var array */
    private $redirects;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->bodyClass = 'layout-relative';

        parent::__construct();

        BackendMenu::setContext('Adrenth.Redirect', 'redirect', 'test_lab');

        $this->loadRedirects();
    }

    /**
     * /index
     *
     * @return void
     */
    public function index()//: void
    {
        $this->pageTitle = 'adrenth.redirect::lang.title.test_lab';

        $this->addCss('/plugins/adrenth/redirect/assets/css/test-lab.css', 'Adrenth.Redirect');
        $this->addJs('/plugins/adrenth/redirect/assets/javascript/test-lab.js', 'Adrenth.Redirect');

        $this->vars['redirectCount'] = $this->getRedirectCount();
    }

    /**
     * Load redirects.
     *
     * @return void
     */
    private function loadRedirects()//: void
    {
        /** @var Collection $redirects */
        $this->redirects = array_values(Redirect::enabled()
            ->testLabEnabled()
            ->orderBy('sort_order')
            ->get()
            ->filter(function (Redirect $redirect) {
                return $redirect->isActiveOnDate(Carbon::today());
            })
            ->all());
    }

    /**
     * @param int $offset
     * @return Redirect|null
     */
    private function offsetGetRedirect($offset)//: ?Redirect
    {
        if (array_key_exists($offset, $this->redirects)) {
            return $this->redirects[$offset];
        }

        return null;
    }

    // @codingStandardsIgnoreStart

    /**
     * @return string
     */
    public function index_onTest(): string
    {
        $offset = (int) Input::get('offset');

        $redirect = $this->offsetGetRedirect($offset);

        if ($redirect === null) {
            return '';
        }

        try {
            $partial = (string) $this->makePartial(
                'tester_result', [
                    'redirect' => $redirect,
                    'testPath' => $this->getTestPath($redirect),
                    'testResults' => $this->getTestResults($redirect),
                ]
            );
        } catch (Exception $e) {
            $partial = (string) $this->makePartial(
                'tester_failed',
                [
                    'redirect' => $redirect,
                    'message' => $e->getMessage()
                ]
            );
        }

        return $partial;
    }

    /**
     * @return array
     * @throws ModelNotFoundException
     */
    public function index_onReRun(): array
    {
        /** @var Redirect $redirect */
        $redirect = Redirect::findOrFail(post('id'));

        Flash::success(trans('adrenth.redirect::lang.test_lab.flash_test_executed'));

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
    public function index_onExclude(): array
    {
        /** @var Redirect $redirect */
        $redirect = Redirect::findOrFail(post('id'));
        $redirect->update(['test_lab' => false]);

        Flash::success(trans('adrenth.redirect::lang.test_lab.flash_redirect_excluded'));

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
    public function getTestPath(Redirect $redirect): string
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
    public function getTestResults(Redirect $redirect): array
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
    private function getRedirectCount(): int
    {
        return count($this->redirects);
    }
}

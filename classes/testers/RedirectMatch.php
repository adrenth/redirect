<?php

namespace Adrenth\Redirect\Classes\Testers;

use Adrenth\Redirect\Classes\Exceptions\InvalidScheme;
use Adrenth\Redirect\Classes\Exceptions\RulesPathNotReadable;
use Adrenth\Redirect\Classes\TesterBase;
use Adrenth\Redirect\Classes\TesterResult;
use Backend;
use Request;

/**
 * Class RedirectMatch
 *
 * @package Adrenth\Redirect\Classes\Testers
 */
class RedirectMatch extends TesterBase
{
    /**
     * {@inheritdoc}
     * @throws InvalidScheme
     */
    protected function test()
    {
        try {
            $manager = $this->getRedirectManager();
        } catch (RulesPathNotReadable $e) {
            return new TesterResult(false, $e->getMessage());
        }

        // TODO: Add scheme.
        $match = $manager->match($this->testPath, Request::getScheme());

        if ($match === false) {
            return new TesterResult(false, trans('adrenth.redirect::lang.test_lab.not_match_redirect'));
        }

        $message = sprintf(
            '%s <a href="%s" target="_blank">%s</a>.',
            trans('adrenth.redirect::lang.test_lab.matched'),
            Backend::url('adrenth/redirect/redirects/update/' . $match->getId()),
            trans('adrenth.redirect::lang.test_lab.redirect')
        );

        return new TesterResult(true, $message);
    }
}

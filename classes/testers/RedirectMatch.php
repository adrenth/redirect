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
    protected function test(): TesterResult
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

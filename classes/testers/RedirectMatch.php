<?php

namespace Adrenth\Redirect\Classes\Testers;

use Adrenth\Redirect\Classes\Exceptions\RulesPathNotReadable;
use Adrenth\Redirect\Classes\TesterBase;
use Adrenth\Redirect\Classes\TesterResult;
use Adrenth\Redirect\Models\Redirect;
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
            return new TesterResult(false, 'Did not match any redirect.');
        }

        $message = sprintf(
            'Matched <a href="%s" target="_blank">redirect</a>.',
            Backend::url('adrenth/redirect/redirects/update/' . $match->getId())
        );

        return new TesterResult(true, $message);
    }
}

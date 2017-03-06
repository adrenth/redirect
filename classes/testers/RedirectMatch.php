<?php

namespace Adrenth\Redirect\Classes\Testers;

use Adrenth\Redirect\Classes\Exceptions\RulesPathNotReadable;
use Adrenth\Redirect\Classes\Tester;
use Adrenth\Redirect\Classes\TesterResult;
use Backend;

/**
 * Class RedirectMatch
 *
 * @package Adrenth\Redirect\Classes\Testers
 */
class RedirectMatch extends Tester
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

        $match = $manager->match($this->testPath);

        if ($match === false) {
            return new TesterResult(false, 'Did not match any redirect.');
        }

        $message = sprintf(
            'Matched <a href="%s">redirect</a>.',
            Backend::url('adrenth/redirect/redirects/update/' . $match->getId())
        );

        return new TesterResult(true, $message);
    }
}

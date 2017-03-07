<?php

namespace Adrenth\Redirect\Classes\Testers;

use Adrenth\Redirect\Classes\Exceptions\RulesPathNotReadable;
use Adrenth\Redirect\Classes\TesterBase;
use Adrenth\Redirect\Classes\TesterResult;
use Adrenth\Redirect\Models\Redirect;
use Backend;

/**
 * Class ResponseCode
 *
 * Tester for checking if the response HTTP code is equal to the matched redirect.
 *
 * Situations:
 * a) Failing when given path matches a redirect but response code is not equal to response code.
 * b) Failing when given path does not match but status code is not 301, 302, ...
 * c) Passes when given path does not match with a redirect.
 *
 * @package Adrenth\Redirect\Classes\Testers
 */
class ResponseCode extends TesterBase
{
    /**
     * {@inheritdoc}
     */
    protected function test()
    {
        $curlHandle = curl_init($this->testUrl);

        $this->setDefaultCurlOptions($curlHandle);

        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, false);

        $error = null;

        if (curl_exec($curlHandle) === false) {
            $error = curl_error($curlHandle);
        }

        if ($error !== null) {
            return new TesterResult(false, 'Could not execute request.', 0);
        }

        $statusCode = (int) curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

        curl_close($curlHandle);

        try {
            $manager = $this->getRedirectManager();
        } catch (RulesPathNotReadable $e) {
            return new TesterResult(false, $e->getMessage());
        }

        $match = $manager->match($this->testPath);

        if ($match && $match->getStatusCode() !== $statusCode) {
            $message = sprintf(
                'Matched <a href="%s" target="_blank">redirect</a>, '
                    . 'but response HTTP code did not match! Expected %d but received %s.',
                Backend::url('adrenth/redirect/redirects/update/' . $match->getId()),
                $match->getStatusCode(),
                $statusCode
            );

            return new TesterResult(false, $message);
        } elseif ($match && $match->getStatusCode() === $statusCode) {
            $message = sprintf(
                'Matched <a href="%s" target="_blank">redirect</a>, response HTTP code %d.',
                Backend::url('adrenth/redirect/redirects/update/' . $match->getId()),
                $statusCode
            );

            return new TesterResult(true, $message);
        }

        // Should be a 301, 302, 303, 404, 410, ...
        if (!array_key_exists($statusCode, Redirect::$statusCodes)) {
            return new TesterResult(
                false,
                'Response HTTP code should be one of: '
                . implode(', ', array_keys(Redirect::$statusCodes))
            );
        }

        return new TesterResult(
            true,
            'Response HTTP code ' . $statusCode
        );
    }
}

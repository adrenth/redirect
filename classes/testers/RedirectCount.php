<?php

namespace Adrenth\Redirect\Classes\Testers;

use Adrenth\Redirect\Classes\TesterBase;
use Adrenth\Redirect\Classes\TesterResult;

/**
 * Class RedirectCount
 *
 * @package Adrenth\Redirect\Classes\Testers
 */
class RedirectCount extends TesterBase
{
    /**
     * {@inheritdoc}
     */
    protected function test()
    {
        $curlHandle = curl_init($this->testUrl);

        $this->setDefaultCurlOptions($curlHandle);

        $error = null;

        if (curl_exec($curlHandle) === false) {
            $error = curl_error($curlHandle);
        }

        if ($error !== null) {
            return new TesterResult(false, 'Could not execute request.', 0);
        }

        $statusCode = (int) curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        $redirectCount = (int) curl_getinfo($curlHandle, CURLINFO_REDIRECT_COUNT);

        curl_close($curlHandle);

        return new TesterResult(
            $redirectCount === 1 || $redirectCount === 0 && $statusCode > 400,
            'Number of redirects followed: ' . $redirectCount . ' (limited to 10).'
        );
    }
}

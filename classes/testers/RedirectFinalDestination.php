<?php

namespace Adrenth\Redirect\Classes\Testers;

use Adrenth\Redirect\Classes\TesterBase;
use Adrenth\Redirect\Classes\TesterResult;

/**
 * Class RedirectFinalDestination
 *
 * @package Adrenth\Redirect\Classes\Testers
 */
class RedirectFinalDestination extends TesterBase
{

    /**
     * Execute test
     *
     * @return TesterResult
     */
    protected function test()
    {
        $curlHandle = curl_init($this->testUrl);

        $this->setDefaultCurlOptions($curlHandle);

        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, false);

        $error = null;

        if (curl_exec($curlHandle) === false) {
            $error = 'Could not determine final destination URL.';
        }

        $finalDestination = curl_getinfo($curlHandle, CURLINFO_REDIRECT_URL);
        $statusCode = (int) curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

        curl_close($curlHandle);

        if (empty($finalDestination) && $statusCode > 400) {
            $message = $error === null ? 'No final destination URL.' : $error;
        } else {
            $finalDestination = sprintf('<a href="%s" target="_blank">%s</a>', $finalDestination, $finalDestination);
            $message = $error === null ? "Final destination is $finalDestination." : $error;
        }

        return new TesterResult($error === null, $message);
    }
}

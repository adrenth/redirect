<?php

namespace Adrenth\Redirect\Classes\Testers;

use Adrenth\Redirect\Classes\Tester;
use Adrenth\Redirect\Classes\TesterResult;

/**
 * Class RedirectLoop
 *
 * @package Adrenth\Redirect\Classes\Testers
 */
class RedirectLoop extends Tester
{
    /**
     * {@inheritdoc}
     */
    protected function test()
    {
        $curlHandle = curl_init($this->testUrl);

        $this->setDefaultCurlOptions($curlHandle);

        curl_setopt($curlHandle, CURLOPT_MAXREDIRS, 20);

        $error = null;

        if (curl_exec($curlHandle) === false
            && curl_errno($curlHandle) === CURLE_TOO_MANY_REDIRECTS) {
            $error = 'Possible redirect loop!';
        }

        curl_close($curlHandle);

        $message = $error === null ? 'No redirect loop detected.' : $error;

        return new TesterResult($error === null, $message);
    }
}

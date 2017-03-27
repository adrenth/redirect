<?php

namespace Adrenth\Redirect\Classes\Testers;

use Adrenth\Redirect\Classes\TesterBase;
use Adrenth\Redirect\Classes\TesterResult;

/**
 * Class RedirectLoop
 *
 * @package Adrenth\Redirect\Classes\Testers
 */
class RedirectLoop extends TesterBase
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
            $error = trans('adrenth.redirect::lang.test_lab.possible_loop');
        }

        curl_close($curlHandle);

        $message = $error === null ? trans('adrenth.redirect::lang.test_lab.no_loop') : $error;

        return new TesterResult($error === null, $message);
    }
}

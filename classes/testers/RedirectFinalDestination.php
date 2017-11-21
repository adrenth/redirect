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
    protected function test(): TesterResult
    {
        $curlHandle = curl_init($this->testUrl);

        $this->setDefaultCurlOptions($curlHandle);

        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, false);

        $error = null;

        if (curl_exec($curlHandle) === false) {
            $error = trans('adrenth.redirect::lang.test_lab.not_determinate_destination_url');
        }

        $finalDestination = curl_getinfo($curlHandle, CURLINFO_REDIRECT_URL);
        $statusCode = (int) curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

        curl_close($curlHandle);

        if (empty($finalDestination) && $statusCode > 400) {
            $message = $error ?? trans('adrenth.redirect::lang.test_lab.no_destination_url');
        } else {
            $finalDestination = sprintf('<a href="%s" target="_blank">%s</a>', $finalDestination, $finalDestination);
            $message = $error ?? trans('adrenth.redirect::lang.test_lab.final_destination_is', ['destination' => $finalDestination]);
        }

        return new TesterResult($error === null, $message);
    }
}

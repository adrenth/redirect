<?php
/**
 * October CMS plugin: Adrenth.Redirect
 *
 * Copyright (c) 2016 - 2018 Alwin Drenth
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute,
 * sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or
 * substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
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

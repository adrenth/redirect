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

use Adrenth\Redirect\Classes\Exceptions\InvalidScheme;
use Adrenth\Redirect\Classes\Exceptions\RulesPathNotReadable;
use Adrenth\Redirect\Classes\TesterBase;
use Adrenth\Redirect\Classes\TesterResult;
use Adrenth\Redirect\Models\Redirect;
use Request;

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
     * @throws InvalidScheme
     */
    protected function test(): TesterResult
    {
        $curlHandle = curl_init($this->testUrl);

        $this->setDefaultCurlOptions($curlHandle);

        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, false);

        $error = null;

        if (curl_exec($curlHandle) === false) {
            $error = curl_error($curlHandle);
        }

        if ($error !== null) {
            return new TesterResult(false, trans('adrenth.redirect::lang.test_lab.result_request_failed'), 0);
        }

        $statusCode = (int) curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

        curl_close($curlHandle);

        try {
            $manager = $this->getRedirectManager();
        } catch (RulesPathNotReadable $e) {
            return new TesterResult(false, $e->getMessage());
        }

        // TODO: Add scheme
        $match = $manager->match($this->testPath, Request::getScheme());

        if ($match && $match->getStatusCode() !== $statusCode) {
            $message = trans('adrenth.redirect::lang.test_lab.matched_not_http_code', [
                'expected' => $match->getStatusCode(),
                'received' => $statusCode
            ]);

            return new TesterResult(false, $message);
        } elseif ($match && $match->getStatusCode() === $statusCode) {
            $message = trans('adrenth.redirect::lang.test_lab.matched_http_code', [
                'code' => $statusCode,
            ]);

            return new TesterResult(true, $message);
        }

        // Should be a 301, 302, 303, 404, 410, ...
        if (!array_key_exists($statusCode, Redirect::$statusCodes)) {
            return new TesterResult(
                false,
                trans('adrenth.redirect::lang.test_lab.response_http_code_should_be')
                . ' '
                . implode(', ', array_keys(Redirect::$statusCodes))
            );
        }

        return new TesterResult(
            true,
            trans('adrenth.redirect::lang.test_lab.response_http_code') . ': ' . $statusCode
        );
    }
}

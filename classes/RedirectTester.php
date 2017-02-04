<?php

namespace Adrenth\Redirect\Classes;

use Adrenth\Redirect\Models\Redirect;
use Backend;
use Cms;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class RedirectTester
 *
 * TODO: Split into separate classes.
 *
 * @package Adrenth\Redirect\Classes
 */
class RedirectTester
{
    const MAX_REDIRECTS = 10;
    const CONNECTION_TIMEOUT = 10;

    /** @var string */
    private $testUrl;

    /** @var string */
    private $testPath;

    /**
     * @param string $testPath
     */
    public function __construct($testPath)
    {
        $this->testPath = $testPath;
        $this->testUrl = Cms::url($testPath);
    }

    /**
     * @return RedirectTestResult
     */
    public function testRedirectCount()
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start(__FUNCTION__);

        $curlHandle = curl_init($this->testUrl);

        $this->setDefaultCurlOptions($curlHandle);

        $error = null;

        if (curl_exec($curlHandle) === false) {
            $error = curl_error($curlHandle);
        }

        if ($error !== null) {
            return new RedirectTestResult(false, 'Could not execute request.', 0);
        }

        $redirectCount = (int) curl_getinfo($curlHandle, CURLINFO_REDIRECT_COUNT);

        $event = $stopwatch->stop(__FUNCTION__);

        return new RedirectTestResult(
            $redirectCount <= self::MAX_REDIRECTS,
            'Number of redirects followed: ' . $redirectCount . ' (test limited to 10)',
            $event->getDuration()
        );
    }

    /**
     * Test for checking if the response HTTP code is equal to the matched redirect.
     *
     * Situations:
     * a) Failing when given path matches a redirect but response code is not equal to response code.
     * b) Failing when given path does not match but status code is not 301, 302, ...
     * c) Passes when given path does not match with a redirect.
     *
     * @return RedirectTestResult
     * @throws Exceptions\RulesPathNotReadable
     */
    public function testResponseCode()
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start(__FUNCTION__);

        $curlHandle = curl_init($this->testUrl);

        $this->setDefaultCurlOptions($curlHandle);

        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, false);

        $error = null;

        if (curl_exec($curlHandle) === false) {
            $error = curl_error($curlHandle);
        }

        if ($error !== null) {
            return new RedirectTestResult(false, 'Could not execute request.', 0);
        }

        $statusCode = (int) curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

        curl_close($curlHandle);

        $manager = RedirectManager::createWithDefaultRulesPath();
        $match = $manager->match($this->testPath);

        $event = $stopwatch->stop(__FUNCTION__);

        if ($match && $match->getStatusCode() !== $statusCode) {
            $message = sprintf(
                'Matched <a href="%s">redirect</a>, but response HTTP code did not match! Expected %d but received %s.',
                $this->getRedirectUpdateUrl($match->getId()),
                $match->getStatusCode(),
                $statusCode
            );

            return new RedirectTestResult(false, $message, $event->getDuration());
        } elseif ($match && $match->getStatusCode() === $statusCode) {
            $message = sprintf(
                'Matched <a href="%s">redirect</a>, response HTTP code %d.',
                $this->getRedirectUpdateUrl($match->getId()),
                $statusCode
            );

            return new RedirectTestResult(true, $message, $event->getDuration());
        }

        // Should be a 301, 302, 303, 404, 410, ...
        if (!array_key_exists($statusCode, Redirect::$statusCodes)) {
            return new RedirectTestResult(
                false,
                'Response HTTP code should be one of: '
                . implode(', ', array_keys(Redirect::$statusCodes)),
                $event->getDuration()
            );
        }

        return new RedirectTestResult(
            true,
            'Response HTTP code ' . $statusCode,
            $event->getDuration()
        );
    }

    /**
     * Test for detecting a redirect loop. The threshold value for max_redirects is 20.
     * So if a 20 redirect count has been reached, the test will fail.
     *
     * @return RedirectTestResult
     */
    public function testMaxRedirects()
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start(__FUNCTION__);

        $curlHandle = curl_init($this->testUrl);

        $this->setDefaultCurlOptions($curlHandle);
        curl_setopt($curlHandle, CURLOPT_MAXREDIRS, 20);

        $error = null;

        if (curl_exec($curlHandle) === false
            && curl_errno($curlHandle) === CURLE_TOO_MANY_REDIRECTS) {
            $error = 'Possible redirect loop!';
        }

        curl_close($curlHandle);

        $event = $stopwatch->stop(__FUNCTION__);

        $message = $error === null ? 'No redirect loop detected.' : $error;

        return new RedirectTestResult($error === null, $message, $event->getDuration());
    }

    /**
     * Tests if given path matches a redirect.
     *
     * @return RedirectTestResult
     * @throws Exceptions\RulesPathNotReadable
     */
    public function testMatchRedirect()
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start(__FUNCTION__);

        $manager = RedirectManager::createWithDefaultRulesPath();
        $match = $manager->match($this->testPath);

        $event = $stopwatch->stop(__FUNCTION__);

        if ($match === false) {
            return new RedirectTestResult(false, 'Did not match any redirect.', $event->getDuration());
        }

        $message = sprintf(
            'Matched <a href="%s">redirect</a>.',
            $this->getRedirectUpdateUrl($match->getId())
        );

        return new RedirectTestResult(true, $message, $event->getDuration());
    }

    /**
     * Set default cURL options.
     *
     * @param resource $curlHandle
     * @return void
     */
    private function setDefaultCurlOptions($curlHandle)
    {
        curl_setopt($curlHandle, CURLOPT_MAXREDIRS, self::MAX_REDIRECTS);
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, self::CONNECTION_TIMEOUT);
        curl_setopt($curlHandle, CURLOPT_AUTOREFERER, true);
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);
        /** @noinspection CurlSslServerSpoofingInspection */
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);
        /** @noinspection CurlSslServerSpoofingInspection */
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYSTATUS, false);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_VERBOSE, false);
    }

    /**
     * @param int $id
     * @return string
     */
    private function getRedirectUpdateUrl($id)
    {
        return Backend::url('adrenth/redirect/redirects/update/' . $id);
    }
}

<?php

namespace Adrenth\Redirect\Classes;
use Cms;

/**
 * Class RedirectTester
 *
 * @package Adrenth\Redirect\Classes
 */
class RedirectTester
{
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

    public function testResponseCode()
    {
        // Should be a 301, 302, 303, 404, 410
    }

    public function testMaxRedirects()
    {
        $curlHandle = curl_init($this->testUrl);

        curl_setopt($curlHandle, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curlHandle, CURLOPT_AUTOREFERER, true);
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);
        /** @noinspection CurlSslServerSpoofingInspection */
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);
        /** @noinspection CurlSslServerSpoofingInspection */
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYSTATUS, false);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curlHandle, CURLOPT_VERBOSE, false);

        $error = '';

        if (curl_exec($curlHandle) === false) {
            $error = curl_error($curlHandle);
        }

        curl_close($curlHandle);

        return $error;
    }

    public function testMatchRedirect()
    {
        $manager = RedirectManager::createWithDefaultRulesPath();

        return $manager->match($this->testPath);
    }
}

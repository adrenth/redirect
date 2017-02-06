<?php

namespace Adrenth\Redirect\Classes;

use Adrenth\Redirect\Classes\Exceptions\RulesPathNotReadable;
use Cms;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class Tester
 *
 * @package Adrenth\Redirect\Classes
 */
abstract class Tester
{
    const MAX_REDIRECTS = 10;
    const CONNECTION_TIMEOUT = 10;

    /** @var string */
    protected $testUrl;

    /** @var string */
    protected $testPath;

    /**
     * @param string $testPath
     */
    public function __construct($testPath)
    {
        $this->testPath = $testPath;
        $this->testUrl = Cms::url($testPath);
    }

    /**
     * Execute the test
     *
     * @return TesterResult
     */
    final public function execute()
    {
        $stopwatch = new Stopwatch();

        $stopwatch->start(__FUNCTION__);

        $result = $this->test();

        $event = $stopwatch->stop(__FUNCTION__);

        $result->setDuration($event->getDuration());

        return $result;
    }

    /**
     * Execute test
     *
     * @return TesterResult
     */
    abstract protected function test();

    /**
     * Set default cURL options.
     *
     * @param resource $curlHandle
     * @return void
     */
    protected function setDefaultCurlOptions($curlHandle)
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
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, [
            'X-Adrenth-Redirect: Tester'
        ]);
    }

    /**
     * @return RedirectManager
     * @throws RulesPathNotReadable
     */
    protected function getRedirectManager()
    {
        $manager = RedirectManager::createWithDefaultRulesPath();

        return $manager->setLoggingEnabled(false)
            ->setStatisticsEnabled(false);
    }
}

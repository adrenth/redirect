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

namespace Adrenth\Redirect\Classes;

use Adrenth\Redirect\Classes\Contracts\Tester;
use Adrenth\Redirect\Classes\Exceptions\RulesPathNotReadable;
use Cms;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class Tester
 *
 * @package Adrenth\Redirect\Classes
 */
abstract class TesterBase implements Tester
{
    /**
     * Maximum redirects to follow.
     *
     * @var int
     */
    const MAX_REDIRECTS = 10;

    /**
     * Connection timeout in seconds.
     *
     * @var int
     */
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
     * {@inheritdoc}
     */
    final public function execute(): TesterResult
    {
        $stopwatch = new Stopwatch();

        $stopwatch->start(__FUNCTION__);

        $result = $this->test();

        $event = $stopwatch->stop(__FUNCTION__);

        $result->setDuration($event->getDuration());

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getTestPath(): string
    {
        return $this->testPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getTestUrl(): string
    {
        return $this->testUrl;
    }

    /**
     * Execute test
     *
     * @return TesterResult
     */
    abstract protected function test(): TesterResult;

    /**
     * Set default cURL options.
     *
     * @param resource $curlHandle
     * @return void
     */
    protected function setDefaultCurlOptions($curlHandle)//: void
    {
        curl_setopt($curlHandle, CURLOPT_MAXREDIRS, self::MAX_REDIRECTS);
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, self::CONNECTION_TIMEOUT);
        curl_setopt($curlHandle, CURLOPT_AUTOREFERER, true);

        // This constant is not available when open_basedir or safe_mode are enabled.
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);

        /** @noinspection CurlSslServerSpoofingInspection */
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);
        /** @noinspection CurlSslServerSpoofingInspection */
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);

        if (PHP_MAJOR_VERSION === 7) {
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYSTATUS, false);
        }

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
    protected function getRedirectManager(): RedirectManager
    {
        $manager = RedirectManager::createWithDefaultRulesPath();

        return $manager->setLoggingEnabled(false)
            ->setStatisticsEnabled(false);
    }
}

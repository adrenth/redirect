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

/**
 * Class TesterResult
 *
 * @package Adrenth\Redirect\Classes
 */
class TesterResult
{
    /** @var bool */
    private $passed;

    /** @var string */
    private $message;

    /** @var int */
    private $duration;

    /**
     * @param bool $passed
     * @param string $message
     */
    public function __construct($passed, $message)
    {
        $this->passed = $passed;
        $this->message = $message;
        $this->duration = 0;
    }

    /**
     * @return bool
     */
    public function isPassed(): bool
    {
        return $this->passed;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param int $duration
     * @return TesterResult
     */
    public function setDuration($duration): TesterResult
    {
        $this->duration = (int) $duration;
        return $this;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @return string
     */
    public function getStatusCssClass(): string
    {
        return $this->passed ? 'passed' : 'failed';
    }
}

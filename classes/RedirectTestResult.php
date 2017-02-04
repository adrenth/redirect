<?php

namespace Adrenth\Redirect\Classes;

/**
 * Class RedirectTestResult
 *
 * @package Adrenth\Redirect\Classes
 */
class RedirectTestResult
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
     * @param int $duration
     */
    public function __construct($passed, $message, $duration)
    {
        $this->passed = $passed;
        $this->message = $message;
        $this->duration = $duration;
    }

    /**
     * @return bool
     */
    public function isPassed()
    {
        return $this->passed;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }
}

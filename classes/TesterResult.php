<?php

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
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @return string
     */
    public function getStatusCssClass()
    {
        return $this->passed ? 'passed' : 'failed';
    }
}

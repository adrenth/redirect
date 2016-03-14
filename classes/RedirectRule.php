<?php

namespace Adrenth\Redirect\Classes;

/**
 * Class RedirectRule
 *
 * @package Adrenth\Redirect\Classes
 */
class RedirectRule
{
    /** @type int */
    private $id;

    /** @type string */
    private $matchType;

    /** @type string */
    private $fromUrl;

    /** @type string */
    private $toUrl;

    /** @type int */
    private $statusCode;

    /** @type array */
    private $requirements;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        // TODO Add checks
        $this->id = $attributes[0];
        $this->matchType = $attributes[1];
        $this->fromUrl = $attributes[2];
        $this->toUrl = $attributes[3];
        $this->statusCode = $attributes[4];
        $this->requirements = json_decode($attributes[5], true); // must be an array
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMatchType()
    {
        return $this->matchType;
    }

    /**
     * @return string
     */
    public function getFromUrl()
    {
        return $this->fromUrl;
    }

    /**
     * @return string
     */
    public function getToUrl()
    {
        return $this->toUrl;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getRequirements()
    {
        return $this->requirements;
    }
}

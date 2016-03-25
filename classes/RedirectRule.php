<?php

namespace Adrenth\Redirect\Classes;

use Adrenth\Redirect\Models\Redirect;

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
     * @throws \InvalidArgumentException
     */
    public function __construct(array $attributes)
    {
        if (count($attributes) !== 6
            || array_sum(array_keys($attributes)) !== 15
        ) {
            throw new \InvalidArgumentException('Invalid attributes provided');
        }

        list(
            $this->id,
            $this->matchType,
            $this->fromUrl,
            $this->toUrl,
            $this->statusCode,
            $this->requirements
        ) = $attributes;

        $this->requirements = json_decode($this->requirements, true);
    }

    /**
     * @param Redirect $model
     * @return RedirectRule
     * @throws \InvalidArgumentException
     */
    public static function createWithModel(Redirect $model)
    {
        return new self([
            $model->getAttribute('id'),
            $model->getAttribute('match_type'),
            $model->getAttribute('from_url'),
            $model->getAttribute('to_url'),
            $model->getAttribute('status_code'),
            json_encode($model->getAttribute('requirements')),
        ]);
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

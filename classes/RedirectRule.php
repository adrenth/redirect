<?php

namespace Adrenth\Redirect\Classes;

use Adrenth\Redirect\Models\Redirect;
use Carbon\Carbon;

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

    /** @type Carbon */
    private $fromDate;

    /** @type Carbon */
    private $toDate;

    /**
     * @param array $attributes
     * @throws \InvalidArgumentException
     */
    public function __construct(array $attributes)
    {
        if (count($attributes) !== 8
            || array_sum(array_keys($attributes)) !== 28
        ) {
            throw new \InvalidArgumentException('Invalid attributes provided');
        }

        list(
            $this->id,
            $this->matchType,
            $this->fromUrl,
            $this->toUrl,
            $this->statusCode,
            $this->requirements,
            $this->fromDate,
            $this->toDate,
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
            $model->getAttribute('from_date'),
            $model->getAttribute('to_date'),
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
        return (int) $this->statusCode;
    }

    /**
     * @return array
     */
    public function getRequirements()
    {
        return (array) $this->requirements;
    }

    /**
     * @return Carbon|null
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * @return Carbon|null
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * @return bool
     */
    public function isExactMatchType()
    {
        return $this->matchType === Redirect::TYPE_EXACT;
    }

    /**
     * @return bool
     */
    public function isPlaceholdersMatchType()
    {
        return $this->matchType === Redirect::TYPE_PLACEHOLDERS;
    }
}

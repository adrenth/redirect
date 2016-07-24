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
    /** @var int */
    private $id;

    /** @var string */
    private $matchType;

    /** @var string */
    private $targetType;

    /** @var string */
    private $fromUrl;

    /** @var string */
    private $toUrl;

    /** @var string */
    private $cmsPage;

    /** @var string */
    private $staticPage;

    /** @var int */
    private $statusCode;

    /** @var array */
    private $requirements;

    /** @var Carbon */
    private $fromDate;

    /** @var Carbon */
    private $toDate;

    /** @var array */
    private $placeholderMatches;

    /**
     * @param array $attributes
     * @throws \InvalidArgumentException
     */
    public function __construct(array $attributes)
    {
        if (count($attributes) !== 11
            || array_sum(array_keys($attributes)) !== 55
        ) {
            throw new \InvalidArgumentException('Invalid attributes provided');
        }

        list(
            $this->id,
            $this->matchType,
            $this->targetType,
            $this->fromUrl,
            $this->toUrl,
            $this->cmsPage,
            $this->staticPage,
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
            $model->getAttribute('target_type'),
            $model->getAttribute('from_url'),
            $model->getAttribute('to_url'),
            $model->getAttribute('cms_page'),
            $model->getAttribute('static_page'),
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
    public function getTargetType()
    {
        return $this->targetType;
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
     * @return string
     */
    public function getCmsPage()
    {
        return $this->cmsPage;
    }

    /**
     * @return string
     */
    public function getStaticPage()
    {
        return $this->staticPage;
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

    /**
     * @return array
     */
    public function getPlaceholderMatches()
    {
        return (array) $this->placeholderMatches;
    }

    /**
     * @param array $placeholderMatches
     * @return $this
     */
    public function setPlaceholderMatches(array $placeholderMatches = [])
    {
        $this->placeholderMatches = $placeholderMatches;
        return $this;
    }
}

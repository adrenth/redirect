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

use Adrenth\Redirect\Models\Redirect;
use Carbon\Carbon;
use InvalidArgumentException;

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
    private $fromScheme;

    /** @var string */
    private $toUrl;

    /** @var string */
    private $toScheme;

    /** @var string */
    private $cmsPage;

    /** @var string */
    private $staticPage;

    /** @var int */
    private $statusCode;

    /** @var array */
    private $requirements;

    /** @var Carbon|null */
    private $fromDate;

    /** @var Carbon|null */
    private $toDate;

    /** @var array */
    private $placeholderMatches;

    /**
     * @param array $attributes
     * @throws InvalidArgumentException
     */
    public function __construct(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $property = camel_case($key);

            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }

        if (empty($this->fromDate)) {
            $this->fromDate = null;
        } else {
            $this->fromDate = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                substr($this->fromDate, 0, 10) . ' 00:00:00'
            );
        }

        if (empty($this->toDate)) {
            $this->toDate = null;
        } else {
            $this->toDate = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                substr($this->toDate, 0, 10) . ' 00:00:00'
            );
        }

        $this->requirements = json_decode($this->requirements, true);
    }

    /**
     * @param Redirect $model
     * @return RedirectRule
     * @throws InvalidArgumentException
     */
    public static function createWithModel(Redirect $model): RedirectRule
    {
        $attributes = $model->getAttributes();
        $attributes['requirements'] = json_encode($model->getAttribute('requirements'));

        return new self($attributes);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * @return string
     */
    public function getMatchType(): string
    {
        return (string) $this->matchType;
    }

    /**
     * @return string
     */
    public function getTargetType(): string
    {
        return (string) $this->targetType;
    }

    /**
     * @return string
     */
    public function getFromUrl(): string
    {
        return (string) $this->fromUrl;
    }

    /**
     * @return string
     */
    public function getFromScheme(): string
    {
        return (string) $this->fromScheme;
    }

    /**
     * @return string
     */
    public function getToUrl(): string
    {
        return (string) $this->toUrl;
    }

    /**
     * @return string
     */
    public function getToScheme(): string
    {
        return (string) $this->toScheme;
    }

    /**
     * @return string
     */
    public function getCmsPage(): string
    {
        return (string) $this->cmsPage;
    }

    /**
     * @return string
     */
    public function getStaticPage(): string
    {
        return (string) $this->staticPage;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return (int) $this->statusCode;
    }

    /**
     * @return array
     */
    public function getRequirements(): array
    {
        return (array) $this->requirements;
    }

    /**
     * @return Carbon|null
     */
    public function getFromDate()//: ?Carbon
    {
        return $this->fromDate;
    }

    /**
     * @return Carbon|null
     */
    public function getToDate()//: ?Carbon
    {
        return $this->toDate;
    }

    /**
     * @return bool
     */
    public function isExactMatchType(): bool
    {
        return $this->matchType === Redirect::TYPE_EXACT;
    }

    /**
     * @return bool
     */
    public function isPlaceholdersMatchType(): bool
    {
        return $this->matchType === Redirect::TYPE_PLACEHOLDERS;
    }

    /**
     * @return array
     */
    public function getPlaceholderMatches(): array
    {
        return (array) $this->placeholderMatches;
    }

    /**
     * @param array $placeholderMatches
     * @return RedirectRule
     */
    public function setPlaceholderMatches(array $placeholderMatches = []): RedirectRule
    {
        $this->placeholderMatches = $placeholderMatches;
        return $this;
    }
}

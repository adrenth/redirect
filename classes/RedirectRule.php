<?php
/**
 * October CMS plugin: Adrenth.Redirect
 *
 * Copyright (c) 2016 - 2018 Alwin Drenth
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute,
 * sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or
 * substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
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

        try {
            $this->fromDate = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                substr($this->fromDate, 0, 10) . ' 00:00:00'
            );
        } catch (InvalidArgumentException $e) {
            $this->fromDate = null;
        }

        try {
            $this->toDate = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                substr($this->toDate, 0, 10) . ' 00:00:00'
            );
        } catch (InvalidArgumentException $e) {
            $this->toDate = null;
        }

        $this->requirements = json_decode((string) $this->requirements, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $this->requirements = [];
        }
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

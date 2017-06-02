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

namespace Adrenth\Redirect\Models;

use Adrenth\Redirect\Classes\OptionHelper;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Support\Fluent;
use Illuminate\Validation\Validator;
use October\Rain\Database\Builder;
use October\Rain\Database\Model;
use October\Rain\Database\Relations\HasMany;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;

/** @noinspection ClassOverridesFieldOfSuperClassInspection */

/**
 * Class Redirect
 *
 * @package Adrenth\Redirect\Models
 * @mixin Eloquent
 */
class Redirect extends Model
{
    use Sortable;
    use Validation {
        makeValidator as traitMakeValidator;
    }

    // Types
    public const TYPE_EXACT = 'exact';
    public const TYPE_PLACEHOLDERS = 'placeholders';

    // Target Types
    public const TARGET_TYPE_PATH_URL = 'path_or_url';
    public const TARGET_TYPE_CMS_PAGE = 'cms_page';
    public const TARGET_TYPE_STATIC_PAGE = 'static_page';
    public const TARGET_TYPE_NONE = 'none';

    // Scheme options
    public const SCHEME_HTTP = 'http';
    public const SCHEME_HTTPS = 'https';
    public const SCHEME_AUTO = 'auto';

    /** @var array */
    public static $types = [
        self::TYPE_EXACT,
        self::TYPE_PLACEHOLDERS,
    ];

    /** @var array */
    public static $targetTypes = [
        self::TARGET_TYPE_PATH_URL,
        self::TARGET_TYPE_CMS_PAGE,
        self::TARGET_TYPE_STATIC_PAGE,
        self::TARGET_TYPE_NONE,
    ];

    /** @var array */
    public static $statusCodes = [
        301 => 'permanent',
        302 => 'temporary',
        303 => 'see_other',
        404 => 'not_found',
        410 => 'gone',
    ];

    /**
     * {@inheritdoc}
     */
    public $table = 'adrenth_redirect_redirects';

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * Validation rules
     *
     * @var array
     */
    public $rules = [
        'from_url' => 'required',
        'from_scheme' => 'in:http,https,auto',
        'to_url' => 'different:from_url|required_if:target_type,path_or_url',
        'to_scheme' => 'in:http,https,auto',
        'cms_page' => 'required_if:target_type,cms_page',
        'static_page' => 'required_if:target_type,static_page',
        'match_type' => 'required|in:exact,placeholders',
        'target_type' => 'required|in:path_or_url,cms_page,static_page,none',
        'status_code' => 'required|in:301,302,303,404,410',
        'sort_order' => 'numeric',
    ];

    /**
     * Custom validation messages
     *
     * @var array
     */
    public $customMessages = [
        'to_url.required_if' => 'adrenth.redirect::lang.redirect.to_url_required_if',
        'cms_page.required_if' => 'adrenth.redirect::lang.redirect.cms_page_required_if',
        'static_page.required_if' => 'adrenth.redirect::lang.redirect.static_page_required_if',
    ];

    /**
     * {@inheritdoc}
     */
    public $jsonable = [
        'requirements',
    ];

    /**
     * Custom attribute names
     *
     * @var array
     */
    public $attributeNames = [
        'to_url' => 'adrenth.redirect::lang.redirect.to_url',
        'to_scheme' => 'adrenth.redirect::lang.redirect.to_scheme',
        'from_url' => 'adrenth.redirect::lang.redirect.from_url',
        'from_scheme' => 'adrenth.redirect::lang.redirect.to_scheme',
        'match_type' => 'adrenth.redirect::lang.redirect.match_type',
        'target_type' => 'adrenth.redirect::lang.redirect.target_type',
        'cms_page' => 'adrenth.redirect::lang.redirect.target_type_cms_page',
        'static_page' => 'adrenth.redirect::lang.redirect.target_type_static_page',
        'status_code' => 'adrenth.redirect::lang.redirect.status_code',
        'from_date' => 'adrenth.redirect::lang.scheduling.from_date',
        'to_date' => 'adrenth.redirect::lang.scheduling.to_date',
        'sort_order' => 'adrenth.redirect::lang.redirect.sort_order',
        'requirements' => 'adrenth.redirect::lang.redirect.requirements',
        'last_used_at' => 'adrenth.redirect::lang.redirect.last_used_at',
    ];

    /**
     * {@inheritdoc}
     */
    protected $dates = [
        'from_date',
        'to_date',
        'last_used_at',
    ];

    /**
     * {@inheritdoc}
     */
    public $hasMany = [
        'clients' => Client::class,
    ];

    /**
     * {@inheritdoc}
     */
    public $belongsTo = [
        'category' => Category::class,
    ];

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param array $data
     * @param array $rules
     * @param array $customMessages
     * @param array $attributeNames
     * @return Validator
     */
    protected static function makeValidator(
        array $data,
        array $rules,
        array $customMessages = [],
        array $attributeNames = []
    ): Validator {
        $validator = self::traitMakeValidator($data, $rules, $customMessages, $attributeNames);

        $validator->sometimes('to_url', 'required', function (Fluent $request) {
            return in_array($request->get('status_code'), ['301', '302', '303'], true)
            && $request->get('target_type') === self::TARGET_TYPE_PATH_URL;
        });

        $validator->sometimes('cms_page', 'required', function (Fluent $request) {
            return in_array($request->get('status_code'), ['301', '302', '303'], true)
            && $request->get('target_type') === self::TARGET_TYPE_CMS_PAGE;
        });

        $validator->sometimes('static_page', 'required', function (Fluent $request) {
            return in_array($request->get('status_code'), ['301', '302', '303'], true)
            && $request->get('target_type') === self::TARGET_TYPE_STATIC_PAGE;
        });

        return $validator;
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeEnabled(Builder $builder): Builder
    {
        return $builder->where('is_enabled', '=', true);
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeTestLabEnabled(Builder $builder): Builder
    {
        return $builder->where('test_lab', '=', true);
    }

    /**
     * @return bool
     */
    public function isMatchTypeExact(): bool
    {
        return $this->attributes['match_type'] === self::TYPE_EXACT;
    }

    /**
     * @return bool
     */
    public function isMatchTypePlaceholders(): bool
    {
        return $this->attributes['match_type'] === self::TYPE_PLACEHOLDERS;
    }

    /**
     * @return HasMany
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Mutator for 'from_url' attribute; make sure the value is URL decoded.
     *
     * @param string $value
     */
    public function setFromUrlAttribute($value): void
    {
        $this->attributes['from_url'] = urldecode($value);
    }

    /**
     * Mutator for 'sort_order' attribute; make sure the value is an integer.
     *
     * @param mixed $value
     */
    public function setSortOrderAttribute($value): void
    {
        $this->attributes['sort_order'] = (int) $value;
    }

    /**
     * FromDate mutator
     *
     * @param mixed $value
     * @return Carbon|null
     */
    public function getFromDateAttribute($value): ?Carbon
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return new Carbon($value);
    }

    /**
     * ToDate mutator
     *
     * @param mixed $value
     * @return Carbon|null
     */
    public function getToDateAttribute($value): ?Carbon
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return new Carbon($value);
    }

    /**
     * @see OptionHelper::getTargetTypeOptions()
     * @return array
     */
    public function getTargetTypeOptions(): array
    {
        return OptionHelper::getTargetTypeOptions((int) $this->getAttribute('status_code'));
    }

    /**
     * @see OptionHelper::getCmsPageOptions()
     * @return array
     */
    public function getCmsPageOptions(): array
    {
        return OptionHelper::getCmsPageOptions();
    }

    /**
     * @see OptionHelper::getStaticPageOptions()
     * @return array
     */
    public function getStaticPageOptions(): array
    {
        return OptionHelper::getStaticPageOptions();
    }

    /**
     * @see OptionHelper::getCategoryOptions()
     * @return array
     */
    public function getCategoryOptions(): array
    {
        return OptionHelper::getCategoryOptions();
    }

    /**
     * Filter options for Match Type.
     *
     * @return array
     */
    public function filterMatchTypeOptions(): array
    {
        $options = [];

        foreach (self::$types as $value) {
            $options[$value] = trans("adrenth.redirect::lang.redirect.$value");
        }

        return $options;
    }

    /**
     * Filter options for Target Type.
     *
     * @return array
     */
    public function filterTargetTypeOptions(): array
    {
        $options = [];

        foreach (self::$targetTypes as $value) {
            $options[$value] = trans("adrenth.redirect::lang.redirect.target_type_$value");
        }

        return $options;
    }

    /**
     * @return array
     */
    public function filterStatusCodeOptions(): array
    {
        $options = [];

        foreach (self::$statusCodes as $value => $message) {
            $options[$value] = trans("adrenth.redirect::lang.redirect.$message");
        }

        return $options;
    }

    /**
     * Triggered before the model is saved, either created or updated.
     * Make sure target fields are correctly set after saving.
     *
     * @return void
     */
    public function beforeSave(): void
    {
        switch ($this->getAttribute('target_type')) {
            case Redirect::TARGET_TYPE_NONE:
                $this->setAttribute('to_url', null);
                $this->setAttribute('cms_page', null);
                $this->setAttribute('static_page', null);
                $this->setAttribute('to_scheme', self::SCHEME_AUTO);
                break;
            case Redirect::TARGET_TYPE_PATH_URL:
                $this->setAttribute('cms_page', null);
                $this->setAttribute('static_page', null);
                break;
            case Redirect::TARGET_TYPE_CMS_PAGE:
                $this->setAttribute('to_url', null);
                $this->setAttribute('static_page', null);
                break;
            case Redirect::TARGET_TYPE_STATIC_PAGE:
                $this->setAttribute('to_url', null);
                $this->setAttribute('cms_page', null);
                break;
        }
    }

    /**
     * Check if this redirect is active on certain date.
     *
     * @param Carbon $date
     * @return bool
     */
    public function isActiveOnDate(Carbon $date): bool
    {
        if ($this->getAttribute('from_date') instanceof Carbon
            && $this->getAttribute('to_date') instanceof Carbon
        ) {
            return $date->between(
                $this->getAttribute('from_date'),
                $this->getAttribute('to_date')
            );
        }

        if ($this->getAttribute('from_date') instanceof Carbon
            && $this->getAttribute('to_date') === null
        ) {
            return $date->gte($this->getAttribute('from_date'));
        }

        if ($this->getAttribute('to_date') instanceof Carbon
            && $this->getAttribute('from_date') === null
        ) {
            return $date->lte($this->getAttribute('to_date'));
        }

        return true;
    }
}

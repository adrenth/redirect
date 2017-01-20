<?php

namespace Adrenth\Redirect\Models;

use Adrenth\Redirect\Classes\OptionHelper;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Support\Fluent;
use October\Rain\Database\Model;
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
    const TYPE_EXACT = 'exact';
    const TYPE_PLACEHOLDERS = 'placeholders';

    // Target Types
    const TARGET_TYPE_PATH_URL = 'path_or_url';
    const TARGET_TYPE_CMS_PAGE = 'cms_page';
    const TARGET_TYPE_STATIC_PAGE = 'static_page';

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
        'to_url' => 'different:from_url|required_if:target_type,path_or_url',
        'cms_page' => 'required_if:target_type,cms_page',
        'static_page' => 'required_if:target_type,static_page',
        'match_type' => 'required|in:exact,placeholders',
        'target_type' => 'required|in:path_or_url,cms_page,static_page',
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
        'from_url' => 'adrenth.redirect::lang.redirect.from_url',
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
    public $dates = [
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
     * @return \Illuminate\Validation\Validator
     */
    protected static function makeValidator(
        array $data,
        array $rules,
        array $customMessages = [],
        array $attributeNames = []
    ) {
        $validator = self::traitMakeValidator($data, $rules, $customMessages, $attributeNames);

        $validator->sometimes('to_url', 'required', function (Fluent $request) {
            return in_array($request->get('status_code'), ['301', '302', '303'], true)
            && $request->get('target_type') === 'path_or_url';
        });

        $validator->sometimes('cms_page', 'required', function (Fluent $request) {
            return in_array($request->get('status_code'), ['301', '302', '303'], true)
            && $request->get('target_type') === 'cms_page';
        });

        $validator->sometimes('static_page', 'required', function (Fluent $request) {
            return in_array($request->get('status_code'), ['301', '302', '303'], true)
            && $request->get('target_type') === 'static_page';
        });

        return $validator;
    }

    /**
     * @return \October\Rain\Database\Relations\HasMany
     */
    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Mutator for 'from_url' attribute; make sure the value is URL decoded.
     *
     * @param string $value
     */
    public function setFromUrlAttribute($value)
    {
        $this->attributes['from_url'] = urldecode($value);
    }

    /**
     * Mutator for 'sort_order' attribute; make sure the value is an integer.
     *
     * @param mixed $value
     */
    public function setSortOrderAttribute($value)
    {
        $this->attributes['sort_order'] = (int) $value;
    }

    /**
     * FromDate mutator
     *
     * @param mixed $value
     * @return Carbon|null
     */
    public function getFromDateAttribute($value)
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
    public function getToDateAttribute($value)
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
    public function getTargetTypeOptions()
    {
        return OptionHelper::getTargetTypeOptions();
    }

    /**
     * @see OptionHelper::getCmsPageOptions()
     * @return array
     */
    public function getCmsPageOptions()
    {
        return OptionHelper::getCmsPageOptions();
    }

    /**
     * @see OptionHelper::getStaticPageOptions()
     * @return array
     */
    public function getStaticPageOptions()
    {
        return OptionHelper::getStaticPageOptions();
    }

    /**
     * @see OptionHelper::getCategoryOptions()
     * @return array
     */
    public function getCategoryOptions()
    {
        return OptionHelper::getCategoryOptions();
    }

    /**
     * Filter options for Match Type.
     *
     * @return array
     */
    public function filterMatchTypeOptions()
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
    public function filterTargetTypeOptions()
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
    public function filterStatusCodeOptions()
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
    public function beforeSave()
    {
        switch ($this->getAttribute('target_type')) {
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
}

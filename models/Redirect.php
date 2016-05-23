<?php

namespace Adrenth\Redirect\Models;

use Adrenth\Redirect\Classes\OptionHelper;
use Carbon\Carbon;
use Illuminate\Support\Fluent;
use Model;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;

/**
 * Redirect Model
 */
class Redirect extends Model
{
    use Validation {
        makeValidator as traitMakeValidator;
    }

    use Sortable {
        setSortableOrder as traitSetSortableOrder;
    }

    // Types
    const TYPE_EXACT = 'exact';
    const TYPE_PLACEHOLDERS = 'placeholders';

    // Statuses
    const STATUS_NOT_PUBLISHED = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_CHANGED = 2;

    const TARGET_TYPE_PATH_URL = 'path_or_url';
    const TARGET_TYPE_CMS_PAGE = 'cms_page';
//    const TARGET_TYPE_STATIC_PAGE = 'static_page';

    /**
     * @type array
     */
    public static $targetTypes = [
        self::TARGET_TYPE_PATH_URL,
        self::TARGET_TYPE_CMS_PAGE,
//        self::TARGET_TYPE_STATIC_PAGE,
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
     * @type array
     */
    public $rules = [
        'from_url' => 'required',
        'to_url' => 'different:from_url|required_if:target_type,path_or_url',
        'cms_page' => 'required_if:target_type,cms_page',
        'match_type' => 'required|in:exact,placeholders',
        'target_type' => 'required|in:path_or_url,cms_page', // ,static_page
        'status_code' => 'required|in:301,302,404',
        'sort_order' => 'numeric',
    ];

    public $customMessages = [
        'to_url.required_if' => 'adrenth.redirect::lang.redirect.to_url_required_if',
        'cms_page.required_if' => 'adrenth.redirect::lang.redirect.cms_page_required_if',
    ];

    public $jsonable = [
        'requirements',
    ];

    /**
     * Custom attribute names
     *
     * @type array
     */
    public $attributeNames = [
        'to_url' => 'adrenth.redirect::lang.redirect.to_url',
        'from_url' => 'adrenth.redirect::lang.redirect.from_url',
        'match_type' => 'adrenth.redirect::lang.redirect.match_type',
        'target_type' => 'adrenth.redirect::lang.redirect.target_type',
        'cms_page' => 'adrenth.redirect::lang.redirect.target_type_cms_page',
//        'static_page' => 'adrenth.redirect::lang.redirect.target_type_static_page',
        'status_code' => 'adrenth.redirect::lang.redirect.status_code',
        'from_date' => 'adrenth.redirect::lang.scheduling.from_date',
        'to_date' => 'adrenth.redirect::lang.scheduling.to_date',
        'sort_order' => 'adrenth.redirect::lang.redirect.sort_order',
        'requirements' => 'adrenth.redirect::lang.redirect.requirements',
    ];

    /**
     * {@inheritdoc}
     */
    public $dates = [
        'from_date',
        'to_date',
    ];

    /**
     * Override the setSortableOrder of the Sortable Trait
     *
     * {@inheritdoc}
     */
    public function setSortableOrder($itemIds, $itemOrders = null)
    {
        $this->traitSetSortableOrder($itemIds, $itemOrders);

        if (!is_array($itemIds)) {
            return;
        }

        // Un-publish every touched record.
        foreach ($itemIds as $index => $id) {
            $this->newQuery()
                ->where('id', $id)
                ->update(['publish_status' => self::STATUS_CHANGED]);
        }
    }

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
            return in_array($request->get('status_code'), ['301', '302'], true)
            && $request->get('target_type') === 'path_or_url';
        });

        $validator->sometimes('cms_page', 'required', function (Fluent $request) {
            return in_array($request->get('status_code'), ['301', '302'], true)
                && $request->get('target_type') === 'cms_page';
        });

//        $validator->sometimes('static_page', 'required', function (Fluent $request) {
//            return in_array($request->get('status_code'), ['301', '302'], true)
//            && $request->get('target_type') === 'static_page';
//        });

        return $validator;
    }

    /**
     * Un-publis all records
     *
     * @return void
     */
    public static function unpublishAll()
    {
        $instance = new self;
        $instance->newQuery()
            ->where('publish_status', self::STATUS_PUBLISHED)
            ->update(['publish_status' => self::STATUS_CHANGED]);
    }

    /**
     * Before the model is saved, either created or updated.
     *
     * @return void
     */
    public function beforeSave()
    {
        $dirtyAttributes = $this->getDirty();

        if (!array_key_exists('hits', $dirtyAttributes) && $this->isDirty()) {
            $this->setAttribute('publish_status', $this->exists ? self::STATUS_CHANGED : self::STATUS_NOT_PUBLISHED);
        }
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

//    /**
//     * @see OptionHelper::getStaticPageOptions()
//     * @return array
//     */
//    public function getStaticPageOptions()
//    {
//        return OptionHelper::getStaticPageOptions();
//    }
}

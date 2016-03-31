<?php

namespace Adrenth\Redirect\Models;

use Carbon\Carbon;
use Model;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;

/**
 * Redirect Model
 */
class Redirect extends Model
{
    use Validation;
    use Sortable {
        setSortableOrder as traitSetSortableOrder;
    }

    const TYPE_EXACT = 'exact';
    const TYPE_PLACEHOLDERS = 'placeholders';

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
        'to_url' => 'required|different:from_url',
        'match_type' => 'required|in:exact,placeholders',
        'status_code' => 'required|in:301,302',
        'sort_order' => 'numeric',
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
        'test_url' => 'adrenth.redirect::lang.redirect.input_path',
        'match_type' => 'adrenth.redirect::lang.redirect.match_type',
        'status_code' => 'adrenth.redirect::lang.redirect.status_code',
        'from_date' => 'adrenth.redirect::lang.redirect.from_date',
        'to_date' => 'adrenth.redirect::lang.redirect.to_date',
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
            $this->newQuery()->where('id', $id)->update(['is_published' => 2]);
        }
    }

    /**
     * Un-publis all records
     *
     * @return void
     */
    public static function unPublishAll()
    {
        $instance = new self;
        $instance->newQuery()->where('is_published', 1)->update(['is_published' => 2]);
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
            $this->setAttribute('is_published', $this->exists ? 2 : 0);
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
}

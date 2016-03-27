<?php

namespace Adrenth\Redirect\Models;

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
        'requirements'
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
        'sort_order' => 'adrenth.redirect::lang.redirect.sort_order',
        'requirements' => 'adrenth.redirect::lang.redirect.requirements',
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
            $this->newQuery()->where('id', $id)->update(['is_published' => false]);
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
        $instance->newQuery()->where('is_published', 1)->update(['is_published' => false]);
    }

    /**
     * Before the model is saved, either created or updated.
     *
     * @return void
     */
    public function beforeSave()
    {
        if ($this->isDirty()) {
            $this->setAttribute('is_published', false);
        }
    }
}

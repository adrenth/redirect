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
    use Sortable;

    /**
     * {@inheritdoc}
     */
    public $table = 'adrenth_redirect_redirects';

    /**
     * {@inheritdoc}
     */
    protected $guarded = ['*'];

    /**
     * {@inheritdoc}
     */
    protected $fillable = [];

    /**
     * Validation rules
     *
     * @type array
     */
    public $rules = [
        'to_url' => 'required',
        'from_url' => 'required',
        'match_type' => 'required|in:exact,starts_with,ends_with,regex',
        'status_code' => 'required|in:301,302',
        'sort_order' => 'required|numeric'
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
        'status_code' => 'adrenth.redirect::lang.redirect.status_code',
        'sort_order' => 'adrenth.redirect::lang.redirect.sort_order',
    ];
}

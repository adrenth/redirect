<?php

namespace Adrenth\Redirect\Models;

use Model;

/**
 * Class Category
 *
 * @package Adrenth\Redirect\Models
 */
class Category extends Model
{
    /**
     * {@inheritdoc}
     */
    public $table = 'adrenth_redirect_categories';

    /**
     * {@inheritdoc}
     */
    protected $guarded = ['*'];

    /**
     * {@inheritdoc}
     */
    protected $fillable = [];
}

<?php

namespace Adrenth\Redirect\Models;

use Eloquent;
use October\Rain\Database\Model;

/** @noinspection ClassOverridesFieldOfSuperClassInspection */

/**
 * Class Category
 *
 * @package Adrenth\Redirect\Models
 * @mixin Eloquent
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

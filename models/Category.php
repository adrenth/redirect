<?php

namespace Adrenth\Redirect\Models;

use Adrenth\Redirect\Classes\IconList;
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

    /**
     * @return array
     */
    public function getIconOptions()
    {
        return IconList::getList();
    }
}

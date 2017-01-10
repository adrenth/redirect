<?php

namespace Adrenth\Redirect\Models;

use Eloquent;
use October\Rain\Database\Model;

/** @noinspection ClassOverridesFieldOfSuperClassInspection */

/**
 * Class Client
 *
 * @package Adrenth\Redirect\Models
 * @mixin Eloquent
 */
class Client extends Model
{
    /**
     * {@inheritdoc}
     */
    public $table = 'adrenth_redirect_clients';

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    public $belongsTo = [
        'redirect' => Redirect::class,
    ];

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;
}

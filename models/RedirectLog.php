<?php

namespace Adrenth\Redirect\Models;

use Eloquent;
use October\Rain\Database\Model;

/** @noinspection ClassOverridesFieldOfSuperClassInspection */

/**
 * Class RedirectLog
 *
 * @package Adrenth\Redirect\Models
 * @mixin Eloquent
 */
class RedirectLog extends Model
{
    /**
     * {@inheritdoc}
     */
    public $table = 'adrenth_redirect_redirect_logs';

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    public $dates = [
        'date_time',
    ];

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

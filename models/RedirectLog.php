<?php

namespace Adrenth\Redirect\Models;

use Model;

/**
 * Class RedirectLog
 *
 * @package Adrenth\Redirect\Models
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

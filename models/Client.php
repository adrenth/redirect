<?php

namespace Adrenth\Redirect\Models;

use Model;

/**
 * Client Model
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
        'redirect' => Redirect::class
    ];

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;
}

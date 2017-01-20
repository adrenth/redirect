<?php

namespace Adrenth\Redirect\Models;

use Backend\Models\ExportModel;

/** @noinspection LongInheritanceChainInspection */

/**
 * Class RedirectExport
 *
 * @package Adrenth\Redirect\Models
 */
class RedirectExport extends ExportModel
{
    /**
     * {@inheritdoc}
     */
    public $table = 'adrenth_redirect_redirects';

    /**
     * {@inheritdoc}
     */
    public function exportData($columns, $sessionKey = null)
    {
        return self::make()->get()->toArray();
    }
}

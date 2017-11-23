<?php
/**
 * OctoberCMS plugin: Adrenth.Redirect
 *
 * Copyright (c) Alwin Drenth 2017.
 *
 * Licensing information:
 * https://octobercms.com/help/license/regular
 * https://octobercms.com/help/license/extended
 * https://octobercms.com/help/license/faqs
 */

declare(strict_types=1);

namespace Adrenth\Redirect\Models;

use Backend\Models\ExportModel;
use Eloquent;

/** @noinspection LongInheritanceChainInspection */

/**
 * Class RedirectExport
 *
 * @package Adrenth\Redirect\Models
 * @mixin Eloquent
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
    public function exportData($columns, $sessionKey = null): array
    {
        return self::make()
            ->get()
            ->toArray();
    }
}

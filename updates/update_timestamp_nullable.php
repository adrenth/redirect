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

namespace Adrenth\Redirect\Updates;

use DbDongle;
use October\Rain\Database\Updates\Migration;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class UpdateTimestampsNullable
 *
 * @package Adrenth\Redirect\Updates
 */
class UpdateTimestampsNullable extends Migration
{

    public function up(): void
    {
        DbDongle::disableStrictMode();
        DbDongle::convertTimestamps('adrenth_redirect_redirects', ['created_at', 'updated_at']);
    }

    public function down(): void
    {
        // ...
    }
}

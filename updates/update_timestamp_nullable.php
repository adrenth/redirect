<?php

namespace Adrenth\Redirect\Updates;

use DbDongle;
use October\Rain\Database\Updates\Migration;

/**
 * Class UpdateTimestampsNullable
 *
 * @package Adrenth\Redirect\Updates
 */
class UpdateTimestampsNullable extends Migration
{

    public function up()
    {
        DbDongle::disableStrictMode();
        DbDongle::convertTimestamps('adrenth_redirect_redirects', ['created_at', 'updated_at']);
    }

    public function down()
    {
        // ...
    }
}

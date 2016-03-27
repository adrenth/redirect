<?php

namespace Adrenth\Redirect\Updates;

use DB;
use October\Rain\Database\Updates\Migration;

/**
 * Class ChangeTypeUrlColumnsRedirectsTable
 *
 * @package Adrenth\Redirect\Updates
 */
class ChangeTypeUrlColumnsRedirectsTable extends Migration
{
    const TABLE = 'adrenth_redirect_redirects';

    public function up()
    {
        DB::statement('ALTER TABLE ' . self::TABLE . ' MODIFY COLUMN to_url MEDIUMTEXT');
        DB::statement('ALTER TABLE ' . self::TABLE . ' MODIFY COLUMN from_url MEDIUMTEXT');
        DB::statement('ALTER TABLE ' . self::TABLE . ' MODIFY COLUMN match_type CHAR(12)');
    }

    public function down()
    {
        DB::statement('ALTER TABLE ' . self::TABLE . ' MODIFY COLUMN to_url VARCHAR(255)');
        DB::statement('ALTER TABLE ' . self::TABLE . ' MODIFY COLUMN from_url VARCHAR(255)');
    }
}

<?php namespace Adrenth\Redirect\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateRedirectImportsTable extends Migration
{

    public function up()
    {
        Schema::create('adrenth_redirect_redirect_imports', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('adrenth_redirect_redirect_imports');
    }

}

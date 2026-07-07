<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOperateursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operateurs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('intitule')->nullable();
            $table->string('intitule_fr')->nullable();
            $table->string('type_struct')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('operateurs');
    }
}

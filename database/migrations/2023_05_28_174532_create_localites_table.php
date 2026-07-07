<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLocalitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('localites', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            
            $table->bigInteger('code')->unique();
            $table->string('intitule');
            $table->string('intitule_fr');
            $table->integer('nb_pop_ansade')->nullable();
            $table->string('code_ansade')->nullable();
            $table->double('lat_loc')->nullable();
            $table->double('long_loc')->nullable();
            
            $table->bigInteger('wilaya_id')->unsigned();
            $table->bigInteger('moughata_id')->unsigned();
            $table->bigInteger('commune_id')->unsigned();
            
            $table->foreign('moughata_id')->references('id')->on('moughatas')->onDelete('cascade');
            $table->foreign('wilaya_id')->references('id')->on('wilayas')->onDelete('cascade');
            $table->foreign('commune_id')->references('id')->on('communes')->onDelete('cascade');
            $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('localites');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('code');
            $table->string('intitule');
            $table->string('intitule_fr');
            $table->bigInteger('moughata_id')->unsigned();
            $table->foreign('moughata_id')->references('id')->on('moughatas')->onDelete('cascade');
            $table->bigInteger('wilaya_id')->unsigned();
            $table->foreign('wilaya_id')->references('id')->on('wilayas')->onDelete('cascade');
            
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
        Schema::dropIfExists('communes');
    }
};

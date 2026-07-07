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
        Schema::create('infrastructures', function (Blueprint $table) {
            $table->bigIncrements('id');
            //
            $table->string('infra_publ');
            $table->string('code_infra_publ');
            $table->string('intitule_infra_publ');
            
            $table->double('long_infra_publ')->nullable();
            $table->double('lat_infra_publ')->nullable();


            $table->string('code_men')->nullable();
            

            
            $table->bigInteger('wilaya_id')->unsigned();
            $table->bigInteger('moughata_id')->unsigned();
            $table->bigInteger('commune_id')->unsigned();
            $table->bigInteger('localite_id')->unsigned();

            $table->foreign('moughata_id')->references('id')->on('moughatas')->onDelete('cascade');
            $table->foreign('wilaya_id')->references('id')->on('wilayas')->onDelete('cascade');
            $table->foreign('commune_id')->references('id')->on('communes')->onDelete('cascade');
            $table->foreign('localite_id')->references('id')->on('localites')->onDelete('cascade');
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
        Schema::dropIfExists('infrastructures');
    }
};

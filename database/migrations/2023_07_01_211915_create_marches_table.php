<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('marches', function (Blueprint $table) {
            $table->id();
            $table->string('num');
            $table->string('lot');
            $table->date('date_sign');
            $table->string('duree');
            $table->double('montant');
            $table->integer('nb_loc_adecl');
            $table->integer('nb_loc_asuiv_postfdal');
            $table->string('nb_latfam_asubv');
            $table->integer('nb_site_ims');
            $table->integer('nb_autre_cible');
            $table->integer('nb_bloc_a_const');
            $table->integer('nb_cab_a_const');
            $table->integer('nb_cab_a_rehab');
            
            $table->bigInteger('operateur_id')->unsigned();
            $table->foreign('operateur_id')->references('id')->on('operateurs')->onDelete('cascade');

            $table->bigInteger('projet_id')->unsigned();
            $table->foreign('projet_id')->references('id')->on('projets')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marches');
    }
};

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
        Schema::create('projet_partenaires', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('montant_fin');
            $table->integer('nb_loc_adecl');
            $table->integer('nb_loc_asuiv_postfdal');
            $table->string('nb_latfam_asubv');
            $table->integer('nb_site_ims');
            $table->integer('nb_ecole_cibl');
            $table->integer('nb_sante_cibl');
            $table->integer('nb_autre_cible');
            $table->integer('nb_bloc_a_const');
            $table->integer('nb_cab_a_const');
            $table->integer('nb_cab_a_rehab');
            $table->bigInteger('partenaire_id')->unsigned();

            
            $table->foreign('partenaire_id')->references('id')->on('partenaires')->onDelete('cascade');

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
        Schema::dropIfExists('projet_partenaires');
    }
};

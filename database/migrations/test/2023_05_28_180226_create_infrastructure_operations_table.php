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
        Schema::create('infrastructure_operations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('effectif_garcon')->nullable()->default(0);
            $table->bigInteger('effectif_fille')->nullable()->default(0);
            $table->bigInteger('effectif')->nullable()->default(0);

            $table->string('approv_eau')->nullable();
            $table->bigInteger('dist_approv_eau')->nullable()->default(0);

            $table->bigInteger('nbr_bloc')->nullable()->default(0);
            $table->bigInteger('type_latr_publ')->nullable()->default(0);
            //
            $table->bigInteger('nbr_cabine_exist')->nullable()->default(0);
            $table->bigInteger('nbr_cabine_fonct')->nullable()->default(0);
            //dist_cab_hom_fem
            $table->boolean('dist_cab_hom_fem')->nullable()->default(false);
            $table->bigInteger('nbr_cabine_pmr')->nullable()->default(0);
            $table->bigInteger('nbr_cabine_fem')->nullable()->default(0);
            $table->bigInteger('nbr_cabine_fem_hyg')->nullable()->default(0);

            $table->boolean('gest_dechet')->nullable()->default(false);
            $table->boolean('gest_dechet_biomed')->nullable()->default(false);



            $table->bigInteger('nbr_cabine_a_rehab')->nullable()->default(0);
            $table->bigInteger('nbr_bloc_a_const')->nullable()->default(0);
            $table->bigInteger('nbr_cabine_a_const')->nullable()->default(0);
            $table->bigInteger('an_program')->nullable()->default(0);

            $table->bigInteger('nbr_cabine_rehab')->nullable()->default(0);
            $table->bigInteger('nbr_bloc_const')->nullable()->default(0);
            $table->bigInteger('nbr_cabine_const')->nullable()->default(0);

            $table->bigInteger('an_recept')->nullable()->default(0);

            $table->bigInteger('nb_dlm')->nullable()->default(0);
            $table->bigInteger('nb_dlm_complet')->nullable()->default(0);


            $table->bigInteger('nb_dlm_incomplet')->nullable()->default(0);
            $table->bigInteger('nb_sans_dlm')->nullable()->default(0);
            $table->bigInteger('nb_dlm_instal')->nullable()->default(0);
            $table->string('latlng');
            $table->longText('remarque')->nullable()->default('');
            $table->boolean('confirmed')->default(false);
            $table->date('date_op')->nullable();
            $table->bigInteger('localite_id')->unsigned();
            $table->foreign('localite_id')->references('id')->on('localites')->onDelete('cascade');
            $table->bigInteger('wilaya_id')->unsigned();
            $table->foreign('wilaya_id')->references('id')->on('wilayas')->onDelete('cascade');
            $table->bigInteger('moughata_id')->unsigned();
            $table->foreign('moughata_id')->references('id')->on('moughatas')->onDelete('cascade');
            $table->bigInteger('commune_id')->unsigned();
            $table->foreign('commune_id')->references('id')->on('communes')->onDelete('cascade');

            $table->bigInteger('operation_id')->unsigned();
            $table->foreign('operation_id')->references('id')->on('operations')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('projet_id')->unsigned();
            $table->foreign('projet_id')->references('id')->on('projets')->onDelete('cascade');

            $table->bigInteger('operateur_id')->unsigned();
            $table->foreign('operateur_id')->references('id')->on('operateurs')->onDelete('cascade');


            $table->bigInteger('infrastructure_id')->unsigned();
            $table->foreign('infrastructure_id')->references('id')->on('infrastructures')->onDelete('cascade');


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
        Schema::dropIfExists('infrastructure_operations');
    }
};

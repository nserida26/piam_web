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
        Schema::create('localite_operations', function (Blueprint $table) {
            $table->id();
            
            $table->bigInteger('nb_tot_pop')->nullable()->default(0);
            $table->bigInteger('nbr_h_c')->nullable()->default(0);
            $table->bigInteger('nbr_f_c')->nullable()->default(0);
            $table->bigInteger('nbr_enfant_inf_5_ans')->nullable()->default(0);


            $table->bigInteger('nb_menages_comptabilises')->nullable()->default(0);
            $table->string('approv_eau')->nullable();


            $table->bigInteger('nb_tot_latr_fam')->nullable()->default(0);

            $table->bigInteger('nb_latrine_amelior')->nullable()->default(0);
            $table->bigInteger('nb_latrine_non_amelior')->nullable()->default(0);

            $table->bigInteger('nbr_menages_latr_amel_inadeq')->nullable()->default(0);
            $table->bigInteger('nbr_menages_latr_amel_adeq')->nullable()->default(0);
            $table->bigInteger('nbr_menages_latr_amel_vid_adeq_part')->nullable()->default(0);
            $table->bigInteger('nbr_menages_latr_amel_inadeq_part')->nullable()->default(0);

            $table->bigInteger('nbr_menages_ltr_end_phm')->nullable()->default(0);
            $table->bigInteger('nbr_menages_ltr_vsn')->nullable()->default(0);
            $table->bigInteger('nbr_menages_dal')->nullable()->default(0);

            $table->bigInteger('nbr_menages_dlm')->nullable()->default(0);
            $table->bigInteger('nbr_menages_dlm_complet')->nullable()->default(0);
            $table->bigInteger('nbr_menages_dlm_incomplet')->nullable()->default(0);
            $table->bigInteger('nbr_menages_sans_dlm')->nullable()->default(0);

            $table->bigInteger('nbr_latrine_realise')->nullable()->default(0);

            $table->bigInteger('nbr_latrine_autoconst')->nullable()->default(0);
            $table->bigInteger('nbr_latrine_realise_macon')->nullable()->default(0);

            $table->bigInteger('nbr_latrine_entraid')->nullable()->default(0);
            $table->bigInteger('nbr_latrine_subv')->nullable()->default(0);
            $table->bigInteger('nbr_latrine_autofin')->nullable()->default(0);

            $table->bigInteger('nbr_latrine_en_constr')->nullable()->default(0);
            
            $table->double('mont_invest_men')->nullable()->default(0);
            $table->bigInteger('nb_latr_invest_men_dispo')->nullable()->default(0);

            $table->date('date_op')->nullable();
            //

            $table->longText('remarque')->nullable()->default('');
            $table->boolean('confirmed')->default(false);

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
        Schema::dropIfExists('localite_operations');
    }
};

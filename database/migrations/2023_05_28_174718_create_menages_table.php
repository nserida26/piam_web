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
        Schema::create('menages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('nbr_h_c')->nullable()->default(0);
            $table->bigInteger('nbr_f_c')->nullable()->default(0);
            $table->bigInteger('nbr_enfant_inf_5_ans')->nullable()->default(0);
            
            $table->boolean('latr_fam')->nullable()->default(false);
            $table->boolean('latr_amel')->nullable()->default(false);
            $table->boolean('latr_non_amelior')->nullable()->default(false);
            $table->boolean('latr_amel_vid_adeq')->nullable()->default(false);
            $table->boolean('latr_amel_vid_inadeq')->nullable()->default(false);
            $table->integer('latr_amel_vid_inadeq_part')->unsigned()->nullable()->default(0);
            $table->integer('latr_amel_vid_adeq_part')->unsigned()->nullable()->default(0);

            $table->boolean('ltr_endommag')->nullable()->default(false);
            $table->boolean('latr_amelior_voisin')->nullable()->default(false);
            $table->boolean('men_dal')->nullable()->default(false);

            $table->enum('dlm', ['Sans DLM', 'DLM Incomplet','DLM Complet'])->nullable();

            $table->double('mont_invest_men')->nullable()->default(0);
            $table->boolean('invest_men_dispo')->nullable()->default(false);

            $table->boolean('latr_realise')->nullable()->default(false);
            $table->boolean('latr_auto_const')->nullable()->default(false);
            $table->boolean('latr_realise_macon')->nullable()->default(false);
            $table->boolean('latr_autofin')->nullable()->default(false);
            $table->boolean('latr_entraid')->nullable()->default(false);
            $table->boolean('latr_subv')->nullable()->default(false);
            $table->boolean('latr_en_constr')->nullable()->default(false);
          
          
            $table->string('latlng');
            $table->bigInteger('wilaya_id')->unsigned();
            $table->bigInteger('moughata_id')->unsigned();
            $table->bigInteger('commune_id')->unsigned();
            $table->bigInteger('localite_id')->unsigned();

            $table->foreign('moughata_id')->references('id')->on('moughatas')->onDelete('cascade');
            $table->foreign('wilaya_id')->references('id')->on('wilayas')->onDelete('cascade');
            $table->foreign('commune_id')->references('id')->on('communes')->onDelete('cascade');
            $table->foreign('localite_id')->references('id')->on('localites')->onDelete('cascade');
            
            $table->bigInteger('operation_id')->unsigned();
            $table->foreign('operation_id')->references('id')->on('operations')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
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
        Schema::dropIfExists('menages');
    }
};

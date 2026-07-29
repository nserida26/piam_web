<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateControleTravauxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('controle_travaux', function (Blueprint $table) {
            $table->id();

            // Niveau 1 — données générales (site)
            $table->string('intitule_site')->nullable();
            $table->string('type_etablissement')->nullable();
            $table->string('code_ansade')->nullable();
            $table->string('code_mesre')->nullable();
            $table->string('code_ms')->nullable();
            $table->string('effectif_ecole')->nullable();
            $table->string('nb_usagers')->nullable();

            // Niveau 3 — top-level aggregate fields
            $table->decimal('avancement', 5, 2)->nullable();
            $table->string('form_status')->default('draft');
            $table->boolean('confirmed')->default(false);
            $table->date('date_op')->nullable();

            // Nested/repeating sections — stored as-is (JSON), matching the
            // mobile payload's own shape (_buildPayload() in
            // controle_travaux_latrine_screen.dart) rather than flattening
            // ~150 individual fields into columns.
            $table->json('marche_travaux')->nullable();
            $table->json('marche_controle')->nullable();
            $table->json('travaux_a_realiser')->nullable();
            $table->json('personnel')->nullable();
            $table->json('equipements')->nullable();
            $table->json('materiaux')->nullable();
            $table->json('statuts')->nullable();
            $table->json('implantation')->nullable();
            $table->json('fondation')->nullable();
            $table->json('elevation')->nullable();
            $table->json('toit')->nullable();
            $table->json('enduits')->nullable();
            $table->json('menuiserie')->nullable();
            $table->json('plomberie')->nullable();
            $table->json('peinture')->nullable();
            $table->json('revetement')->nullable();
            $table->json('dlm')->nullable();
            $table->json('garde_fou')->nullable();
            $table->json('pges')->nullable();
            $table->json('mgp')->nullable();
            $table->json('appreciation')->nullable();
            $table->json('reception_technique')->nullable();
            $table->json('reception_provisoire')->nullable();

            $table->bigInteger('wilaya_id')->unsigned()->nullable();
            $table->foreign('wilaya_id')->references('id')->on('wilayas')->onDelete('cascade');
            $table->bigInteger('moughata_id')->unsigned()->nullable();
            $table->foreign('moughata_id')->references('id')->on('moughatas')->onDelete('cascade');
            $table->bigInteger('commune_id')->unsigned()->nullable();
            $table->foreign('commune_id')->references('id')->on('communes')->onDelete('cascade');
            $table->bigInteger('localite_id')->unsigned()->nullable();
            $table->foreign('localite_id')->references('id')->on('localites')->onDelete('cascade');

            $table->bigInteger('projet_id')->unsigned()->nullable();
            $table->foreign('projet_id')->references('id')->on('projets')->onDelete('cascade');
            $table->bigInteger('operateur_id')->unsigned()->nullable();
            $table->foreign('operateur_id')->references('id')->on('operateurs')->onDelete('cascade');

            $table->bigInteger('user_id')->unsigned()->nullable();
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
        Schema::dropIfExists('controle_travaux');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Opérateurs avec lesquels un facilitateur est autorisé à travailler (cochés
 * à sa création). Indépendant de son ONG d'appartenance (users.operateur_id).
 * Sans aucune ligne : pas de restriction, l'app propose tous les opérateurs.
 */
return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('user_operateurs_autorises')) {
            return;
        }

        Schema::create('user_operateurs_autorises', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('operateur_id')->unsigned();
            $table->foreign('operateur_id')->references('id')->on('operateurs')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'operateur_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_operateurs_autorises');
    }
};

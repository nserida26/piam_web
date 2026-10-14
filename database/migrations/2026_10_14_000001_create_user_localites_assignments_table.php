<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Localités attribuées à un utilisateur (facilitateur). Un utilisateur qui a
 * au moins une localité attribuée ne peut enquêter que sur ces localités ;
 * sans attribution, aucune restriction (comportement inchangé).
 *
 * Remplace l'ancienne migration 2023_06_01_120355_create_user_localites_table,
 * jamais exécutée, qui imposait une colonne operation_id.
 */
return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('user_localites')) {
            return;
        }

        Schema::create('user_localites', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('localite_id')->unsigned();
            $table->foreign('localite_id')->references('id')->on('localites')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'localite_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_localites');
    }
};

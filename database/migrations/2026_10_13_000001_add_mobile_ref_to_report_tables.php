<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Identifiant local du rapport côté application mobile. Permet de mettre à
 * jour un rapport déjà reçu (fiche modifiée puis renvoyée) au lieu de créer
 * un doublon à chaque synchronisation.
 */
return new class extends Migration
{
    private $tables = [
        'localite_operations',
        'infrastructure_operations',
        'menages',
        'controle_travaux',
    ];

    public function up()
    {
        foreach ($this->tables as $name) {
            if (Schema::hasColumn($name, 'mobile_ref')) {
                continue;
            }
            Schema::table($name, function (Blueprint $table) {
                $table->string('mobile_ref', 64)->nullable()->index();
            });
        }
    }

    public function down()
    {
        foreach ($this->tables as $name) {
            if (!Schema::hasColumn($name, 'mobile_ref')) {
                continue;
            }
            Schema::table($name, function (Blueprint $table) use ($name) {
                $table->dropIndex($name . '_mobile_ref_index');
                $table->dropColumn('mobile_ref');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Validation des fiches ménage envoyées par l'app mobile (onglet
 * « Gest. Validations > Ménages »). Même principe que localite_operations :
 * confirmed = 0 (à valider / rejeté) ou 1 (validé), avec remarque.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::table('menages', function (Blueprint $table) {
            if (!Schema::hasColumn('menages', 'confirmed')) {
                $table->boolean('confirmed')->default(0)->index();
            }
            if (!Schema::hasColumn('menages', 'remarque')) {
                $table->text('remarque')->nullable();
            }
            if (!Schema::hasColumn('menages', 'valide_par')) {
                $table->unsignedBigInteger('valide_par')->nullable();
            }
            if (!Schema::hasColumn('menages', 'valide_le')) {
                $table->timestamp('valide_le')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('menages', function (Blueprint $table) {
            foreach (['confirmed', 'remarque', 'valide_par', 'valide_le'] as $col) {
                if (Schema::hasColumn('menages', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fiche ménage : nom du père (chef) de famille, et projet / opérateur du
 * ménage pour pouvoir fusionner les ménages d'une localité en une seule
 * fiche localité (localite_operations) — voir App\Services\MenageAggregator.
 */
class AddChefAndProjetToMenages extends Migration
{
    public function up()
    {
        Schema::table('menages', function (Blueprint $table) {
            if (!Schema::hasColumn('menages', 'nom_chef_menage')) {
                $table->string('nom_chef_menage', 191)->nullable()->after('id');
            }
            if (!Schema::hasColumn('menages', 'projet_id')) {
                $table->unsignedBigInteger('projet_id')->nullable()->index();
            }
            if (!Schema::hasColumn('menages', 'operateur_id')) {
                $table->unsignedBigInteger('operateur_id')->nullable()->index();
            }
            if (!Schema::hasColumn('menages', 'date_op')) {
                $table->date('date_op')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('menages', function (Blueprint $table) {
            foreach (['nom_chef_menage', 'projet_id', 'operateur_id', 'date_op'] as $col) {
                if (Schema::hasColumn('menages', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}

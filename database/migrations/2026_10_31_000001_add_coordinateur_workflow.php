<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Rôle « Coordinateur » d'ONG :
 *  - users.operateur_id : ONG (table operateurs) de l'utilisateur ;
 *  - users.created_by   : coordinateur qui a créé le facilitateur ;
 *  - user_projets       : projets assignés à un facilitateur ;
 *  - validation coordinateur sur les rapports, avant la validation finale
 *    de l'admin (colonne existante `confirmed`).
 */
return new class extends Migration
{
    private $reportTables = ['localite_operations', 'infrastructure_operations'];

    public function up()
    {
        if (!DB::table('roles')->where('name', 'Coordinateur')->exists()) {
            DB::table('roles')->insert([
                'name' => 'Coordinateur',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'operateur_id')) {
                $table->bigInteger('operateur_id')->unsigned()->nullable()->after('status');
                $table->foreign('operateur_id')->references('id')->on('operateurs')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'created_by')) {
                $table->bigInteger('created_by')->unsigned()->nullable()->after('operateur_id');
            }
        });

        if (!Schema::hasTable('user_projets')) {
            Schema::create('user_projets', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('user_id')->unsigned();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->bigInteger('projet_id')->unsigned();
                $table->foreign('projet_id')->references('id')->on('projets')->onDelete('cascade');
                $table->timestamps();
                $table->unique(['user_id', 'projet_id']);
            });
        }

        foreach ($this->reportTables as $name) {
            Schema::table($name, function (Blueprint $table) use ($name) {
                if (!Schema::hasColumn($name, 'statut_coordinateur')) {
                    // null = en attente, 'valide', 'rejete'
                    $table->string('statut_coordinateur', 20)->nullable()->index();
                    $table->bigInteger('coordinateur_id')->unsigned()->nullable();
                    $table->timestamp('coordinateur_valide_le')->nullable();
                    $table->text('coordinateur_remarque')->nullable();
                }
            });
        }
    }

    public function down()
    {
        foreach ($this->reportTables as $name) {
            Schema::table($name, function (Blueprint $table) use ($name) {
                if (Schema::hasColumn($name, 'statut_coordinateur')) {
                    $table->dropIndex($name . '_statut_coordinateur_index');
                    $table->dropColumn(['statut_coordinateur', 'coordinateur_id', 'coordinateur_valide_le', 'coordinateur_remarque']);
                }
            });
        }

        Schema::dropIfExists('user_projets');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'operateur_id')) {
                $table->dropForeign(['operateur_id']);
                $table->dropColumn('operateur_id');
            }
            if (Schema::hasColumn('users', 'created_by')) {
                $table->dropColumn('created_by');
            }
        });
    }
};

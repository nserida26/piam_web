<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Photo de la latrine du ménage (chemin relatif dans public/uploads,
 * voir App\Services\MenagePhoto).
 *
 * php artisan migrate --path=database/migrations/2026_11_07_000001_add_photo_to_menages.php --force
 */
class AddPhotoToMenages extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('menages', 'photo')) {
            Schema::table('menages', function (Blueprint $table) {
                $table->string('photo', 191)->nullable();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('menages', 'photo')) {
            Schema::table('menages', function (Blueprint $table) {
                $table->dropColumn('photo');
            });
        }
    }
}

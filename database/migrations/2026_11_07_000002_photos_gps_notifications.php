<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * - report_photos : plusieurs photos par rapport (ménage, localité,
 *   infrastructure), voir App\Services\ReportPhotos ;
 * - latlng (position GPS du facilitateur) sur localite_operations et
 *   infrastructure_operations (menages l'a déjà) ;
 * - user_notifications : notifications du facilitateur (rapport rejeté…),
 *   voir App\Services\Notifier.
 *
 * php artisan migrate --path=database/migrations/2026_11_07_000002_photos_gps_notifications.php --force
 */
class PhotosGpsNotifications extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('report_photos')) {
            Schema::create('report_photos', function (Blueprint $table) {
                $table->id();
                $table->string('report_table', 40);
                $table->unsignedBigInteger('report_id');
                $table->string('path', 191);
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
                $table->index(['report_table', 'report_id']);
            });
        }

        foreach (['localite_operations', 'infrastructure_operations'] as $t) {
            if (!Schema::hasColumn($t, 'latlng')) {
                Schema::table($t, function (Blueprint $table) {
                    $table->string('latlng', 100)->nullable();
                });
            }
        }

        if (!Schema::hasTable('user_notifications')) {
            Schema::create('user_notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('type', 30)->default('rejet');
                $table->string('titre', 191);
                $table->text('message');
                $table->string('report_table', 40)->nullable();
                $table->unsignedBigInteger('report_id')->nullable();
                $table->string('mobile_ref', 191)->nullable();
                $table->unsignedBigInteger('par_user_id')->nullable();
                $table->timestamp('lu_le')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('report_photos');
        foreach (['localite_operations', 'infrastructure_operations'] as $t) {
            if (Schema::hasColumn($t, 'latlng')) {
                Schema::table($t, function (Blueprint $table) {
                    $table->dropColumn('latlng');
                });
            }
        }
    }
}

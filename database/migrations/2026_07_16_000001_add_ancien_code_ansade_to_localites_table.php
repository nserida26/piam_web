<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('localites', function (Blueprint $table) {
            $table->string('ancien_code_ansade')->nullable()->after('code_ansade');
        });
    }

    public function down()
    {
        Schema::table('localites', function (Blueprint $table) {
            $table->dropColumn('ancien_code_ansade');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projet_wilayas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('wilaya_id')->unsigned();
            $table->foreign('wilaya_id')->references('id')->on('wilayas')->onDelete('cascade');

            $table->bigInteger('projet_id')->unsigned();
            $table->foreign('projet_id')->references('id')->on('projets')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projet_wilayas');
    }
};

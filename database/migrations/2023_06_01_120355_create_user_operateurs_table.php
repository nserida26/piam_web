<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_operateurs', function (Blueprint $table) {
            $table->id();
            $table->date('date_valid');
            $table->bigInteger('operateur_id')->unsigned();
            $table->foreign('operateur_id')->references('id')->on('operateurs')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('operation_id')->unsigned();
            $table->foreign('operation_id')->references('id')->on('operations')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_operateurs');
    }
};

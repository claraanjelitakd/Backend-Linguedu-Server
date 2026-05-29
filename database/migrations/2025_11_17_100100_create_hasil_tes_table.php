<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('hasil_tes', function (Blueprint $table) {
            $table->id('id_hasil');
            $table->unsignedBigInteger('id_kuis');
            $table->unsignedBigInteger('id_member');
            $table->unsignedBigInteger('id_admin');
            $table->unsignedBigInteger('id_course');
            $table->integer('skor');
            $table->date('tanggal');
            $table->text('desc')->nullable();
            $table->timestamps();

            $table->foreign('id_kuis')->references('id_kuis')->on('kuis');
            $table->foreign('id_member')->references('id_member')->on('member');
            $table->foreign('id_admin')->references('id_admin')->on('admin');
            $table->foreign('id_course')->references('id_course')->on('kursus');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_tes');
    }
};

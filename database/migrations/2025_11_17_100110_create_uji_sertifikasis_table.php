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
        Schema::create('uji_sertifikasi', function (Blueprint $table) {
            $table->id('kode_tes');
            $table->unsignedBigInteger('id_member');
            $table->unsignedBigInteger('id_materi');
            $table->unsignedBigInteger('id_course');
            $table->unsignedBigInteger('id_admin');
            $table->date('tgl');
            $table->integer('skor');
            $table->timestamps();

            $table->foreign('id_member')->references('id_member')->on('member');
            $table->foreign('id_materi')->references('id_materi')->on('materi');
            $table->foreign('id_course')->references('id_course')->on('kursus');
            $table->foreign('id_admin')->references('id_admin')->on('admin');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uji_sertifikasis');
    }
};

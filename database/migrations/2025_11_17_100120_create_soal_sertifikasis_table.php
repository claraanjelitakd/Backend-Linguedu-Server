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
        Schema::create('soal_sertifikasi', function (Blueprint $table) {
            $table->id('id_soal');
            $table->unsignedBigInteger('kode_tes');
            $table->text('pertanyaan');
            $table->string('jawaban_benar');
            $table->timestamps();

            $table->foreign('kode_tes')->references('kode_tes')->on('uji_sertifikasi');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soal_sertifikasis');
    }
};

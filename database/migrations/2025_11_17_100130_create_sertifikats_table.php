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
        Schema::create('sertifikat', function (Blueprint $table) {
            $table->id('kode_hasil_sertif');
            $table->unsignedBigInteger('id_admin');
            $table->unsignedBigInteger('id_course');
            $table->unsignedBigInteger('id_member');
            $table->unsignedBigInteger('kode_tes');
            $table->string('format');
            $table->timestamps();

            $table->foreign('id_admin')->references('id_admin')->on('admin');
            $table->foreign('id_course')->references('id_course')->on('kursus');
            $table->foreign('id_member')->references('id_member')->on('member');
            $table->foreign('kode_tes')->references('kode_tes')->on('uji_sertifikasi');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sertifikats');
    }
};

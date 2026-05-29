<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hasil_sertifikasi', function (Blueprint $table) {
            $table->bigIncrements('id_hasil');

            $table->unsignedBigInteger('kode_tes');   // FK ke uji_sertifikasi
            $table->unsignedBigInteger('id_member');  // FK ke users (member)
            $table->unsignedBigInteger('id_course');  // FK ke kursus

            $table->integer('skor');                  // nilai 0–100 untuk user
            $table->date('tanggal');
            $table->string('status')->nullable();     // "Lulus" / "Tidak lulus"

            $table->timestamps();

            // Foreign key (opsional, tapi bagus kalau ada)
            $table->foreign('kode_tes')
                ->references('kode_tes')
                ->on('uji_sertifikasi')
                ->onDelete('cascade');

            $table->foreign('id_course')
                ->references('id_course')
                ->on('kursus')
                ->onDelete('cascade');

            // sesuaikan kalau id member bukan di tabel users
            $table->foreign('id_member')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_sertifikasi');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('template_sertifikat', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_course');

            $table->string('judul');                 // title sertifikat
            $table->text('deskripsi')->nullable();   // isi teks sertifikat
            $table->string('nama_penandatangan');    // nama di ttd
            $table->string('jabatan_penandatangan')->nullable(); // jabatan di bawah ttd

            $table->timestamps();

            $table->foreign('id_course')
                ->references('id_course')
                ->on('kursus')
                ->onDelete('cascade');

            // Satu template per course
            $table->unique('id_course');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_sertifikat');
    }
};

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
        Schema::create('teori', function (Blueprint $table) {
            // primary key
            $table->id(); // kolom "id" bigserial

            // relasi ke tabel materi (kolom pk-nya id_materi)
            $table->unsignedBigInteger('id_materi');

            // isi teori
            $table->text('overview')->nullable();
            $table->text('kenapa_penting')->nullable();
            $table->text('konsep_dasar')->nullable();
            $table->text('contoh_praktik')->nullable();
            $table->text('ringkasan')->nullable();

            $table->timestamps();

            // foreign key ke tabel materi
            $table->foreign('id_materi')
                ->references('id_materi')   // pk di tabel materi
                ->on('materi')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teori');
    }
};

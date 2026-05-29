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
        Schema::create('riwayat_materis', function (Blueprint $table) {
            $table->id();
            // Pakai bigInteger biar aman, sesuaikan dengan tipe data ID di tabel user/materi
            $table->unsignedBigInteger('id_member');
            $table->unsignedBigInteger('id_materi');

            $table->boolean('is_completed')->default(false); // Udah baca/nonton?
            $table->boolean('has_passed_quiz')->default(false); // Udah lulus kuis?

            $table->timestamps();

            // Foreign Keys (Opsional tapi bagus)
            // Pastikan nama tabel referensinya benar ('member' dan 'materi')
            $table->foreign('id_member')->references('id_member')->on('member')->onDelete('cascade');
            $table->foreign('id_materi')->references('id_materi')->on('materi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_materis');
    }
};

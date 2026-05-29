<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('soal_sertifikasi', function (Blueprint $table) {
            $table->text('opsi_a')->nullable();
            $table->text('opsi_b')->nullable();
            $table->text('opsi_c')->nullable();
            $table->text('opsi_d')->nullable();
            // jawaban_benar nanti diisi A/B/C/D
        });
    }

    public function down(): void
    {
        Schema::table('soal_sertifikasi', function (Blueprint $table) {
            $table->dropColumn(['opsi_a', 'opsi_b', 'opsi_c', 'opsi_d']);
        });
    }
};

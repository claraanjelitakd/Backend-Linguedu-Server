<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riwayat_materis', function (Blueprint $table) {
            // 1. Lepas FK lama yang ke tabel member
            $table->dropForeign('riwayat_materis_id_member_foreign');

            // 2. Pasang FK baru: id_member -> users.id
            $table->foreign('id_member')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_materis', function (Blueprint $table) {
            // Balik lagi ke versi lama kalau di-rollback
            $table->dropForeign('riwayat_materis_id_member_foreign');

            $table->foreign('id_member')
                ->references('id_member')
                ->on('member')
                ->onDelete('cascade');
        });
    }
};

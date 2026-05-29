<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('uji_sertifikasi', function (Blueprint $table) {
            // Hapus foreign key dulu
            $table->dropForeign(['id_member']);
            $table->dropForeign(['id_admin']);

            // Hapus kolom
            $table->dropColumn(['id_member', 'id_admin']);
        });
    }

    public function down(): void
    {
        Schema::table('uji_sertifikasi', function (Blueprint $table) {
            // Balikkan lagi kalau rollback
            $table->unsignedBigInteger('id_member');
            $table->unsignedBigInteger('id_admin');

            $table->foreign('id_member')
                ->references('id_member')
                ->on('member');

            $table->foreign('id_admin')
                ->references('id_admin')
                ->on('admin');
        });
    }
};

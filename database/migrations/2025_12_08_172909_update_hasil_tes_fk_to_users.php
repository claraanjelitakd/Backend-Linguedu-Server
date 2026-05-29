<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hasil_tes', function (Blueprint $table) {
            // Lepas FK lama yang ke tabel member
            $table->dropForeign('hasil_tes_id_member_foreign');

            // Pasang FK baru: id_member → users.id
            $table->foreign('id_member')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('hasil_tes', function (Blueprint $table) {
            // Balikin lagi ke versi lama (kalau di-rollback)
            $table->dropForeign('hasil_tes_id_member_foreign');

            $table->foreign('id_member')
                ->references('id_member')
                ->on('member')
                ->onDelete('cascade');
        });
    }
};

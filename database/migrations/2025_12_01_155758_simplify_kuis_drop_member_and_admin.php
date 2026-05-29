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
        Schema::table('kuis', function (Blueprint $table) {
            // Lepas foreign key dulu
            $table->dropForeign('kuis_id_admin_foreign');
            $table->dropForeign('kuis_id_member_foreign');

            // Hapus kolomnya
            $table->dropColumn(['id_admin', 'id_member']);
        });
    }

    public function down(): void
    {
        Schema::table('kuis', function (Blueprint $table) {
            // Balikin kolom, kalau suatu saat rollback
            $table->unsignedBigInteger('id_admin');
            $table->unsignedBigInteger('id_member');

            // Balikin FK ke tabel lama (sesuai skema awal kamu)
            $table->foreign('id_admin')
                ->references('id_admin')->on('admin');

            $table->foreign('id_member')
                ->references('id_member')->on('member');
        });
    }
};

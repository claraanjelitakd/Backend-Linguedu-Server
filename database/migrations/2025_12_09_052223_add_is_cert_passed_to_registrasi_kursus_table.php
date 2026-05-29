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
        Schema::table('registrasi_kursus', function (Blueprint $table) {
            if (!Schema::hasColumn('registrasi_kursus', 'is_cert_passed')) {
                $table->boolean('is_cert_passed')
                    ->default(false)
                    ->after('last_unlocked_level'); // sesuaikan kalau kolom ini beda
            }
        });
    }

    public function down(): void
    {
        Schema::table('registrasi_kursus', function (Blueprint $table) {
            if (Schema::hasColumn('registrasi_kursus', 'is_cert_passed')) {
                $table->dropColumn('is_cert_passed');
            }
        });
    }
};

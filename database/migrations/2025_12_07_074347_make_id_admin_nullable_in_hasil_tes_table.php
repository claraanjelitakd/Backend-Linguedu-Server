<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE hasil_tes ALTER COLUMN id_admin DROP NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan jadi WAJIB ISI (Not Null) kalau di-rollback
        // Hati-hati, ini bisa error kalau datanya udah ada yang null
        DB::statement('ALTER TABLE hasil_tes ALTER COLUMN id_admin SET NOT NULL');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1) Hapus kolom id_materi (karena sudah tidak dipakai)
        DB::statement('ALTER TABLE uji_sertifikasi DROP COLUMN IF EXISTS id_materi');

        // 2) Tambah kolom kkm (kalau belum ada)
        DB::statement('ALTER TABLE uji_sertifikasi ADD COLUMN IF NOT EXISTS kkm integer');

        // 3) Pindahkan nilai dari skor ke kkm kalau sebelumnya sudah ada data
        if (self::columnExists('uji_sertifikasi', 'skor')) {
            DB::statement('UPDATE uji_sertifikasi SET kkm = skor WHERE skor IS NOT NULL');
            // 4) Hapus kolom skor
            DB::statement('ALTER TABLE uji_sertifikasi DROP COLUMN IF EXISTS skor');
        }

        // Set default KKM 70 kalau masih null
        DB::statement('UPDATE uji_sertifikasi SET kkm = 70 WHERE kkm IS NULL');
    }

    public function down()
    {
        // Rollback kasar (jarang dipakai, tapi tetap disiapkan)
        // Tambah skor
        DB::statement('ALTER TABLE uji_sertifikasi ADD COLUMN IF NOT EXISTS skor integer');
        DB::statement('UPDATE uji_sertifikasi SET skor = kkm WHERE kkm IS NOT NULL');

        // Hapus kkm
        DB::statement('ALTER TABLE uji_sertifikasi DROP COLUMN IF EXISTS kkm');

        // Tambah id_materi lagi (nullable biar tidak error kalau sudah ada data)
        DB::statement('ALTER TABLE uji_sertifikasi ADD COLUMN IF NOT EXISTS id_materi bigint NULL');
    }

    private static function columnExists(string $table, string $column): bool
    {
        $result = DB::select("
            SELECT column_name
            FROM information_schema.columns
            WHERE table_name = ? AND column_name = ?
        ", [$table, $column]);

        return !empty($result);
    }
};

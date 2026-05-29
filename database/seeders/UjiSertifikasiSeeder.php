<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UjiSertifikasi;

class UjiSertifikasiSeeder extends Seeder
{
    public function run(): void
    {
        UjiSertifikasi::create([
            'id_member' => 1,
            'id_materi' => 1,
            'id_course' => 1,
            'id_admin' => 1,
            'tgl' => now(),
            'skor' => 90,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sertifikat;

class SertifikatSeeder extends Seeder
{
    public function run(): void
    {
        Sertifikat::create([
            'id_admin' => 1,
            'id_course' => 1,
            'id_member' => 1,
            'kode_tes' => 1,
            'format' => 'PDF',
        ]);
    }
}

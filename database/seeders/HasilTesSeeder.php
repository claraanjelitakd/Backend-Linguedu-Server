<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HasilTes;

class HasilTesSeeder extends Seeder
{
    public function run(): void
    {
        HasilTes::create([
            'id_kuis' => 1,
            'id_member' => 1,
            'id_admin' => 1,
            'id_course' => 1,
            'skor' => 100,
            'tanggal' => now(),
            'desc' => 'Lulus dengan nilai sempurna'
        ]);
    }
}

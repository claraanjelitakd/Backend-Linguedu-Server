<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SoalKuis;

class SoalKuisSeeder extends Seeder
{
    public function run(): void
    {
        SoalKuis::create([
            'id_kuis' => 1,
            'pertanyaan' => 'What is the capital of England?',
            'jawaban_bnr' => 'London',
        ]);
    }
}

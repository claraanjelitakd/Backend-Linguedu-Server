<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BahasaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bahasa')->insert([
            [
                'nama_bahasa' => 'Bahasa Inggris',
                'desc' => 'Grammar kompleks, writing & speaking profesional.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_bahasa' => 'Bahasa Jepang',
                'desc' => 'Latihan kanji, tata bahasa, dan percakapan sehari-hari.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_bahasa' => 'Bahasa Korea',
                'desc' => 'Struktur kalimat, honorifik, dan percakapan budaya pop.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

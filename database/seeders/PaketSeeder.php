<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Paket;

class PaketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Paket::insert([
            [
                'nama_paket' => 'Basic',
                'desc'       => '8x modul video interaktif, latihan soal setiap bab, sertifikat A1–A2, akses 3 bulan',
                'harga'      => 150000,
            ],
            [
                'nama_paket' => 'Intermediate',
                'desc'       => '12x modul terstruktur + challenge, sertifikat B1–B2, akses 6 bulan',
                'harga'      => 300000,
            ],
            [
                'nama_paket' => 'Advanced + Sertifikasi',
                'desc'       => '20x modul advanced + tes akhir, sertifikat C1–C2, akses 1 tahun',
                'harga'      => 500000,
            ],
        ]);
    }
}

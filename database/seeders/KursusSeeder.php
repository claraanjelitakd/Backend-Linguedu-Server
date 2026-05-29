<?php

namespace Database\Seeders;

use App\Models\Bahasa;
use App\Models\Kursus;
use App\Models\Paket;
use Illuminate\Database\Seeder;

class KursusSeeder extends Seeder
{
    public function run(): void
    {
        $bahasaMap = Bahasa::pluck('id', 'nama_bahasa');   // ['Inggris' => 1, ...]
        $paketMap  = Paket::pluck('id', 'nama_paket');     // ['Basic' => 1, ...]

        if ($bahasaMap->isEmpty() || $paketMap->isEmpty()) {
            dump('Seeder Kursus: tabel bahasa/paket masih kosong');
            return;
        }

        // ✏️ SESUAIKAN nama2 di bawah dengan isi BahasaSeeder & PaketSeeder-mu
        $data = [
            [
                'bahasa'    => 'Inggris',
                'paket'     => 'Basic',
                'deskripsi' => 'Bahasa Inggris Basic',
            ],
            [
                'bahasa'    => 'Inggris',
                'paket'     => 'Intermediate',
                'deskripsi' => 'Bahasa Inggris Intermediate',
            ],
            [
                'bahasa'    => 'Jepang',
                'paket'     => 'Intermediate',
                'deskripsi' => 'Bahasa Jepang Intermediate',
            ],
            [
                'bahasa'    => 'Korea',
                'paket'     => 'Intermediate',
                'deskripsi' => 'Bahasa Korea Intermediate',
            ],
        ];

        foreach ($data as $row) {
            $idBahasa = $bahasaMap[$row['bahasa']] ?? null;
            $idPaket  = $paketMap[$row['paket']] ?? null;

            if (!$idBahasa || !$idPaket) {
                dump("Skip kursus '{$row['deskripsi']}' (bahasa/paket belum ada di master)");
                continue;
            }

            Kursus::firstOrCreate(
                [
                    'id_bahasa' => $idBahasa,
                    'id_paket'  => $idPaket,
                ],
                [
                    'deskripsi' => $row['deskripsi'],
                ],
            );
        }
    }
}

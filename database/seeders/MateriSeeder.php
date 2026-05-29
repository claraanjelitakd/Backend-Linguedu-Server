<?php

namespace Database\Seeders;

use App\Models\Kursus;
use App\Models\Materi;
use Illuminate\Database\Seeder;

class MateriSeeder extends Seeder
{
    public function run(): void
    {
        // Cari kursus Inggris + Intermediate
        $inggrisIntermediate = Kursus::whereHas('bahasa', function ($q) {
                $q->where('nama_bahasa', 'Inggris');
            })
            ->whereHas('paket', function ($q) {
                $q->where('nama_paket', 'Intermediate');  // sesuaikan nama
            })
            ->first();

        if (!$inggrisIntermediate) {
            dump("MateriSeeder: kursus Inggris Intermediate belum ada");
            return;
        }

        Materi::create([
            'id_course'  => $inggrisIntermediate->id_course,
            'judul'      => 'Introduction to English',
            'tipe'       => 'video',
            'url_video'  => 'https://example.com/video1',
            'teks_teori' => 'Dasar dasar pengenalan bahasa Inggris.',
        ]);

        Materi::create([
            'id_course'  => $inggrisIntermediate->id_course,
            'judul'      => 'Basic Grammar',
            'tipe'       => 'teori',
            'url_video'  => null,
            'teks_teori' => 'Penjelasan dasar grammar.',
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\RegistrasiKursus;
use App\Models\User;
use App\Models\Kursus;
use Illuminate\Database\Seeder;

class RegistrasiKursusSeeder extends Seeder
{
    public function run(): void
    {
        // cari satu admin & satu member
        $admin = User::where('role', 'admin')->first();
        $member = User::where('role', 'member')->first();

        // misalnya ambil kursus pertama saja
        $course = Kursus::with('paket')->first();

        if (!$admin || !$member || !$course) {
            dump('RegistrasiKursusSeeder: admin/member/course belum siap, cek seeders lain');
            return;
        }

        RegistrasiKursus::create([
            'id_admin' => $admin->id,
            'id_member' => $member->id,
            'id_course' => $course->id_course,
            'tgl_trans' => now()->toDateString(),
            'metode_bayar' => 'Transfer',
            'total_byr' => $course->paket->harga ?? 250000,
            'bukti_byr' => null,       // bisa diisi string path kalau mau contoh
            'progress' => 0,
            'level' => $course->paket->nama_paket ?? 'Beginner',
        ]);
    }
}

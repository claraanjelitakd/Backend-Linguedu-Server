<?php

namespace Database\Seeders;

use App\Models\Kuis;
use App\Models\Materi;
use App\Models\Kursus;
use App\Models\User;
use Illuminate\Database\Seeder;

class KuisSeeder extends Seeder
{
    public function run(): void
    {
        $member = User::where('role', 'member')->first();
        $admin  = User::where('role', 'admin')->first();
        $course = Kursus::first();
        $materi = Materi::first();

        if (!$member || !$admin || !$course || !$materi) {
            dump('KuisSeeder: member/admin/course/materi belum siap');
            return;
        }

        Kuis::create([
            'id_member' => $member->id,
            'id_materi' => $materi->id_materi,
            'id_course' => $course->id_course,
            'id_admin'  => $admin->id,
        ]);
    }
}

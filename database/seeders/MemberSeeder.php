<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        Member::create([
            'email' => 'member1@gmail.com',
            'username' => 'member01',
            'password' => bcrypt('password123'),
        ]);
    }
}

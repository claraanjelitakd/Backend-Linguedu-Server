<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AdminSeeder::class,
            BahasaSeeder::class, // Harus di atas
            PaketSeeder::class,  // Harus di atas
            KursusSeeder::class, // Baru ini
            MemberSeeder::class,
            MateriSeeder::class,
            // SoalKuisSeeder::class,
            // HasilTesSeeder::class,
            // UjiSertifikasiSeeder::class,
            // SoalSertifikasiSeeder::class,
            // SertifikatSeeder::class,
        ]);
    }

}

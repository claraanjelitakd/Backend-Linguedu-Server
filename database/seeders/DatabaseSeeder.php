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
        MemberSeeder::class,
        BahasaSeeder::class,
        PaketSeeder::class,
        KursusSeeder::class,
        MateriSeeder::class,
        RegistrasiKursusSeeder::class,
        KuisSeeder::class,
        SoalKuisSeeder::class,
        HasilTesSeeder::class,
        UjiSertifikasiSeeder::class,
        SoalSertifikasiSeeder::class,
        SertifikatSeeder::class,
    ]);
}

}

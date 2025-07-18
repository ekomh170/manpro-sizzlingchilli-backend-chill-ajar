<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AdminSeeder::class,
            MentorSeeder::class,
            PelangganSeeder::class,
            KursusSeeder::class,
            JadwalKursusSeeder::class,
            SesiSeeder::class,
            TransaksiSeeder::class,
            TestimoniSeeder::class,
        ]);
    }
}

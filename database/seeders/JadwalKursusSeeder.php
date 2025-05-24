<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalKursus;
use App\Models\Kursus;

class JadwalKursusSeeder extends Seeder
{
    public function run()
    {
        $kursusIds = Kursus::pluck('id')->toArray();
        for ($i = 1; $i <= 20; $i++) {
            JadwalKursus::create([
                'kursus_id' => $kursusIds[array_rand($kursusIds)],
                'tanggal' => now()->addDays(rand(1, 30)),
                'waktu' => sprintf('%02d:00:00', rand(8, 18)),
                'keterangan' => 'Jadwal dummy ke-' . $i,
            ]);
        }
    }
}

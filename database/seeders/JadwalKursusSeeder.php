<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalKursus;
use App\Models\Kursus;

class JadwalKursusSeeder extends Seeder
{
    public function run()
    {
        $kursusMentor = Kursus::with('mentor')->get();
        $mentorKursus = [];
        foreach ($kursusMentor as $k) {
            if ($k->mentor && $k->mentor->user) {
                $mentorKursus[$k->mentor->user_id][] = $k->id;
            }
        }

        // Untuk setiap mentor, buat 2-3 jadwal di tanggal yang sama tapi jam berbeda
        foreach ($mentorKursus as $userId => $kursusIds) {
            $tanggal = now()->addDays(rand(1, 10))->format('Y-m-d');
            $usedHours = [];
            $jumlahJadwal = rand(2, 3);
            for ($j = 0; $j < $jumlahJadwal; $j++) {
                $jam = rand(8, 18);
                while (in_array($jam, $usedHours)) {
                    $jam = rand(8, 18);
                }
                $usedHours[] = $jam;
                JadwalKursus::create([
                    'kursus_id' => $kursusIds[array_rand($kursusIds)],
                    'tanggal' => $tanggal,
                    'waktu' => sprintf('%02d:00:00', $jam),
                    'keterangan' => 'Jadwal mentor user_id ' . $userId,
                    'tempat' => 'Ruang A - Kampus Pusat, Lantai 2', // Contoh default tempat
                ]);
            }
        }

        // Jadwal random lain (jadwal dummy umum)
        $kursusIds = Kursus::pluck('id')->toArray();
        for ($i = 1; $i <= 10; $i++) {
            JadwalKursus::create([
                'kursus_id' => $kursusIds[array_rand($kursusIds)],
                'tanggal' => now()->addDays(rand(11, 30)),
                'waktu' => sprintf('%02d:00:00', rand(8, 18)),
                'keterangan' => 'Jadwal dummy ke-' . $i,
                'tempat' => 'Ruang A - Kampus Pusat, Lantai 2', // Contoh default tempat
            ]);
        }
    }
}

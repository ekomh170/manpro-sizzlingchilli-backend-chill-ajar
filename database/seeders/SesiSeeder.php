<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sesi;
use App\Models\Mentor;
use App\Models\Pelanggan;
use App\Models\Kursus;
use App\Models\JadwalKursus;

class SesiSeeder extends Seeder
{
    public function run()
    {
        $mentorIds = Mentor::pluck('id')->toArray();
        $pelangganIds = Pelanggan::pluck('id')->toArray();
        $kursusIds = Kursus::pluck('id')->toArray();
        $jadwalIds = JadwalKursus::pluck('id')->toArray();
        $statusList = ['pending', 'confirmed', 'selesai'];
        // Buat sesi untuk setiap kombinasi mentor, pelanggan, kursus, dan jadwal_kursus
        $sesiList = [];
        foreach ($mentorIds as $mentorId) {
            foreach ($pelangganIds as $pelangganId) {
                foreach ($kursusIds as $kursusId) {
                    foreach ($jadwalIds as $jadwalId) {
                        // Batasi jumlah sesi agar tidak terlalu banyak (misal: 1 sesi per kombinasi mentor-pelanggan)
                        if (rand(0, 100) < 2) { // 2% chance, randomize for variety
                            $sesiList[] = [
                                'mentor_id' => $mentorId,
                                'pelanggan_id' => $pelangganId,
                                'kursus_id' => $kursusId,
                                'jadwal_kursus_id' => $jadwalId,
                                'detailKursus' => 'Materi sesi antara mentor ' . (\App\Models\Mentor::find($mentorId)?->user?->nama ?? '-') . ' dan pelanggan ' . (\App\Models\Pelanggan::find($pelangganId)?->user?->nama ?? '-') . '.',
                                'statusSesi' => $statusList[array_rand($statusList)],
                            ];
                        }
                    }
                }
            }
        }
        // Jika terlalu sedikit, tambahkan random
        if (count($sesiList) < 10) {
            for ($i = 1; $i <= 10; $i++) {
                $sesiList[] = [
                    'mentor_id' => $mentorIds[array_rand($mentorIds)],
                    'pelanggan_id' => $pelangganIds[array_rand($pelangganIds)],
                    'kursus_id' => $kursusIds[array_rand($kursusIds)],
                    'jadwal_kursus_id' => $jadwalIds[array_rand($jadwalIds)],
                    'detailKursus' => 'Materi sesi ke-' . $i . ' antara mentor ' . (\App\Models\Mentor::find($mentorIds[array_rand($mentorIds)])?->user?->nama ?? '-') . ' dan pelanggan ' . (\App\Models\Pelanggan::find($pelangganIds[array_rand($pelangganIds)])?->user?->nama ?? '-') . '.',
                    'statusSesi' => $statusList[array_rand($statusList)],
                ];
            }
        }
        foreach ($sesiList as $sesi) {
            Sesi::firstOrCreate([
                'mentor_id' => $sesi['mentor_id'],
                'pelanggan_id' => $sesi['pelanggan_id'],
                'kursus_id' => $sesi['kursus_id'],
                'jadwal_kursus_id' => $sesi['jadwal_kursus_id'],
            ], $sesi);
        }
    }
}

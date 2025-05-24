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
        $sesiList = [];
        for ($i = 1; $i <= 10; $i++) {
            $sesiList[] = [
                'mentor_id' => $mentorIds[array_rand($mentorIds)],
                'pelanggan_id' => $pelangganIds[array_rand($pelangganIds)],
                'kursus_id' => $kursusIds[array_rand($kursusIds)],
                'jadwal_kursus_id' => $jadwalIds[array_rand($jadwalIds)],
                'detailKursus' => 'Materi sesi ke-' . $i,
                'statusSesi' => 'booked',
            ];
        }
        foreach ($sesiList as $sesi) {
            Sesi::create($sesi);
        }
    }
}

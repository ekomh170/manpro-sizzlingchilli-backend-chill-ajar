<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kursus;
use App\Models\Mentor;

class KursusSeeder extends Seeder
{
    public function run()
    {
        $mentorIds = Mentor::pluck('id')->toArray();
        $kursusList = [];
        for ($i = 1; $i <= 10; $i++) {
            $kursusList[] = [
                'namaKursus' => 'Kursus Dummy ' . $i,
                'deskripsi' => 'Deskripsi untuk Kursus Dummy ' . $i,
                'mentor_id' => $mentorIds[array_rand($mentorIds)],
                'gayaMengajar' => $i % 2 === 0 ? 'online' : 'offline',
                'fotoKursus' => 'foto_kursus/kursus_dummy_' . $i . '.jpg',
            ];
        }
        foreach ($kursusList as $kursus) {
            Kursus::create($kursus);
        }
    }
}

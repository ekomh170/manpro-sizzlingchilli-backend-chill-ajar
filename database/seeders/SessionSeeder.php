<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Session;
use App\Models\Mentor;
use App\Models\Pelanggan;

class SessionSeeder extends Seeder
{
    public function run()
    {
        $mentorIds = Mentor::pluck('id')->toArray();
        $pelangganIds = Pelanggan::pluck('id')->toArray();
        $sessions = [];
        for ($i = 1; $i <= 10; $i++) {
            $sessions[] = [
                'mentor_id' => $mentorIds[array_rand($mentorIds)],
                'pelanggan_id' => $pelangganIds[array_rand($pelangganIds)],
                'detailKursus' => 'Materi sesi ke-' . $i,
                'jadwal' => now()->addDays($i),
                'statusSesi' => 'booked',
            ];
        }
        foreach ($sessions as $session) {
            Session::create($session);
        }
    }
}

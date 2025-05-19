<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Testimoni;
use App\Models\Session;
use App\Models\Mentor;
use App\Models\Pelanggan;

class TestimoniSeeder extends Seeder
{
    public function run()
    {
        $sessionIds = Session::pluck('id')->toArray();
        $mentorIds = Mentor::pluck('id')->toArray();
        $pelangganIds = Pelanggan::pluck('id')->toArray();
        for ($i = 1; $i <= 20; $i++) {
            Testimoni::create([
                'session_id' => $sessionIds[array_rand($sessionIds)],
                'pelanggan_id' => $pelangganIds[array_rand($pelangganIds)],
                'mentor_id' => $mentorIds[array_rand($mentorIds)],
                'rating' => rand(3, 5),
                'komentar' => 'Komentar dummy ke-' . $i,
                'tanggal' => now()->subDays(rand(0, 30)),
            ]);
        }
    }
}

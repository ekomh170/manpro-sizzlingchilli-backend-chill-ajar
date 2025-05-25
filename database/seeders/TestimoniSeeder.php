<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Testimoni;
use App\Models\Sesi;
use App\Models\Mentor;
use App\Models\Pelanggan;

class TestimoniSeeder extends Seeder
{
    public function run()
    {
        $mentorObjs = Mentor::with('user')->get();
        $pelangganObjs = Pelanggan::with('user')->get();
        $sesiIds = Sesi::pluck('id')->toArray();
        // Buat testimoni untuk setiap kombinasi mentor dan pelanggan (user akun)
        foreach ($mentorObjs as $mentor) {
            foreach ($pelangganObjs as $pelanggan) {
                // Cari sesi yang cocok (jika ada) untuk relasi ini
                $sesiId = null;
                foreach ($sesiIds as $sid) {
                    $sesi = \App\Models\Sesi::find($sid);
                    if ($sesi && $sesi->mentor_id == $mentor->id && $sesi->pelanggan_id == $pelanggan->id) {
                        $sesiId = $sid;
                        break;
                    }
                }
                $mentorNama = $mentor->user ? $mentor->user->nama : 'Mentor';
                $pelangganNama = $pelanggan->user ? $pelanggan->user->nama : 'Pelanggan';
                Testimoni::firstOrCreate([
                    'mentor_id' => $mentor->id,
                    'pelanggan_id' => $pelanggan->id,
                ], [
                    'sesi_id' => $sesiId ?? ($sesiIds ? $sesiIds[array_rand($sesiIds)] : null),
                    'rating' => rand(3, 5),
                    'komentar' => "Testimoni dari $pelangganNama untuk $mentorNama. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque habitant.",
                    'tanggal' => now()->subDays(rand(0, 30)),
                ]);
            }
        }
    }
}

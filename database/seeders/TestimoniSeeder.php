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
        // Buat testimoni hanya untuk sesi yang statusnya 'reviewed'
        $sesiReviewed = Sesi::where('statusSesi', 'reviewed')->get();
        foreach ($sesiReviewed as $sesi) {
            $mentor = $sesi->mentor;
            $pelanggan = $sesi->pelanggan;
            $mentorNama = $mentor && $mentor->user ? $mentor->user->nama : 'Mentor';
            $pelangganNama = $pelanggan && $pelanggan->user ? $pelanggan->user->nama : 'Pelanggan';
            $namaKursus = $sesi->kursus ? $sesi->kursus->namaKursus : 'Kursus';
            // Deteksi mentor cewek berdasarkan nama depan (misal: Siti, Dewi, Fatiya, dsb)
            $namaCewek = ['Siti', 'Dewi', 'Fatiya', 'Sari', 'Putri', 'Lina', 'Tia', 'Rina', 'Ayu', 'Labibah', 'Lestari', 'Marlina', 'Rahmawati', 'Nurhaliza'];
            $isCewek = false;
            foreach ($namaCewek as $namaC) {
                if (stripos($mentorNama, $namaC) === 0) {
                    $isCewek = true;
                    break;
                }
            }
            $komentar = "Testimoni dari $pelangganNama untuk $mentorNama pada kursus $namaKursus. Materi sangat bermanfaat dan penjelasan mudah dipahami!";
            if ($isCewek) {
                $komentar .= " Mentor cantik dan ramah.";
            }
            Testimoni::firstOrCreate([
                'mentor_id' => $sesi->mentor_id,
                'pelanggan_id' => $sesi->pelanggan_id,
                'sesi_id' => $sesi->id,
            ], [
                'rating' => rand(3, 5),
                'komentar' => $komentar,
                'tanggal' => now()->subDays(rand(0, 30)),
            ]);
        }
    }
}

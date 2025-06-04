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
        $statusList = ['pending', 'started', 'end', 'reviewed'];
        // Hapus seluruh sesiList random, hanya generate 4 sesi unik per pelanggan
        $sesiList = [];
        // Setiap pelanggan punya 5 sesi: 2 pending, 1 started, 1 end, 1 reviewed
        $statusAktifList = ['pending', 'pending', 'started', 'end', 'reviewed'];
        foreach ($pelangganIds as $pelangganId) {
            // Ambil 5 kursus acak (boleh sama/beda mentor)
            $kursusArr = \App\Models\Kursus::inRandomOrder()->limit(5)->get();
            for ($i = 0; $i < 5; $i++) {
                $kursus = $kursusArr[$i] ?? \App\Models\Kursus::inRandomOrder()->first();
                if ($kursus) {
                    $kursusId = $kursus->id;
                    $mentorId = $kursus->mentor_id;
                    // Cari jadwal yang sesuai kursus
                    $jadwal = \App\Models\JadwalKursus::where('kursus_id', $kursusId)->inRandomOrder()->first();
                    if ($jadwal) {
                        $jadwalId = $jadwal->id;
                        $statusSesi = $statusAktifList[$i];
                        $sesiList[] = [
                            'mentor_id' => $mentorId,
                            'pelanggan_id' => $pelangganId,
                            'kursus_id' => $kursusId,
                            'jadwal_kursus_id' => $jadwalId,
                            'detailKursus' => 'Sesi history (' . $statusSesi . ') antara mentor ' . (\App\Models\Mentor::find($mentorId)?->user?->nama ?? '-') . ' dan pelanggan ' . (\App\Models\Pelanggan::find($pelangganId)?->user?->nama ?? '-') . '.',
                            'statusSesi' => $statusSesi,
                        ];
                    }
                }
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

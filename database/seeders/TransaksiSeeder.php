<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;
use App\Models\Sesi;
use App\Models\Mentor;
use App\Models\Pelanggan;

class TransaksiSeeder extends Seeder
{
    public function run()
    {
        // Buat transaksi berdasarkan data sesi, mentor, dan pelanggan yang valid
        $sesiObjs = Sesi::all();
        $status = ['menunggu_verifikasi', 'accepted'];
        $metode = ['transfer', 'e-wallet', 'cash'];
        foreach ($sesiObjs as $sesi) {
            // Validasi relasi: mentor harus punya kursus, jadwal harus milik kursus
            $kursus = \App\Models\Kursus::where('id', $sesi->kursus_id)->where('mentor_id', $sesi->mentor_id)->first();
            $jadwal = \App\Models\JadwalKursus::where('id', $sesi->jadwal_kursus_id)->where('kursus_id', $sesi->kursus_id)->first();
            if (!$kursus || !$jadwal) continue;

            // Cek jika pelanggan adalah Eko, paksa mentor dan kursus milik Fatiya
            $pelangganUser = $sesi->pelanggan ? $sesi->pelanggan->user : null;
            $isEko = $pelangganUser && strtolower($pelangganUser->nama) === 'eko muchamad haryono';
            if ($isEko) {
                $mentorFatiya = \App\Models\Mentor::whereHas('user', function ($q) {
                    $q->whereRaw('LOWER(nama) = ?', ['fatiya labibah']);
                })->first();
                if ($mentorFatiya) {
                    $mentorId = $mentorFatiya->id;
                    $kursusFatiya = \App\Models\Kursus::where('mentor_id', $mentorId)->inRandomOrder()->first();
                    if ($kursusFatiya) {
                        $kursus = $kursusFatiya;
                    }
                }
            }

            // Mapping status sesi ke status pembayaran transaksi
            if ($sesi->statusSesi === 'pending') {
                $statusPembayaran = ($sesi->id % 2 === 0) ? 'rejected' : 'menunggu_verifikasi';
            } elseif (in_array($sesi->statusSesi, ['started', 'end', 'reviewed'])) {
                $statusPembayaran = 'accepted';
            } else {
                continue; // skip jika statusSesi tidak valid
            }

            Transaksi::firstOrCreate([
                'sesi_id' => $sesi->id,
            ], [
                'pelanggan_id' => $sesi->pelanggan_id,
                'mentor_id' => $isEko && isset($mentorId) ? $mentorId : $sesi->mentor_id,
                'sesi_id' => $sesi->id,
                'jumlah' => 25000,
                'statusPembayaran' => $statusPembayaran,
                'metodePembayaran' => $metode[array_rand($metode)],
                'tanggalPembayaran' => now()->subDays(rand(0, 30)),
                'buktiPembayaran' => 'bukti_dummy.jpg',
                // Hapus 'kursus_id' karena tidak ada di tabel transaksi
            ]);
        }
    }
}

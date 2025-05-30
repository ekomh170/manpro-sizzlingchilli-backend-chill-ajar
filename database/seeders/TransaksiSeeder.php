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
        $status = ['menunggu_verifikasi', 'lunas', 'gagal', 'accepted'];
        $metode = ['transfer', 'e-wallet', 'cash'];
        foreach ($sesiObjs as $sesi) {
            Transaksi::firstOrCreate([
                'sesi_id' => $sesi->id,
            ], [
                'pelanggan_id' => $sesi->pelanggan_id,
                'mentor_id' => $sesi->mentor_id,
                'sesi_id' => $sesi->id,
                'jumlah' => 25000,
                'statusPembayaran' => $status[array_rand($status)],
                'metodePembayaran' => $metode[array_rand($metode)], // wajib
                'tanggalPembayaran' => now()->subDays(rand(0, 30)), // wajib
                'buktiPembayaran' => 'bukti_dummy.jpg', // wajib file, dummy path
            ]);
        }
    }
}

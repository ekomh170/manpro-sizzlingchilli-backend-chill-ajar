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
        $sesiIds = Sesi::pluck('id')->toArray();
        $mentorIds = Mentor::pluck('id')->toArray();
        $pelangganIds = Pelanggan::pluck('id')->toArray();
        $status = ['menunggu_verifikasi', 'lunas', 'gagal'];
        $metode = ['transfer', 'e-wallet', 'cash'];
        for ($i = 1; $i <= 20; $i++) {
            Transaksi::create([
                'pelanggan_id' => $pelangganIds[array_rand($pelangganIds)],
                'mentor_id' => $mentorIds[array_rand($mentorIds)],
                'sesi_id' => $sesiIds[array_rand($sesiIds)],
                'jumlah' => rand(25000, 25000),
                'statusPembayaran' => $status[array_rand($status)],
                'metodePembayaran' => $metode[array_rand($metode)],
                'tanggalPembayaran' => now()->subDays(rand(0, 30)),
            ]);
        }
    }
}

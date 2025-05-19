<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Session;
use App\Models\Mentor;
use App\Models\User;

class PaymentSeeder extends Seeder
{
    public function run()
    {
        $sessionIds = Session::pluck('id')->toArray();
        $mentorIds = Mentor::pluck('id')->toArray();
        // Ambil hanya user dengan peran pelanggan
        $userIds = User::where('peran', 'pelanggan')->pluck('id')->toArray();
        $status = ['menunggu_verifikasi', 'lunas', 'gagal'];
        $metode = ['transfer', 'e-wallet', 'cash'];
        for ($i = 1; $i <= 20; $i++) {
            Payment::create([
                'user_id' => $userIds[array_rand($userIds)],
                'mentor_id' => $mentorIds[array_rand($mentorIds)],
                'session_id' => $sessionIds[array_rand($sessionIds)],
                'jumlah' => rand(50000, 200000),
                'statusPembayaran' => $status[array_rand($status)],
                'metodePembayaran' => $metode[array_rand($metode)],
                'tanggalPembayaran' => now()->subDays(rand(0, 30)),
            ]);
        }
    }
}

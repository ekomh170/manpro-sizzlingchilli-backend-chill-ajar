<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pelanggan;

class PelangganSeeder extends Seeder
{
    public function run()
    {
        $pelangganEmails = [
            'eko@demo.com',
            'firenze@demo.com',
            'arby@demo.com',
            'wildan@demo.com',
        ];
        // 10 pelanggan dummy
        for ($i = 1; $i <= 10; $i++) {
            $pelangganEmails[] = "pelanggandummy$i@demo.com";
        }

        foreach ($pelangganEmails as $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                Pelanggan::firstOrCreate(
                    ['user_id' => $user->id]
                );
            }
        }
    }
}

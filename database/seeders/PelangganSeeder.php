<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pelanggan;

class PelangganSeeder extends Seeder
{
    public function run()
    {
        // Ambil semua user dengan peran pelanggan
        $pelangganUsers = User::where('peran', 'pelanggan')->get();
        foreach ($pelangganUsers as $user) {
            Pelanggan::firstOrCreate(
                ['user_id' => $user->id]
            );
        }
    }
}

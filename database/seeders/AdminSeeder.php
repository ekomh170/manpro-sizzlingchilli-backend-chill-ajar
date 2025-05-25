<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Ambil semua user dengan peran admin
        $adminUsers = User::where('peran', 'admin')->get();
        foreach ($adminUsers as $user) {
            Admin::firstOrCreate([
                'user_id' => $user->id
            ]);
        }
    }
}

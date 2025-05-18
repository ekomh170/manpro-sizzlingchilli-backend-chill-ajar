<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $adminEmails = [
            'eko@demo.com',
            'firenze@demo.com',
            'farrel@demo.com',
            'faiz@demo.com',
        ];

        foreach ($adminEmails as $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                Admin::firstOrCreate([
                    'user_id' => $user->id
                ]);
            }
        }
    }
}

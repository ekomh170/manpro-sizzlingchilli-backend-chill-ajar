<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Eko Muchamad Haryono',
                'email' => 'eko@demo.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Firenze Higa Putra',
                'email' => 'firenze@demo.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Fatiya Labibah',
                'email' => 'fatiya@demo.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Muhammad Farrel Zilviano',
                'email' => 'farrel@demo.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Ahmad Faiz Al Asad',
                'email' => 'faiz@demo.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Arby Ali Amludin',
                'email' => 'arby@demo.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Muhammad Wildan Ziyad Alfarabi',
                'email' => 'wildan@demo.com',
                'password' => Hash::make('password'),
            ],
        ];

        // 4 mentor tambahan
        for ($i = 1; $i <= 4; $i++) {
            $users[] = [
                'name' => "Mentor Dummy $i",
                'email' => "mentordummy$i@demo.com",
                'password' => Hash::make('password'),
            ];
        }

        // 10 pelanggan tambahan
        for ($i = 1; $i <= 10; $i++) {
            $users[] = [
                'name' => "Pelanggan Dummy $i",
                'email' => "pelanggandummy$i@demo.com",
                'password' => Hash::make('password'),
            ];
        }

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }
    }
}

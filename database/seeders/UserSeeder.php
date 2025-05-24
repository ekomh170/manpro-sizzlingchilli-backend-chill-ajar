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
                'nama' => 'Eko Muchamad Haryono',
                'email' => 'eko@demo.com',
                'password' => Hash::make('password'),
                'nomorTelepon' => '081234567890',
                'peran' => 'admin',
                'alamat' => 'Jakarta',
                'foto_profil' => 'foto_profil/default_admin1.jpg',
            ],
            [
                'nama' => 'Firenze Higa Putra',
                'email' => 'firenze@demo.com',
                'password' => Hash::make('password'),
                'nomorTelepon' => '081234567891',
                'peran' => 'admin',
                'alamat' => 'Bandung',
                'foto_profil' => 'foto_profil/default_admin2.jpg',
            ],
            [
                'nama' => 'Fatiya Labibah',
                'email' => 'fatiya@demo.com',
                'password' => Hash::make('password'),
                'nomorTelepon' => '081234567892',
                'peran' => 'mentor',
                'alamat' => 'Surabaya',
                'foto_profil' => 'foto_profil/default_mentor1.jpg',
            ],
            [
                'nama' => 'Muhammad Farrel Zilviano',
                'email' => 'farrel@demo.com',
                'password' => Hash::make('password'),
                'nomorTelepon' => '081234567893',
                'peran' => 'admin',
                'alamat' => 'Yogyakarta',
                'foto_profil' => 'foto_profil/default_admin3.jpg',
            ],
            [
                'nama' => 'Ahmad Faiz Al Asad',
                'email' => 'faiz@demo.com',
                'password' => Hash::make('password'),
                'nomorTelepon' => '081234567894',
                'peran' => 'admin',
                'alamat' => 'Semarang',
                'foto_profil' => 'foto_profil/default_admin4.jpg',
            ],
            [
                'nama' => 'Arby Ali Amludin',
                'email' => 'arby@demo.com',
                'password' => Hash::make('password'),
                'nomorTelepon' => '081234567895',
                'peran' => 'pelanggan',
                'alamat' => 'Malang',
                'foto_profil' => 'foto_profil/default_pelanggan1.jpg',
            ],
            [
                'nama' => 'Muhammad Wildan Ziyad Alfarabi',
                'email' => 'wildan@demo.com',
                'password' => Hash::make('password'),
                'nomorTelepon' => '081234567896',
                'peran' => 'pelanggan',
                'alamat' => 'Depok',
                'foto_profil' => 'foto_profil/default_pelanggan2.jpg',
            ],
        ];

        // 4 mentor tambahan
        for ($i = 1; $i <= 4; $i++) {
            $users[] = [
                'nama' => "Mentor Dummy $i",
                'email' => "mentordummy$i@demo.com",
                'password' => Hash::make('password'),
                'nomorTelepon' => '0812345678' . (97 + $i),
                'peran' => 'mentor',
                'alamat' => 'Kota Dummy',
                'foto_profil' => "foto_profil/mentor_dummy_$i.jpg",
            ];
        }

        // 10 pelanggan tambahan
        for ($i = 1; $i <= 10; $i++) {
            $users[] = [
                'nama' => "Pelanggan Dummy $i",
                'email' => "pelanggandummy$i@demo.com",
                'password' => Hash::make('password'),
                'nomorTelepon' => '0812345679' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'peran' => 'pelanggan',
                'alamat' => 'Kota Dummy',
                'foto_profil' => "foto_profil/pelanggan_dummy_$i.jpg",
            ];
        }

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }
    }
}

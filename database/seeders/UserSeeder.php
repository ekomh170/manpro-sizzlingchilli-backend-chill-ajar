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

        // 4 mentor tambahan dengan nama, email, dan kota Indonesia acak
        $namaMentor = [
            'Budi Santoso',
            'Siti Nurhaliza',
            'Agus Pratama',
            'Dewi Lestari',
        ];
        $kotaMentor = [
            'Jakarta',
            'Surabaya',
            'Bandung',
            'Yogyakarta'
        ];
        for ($i = 1; $i <= 4; $i++) {
            $nama = $namaMentor[$i - 1];
            $namaEmail = strtolower(str_replace(' ', '', $nama));
            $users[] = [
                'nama' => $nama,
                'email' => $namaEmail . rand(10, 99) . '@mail.com',
                'password' => Hash::make('password'),
                'nomorTelepon' => '0812345678' . (97 + $i),
                'peran' => 'mentor',
                'alamat' => $kotaMentor[$i - 1],
                'foto_profil' => "foto_profil/mentor_dummy_$i.jpg",
            ];
        }

        // 10 pelanggan tambahan dengan nama, email, dan kota Indonesia acak
        $namaPelanggan = [
            'Rina Wulandari',
            'Andi Saputra',
            'Sari Dewi',
            'Dian Prasetyo',
            'Yusuf Maulana',
            'Putri Ayu',
            'Rizky Ramadhan',
            'Lina Marlina',
            'Bayu Nugroho',
            'Tia Rahmawati',
        ];
        $kotaPelanggan = [
            'Bandung',
            'Surabaya',
            'Medan',
            'Makassar',
            'Palembang',
            'Semarang',
            'Depok',
            'Bekasi',
            'Bogor',
            'Malang',
        ];
        for ($i = 1; $i <= 10; $i++) {
            $nama = $namaPelanggan[$i - 1];
            $namaEmail = strtolower(str_replace(' ', '', $nama));
            $users[] = [
                'nama' => $nama,
                'email' => $namaEmail . rand(10, 99) . '@mail.com',
                'password' => Hash::make('password'),
                'nomorTelepon' => '0812345679' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'peran' => 'pelanggan',
                'alamat' => $kotaPelanggan[$i - 1],
                'foto_profil' => "foto_profil/pelanggan_dummy_$i.jpg",
            ];
        }

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }
    }
}

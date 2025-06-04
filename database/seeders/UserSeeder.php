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
                'nomorTelepon' => '082246105463',
                'peran' => 'admin',
                'alamat' => 'Jakarta',
                'foto_profil' => 'foto_profil/default.png', // default foto profil
            ],
            [
                'nama' => 'Firenze Higa Putra',
                'email' => 'firenze@demo.com',
                'password' => Hash::make('password'),
                'nomorTelepon' => '085894310722',
                'peran' => 'admin',
                'alamat' => 'Bandung',
                'foto_profil' => 'foto_profil/default.png', // default foto profil
            ],
            [
                'nama' => 'Fatiya Labibah',
                'email' => 'fatiya@demo.com',
                'password' => Hash::make('password'),
                'nomorTelepon' => '083877136044',
                'peran' => 'mentor',
                'alamat' => 'Surabaya',
                'foto_profil' => 'foto_profil/default.png', // default foto profil
            ],
            [
                'nama' => 'Muhammad Farrel Zilviano',
                'email' => 'farrel@demo.com',
                'password' => Hash::make('password'),
                'nomorTelepon' => '081234567893',
                'peran' => 'admin',
                'alamat' => 'Yogyakarta',
                'foto_profil' => 'foto_profil/default.png', // default foto profil
            ],
            [
                'nama' => 'Ahmad Faiz Al Asad',
                'email' => 'faiz@demo.com',
                'password' => Hash::make('password'),
                'nomorTelepon' => '082214283126',
                'peran' => 'admin',
                'alamat' => 'Semarang',
                'foto_profil' => 'foto_profil/default.png', // default foto profil
            ],
            [
                'nama' => 'Arby Ali Amludin',
                'email' => 'arby@demo.com',
                'password' => Hash::make('password'),
                'nomorTelepon' => '081295563905',
                'peran' => 'pelanggan',
                'alamat' => 'Malang',
                'foto_profil' => 'foto_profil/default.png', // default foto profil
            ],
            [
                'nama' => 'Muhammad Wildan Ziyad Alfarabi',
                'email' => 'wildan@demo.com',
                'password' => Hash::make('password'),
                'nomorTelepon' => '081299666275',
                'peran' => 'pelanggan',
                'alamat' => 'Depok',
                'foto_profil' => 'foto_profil/default.png', // default foto profil
            ],
            [
                'nama' => 'Eko Muchamad Haryono',
                'email' => 'ekopelanggan@demo.com',
                'password' => Hash::make('password'),
                'nomorTelepon' => '082246105463',
                'peran' => 'pelanggan',
                'alamat' => 'Jakarta',
                'foto_profil' => 'foto_profil/default.png', // default foto profil
            ],
            [
                'nama' => 'Firenze Higa Putra',
                'email' => 'firenzepelanggan@demo.com',
                'password' => Hash::make('password'),
                'nomorTelepon' => '085894310722',
                'peran' => 'admin',
                'alamat' => 'Bandung',
                'foto_profil' => 'foto_profil/default.png', // default foto profil
            ],
            [
                'nama' => 'Muhammad Farrel Zilviano',
                'email' => 'farrelpelanggan@demo.com',
                'password' => Hash::make('password'),
                'nomorTelepon' => '081234567893',
                'peran' => 'admin',
                'alamat' => 'Yogyakarta',
                'foto_profil' => 'foto_profil/default.png', // default foto profil
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
                'foto_profil' => "foto_profil/default.png", // default foto profil
            ];
        }

        // 11 pelanggan tambahan dengan nama, email, dan kota Indonesia acak
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
            'Dewi Anggraini',
            'Nina Kartika',
            'Bambang Pamungkas',
            'Sinta Oktaviani',
            'Rudi Hartono',
            'Mega Sari',
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
            'Padang',
            'Solo',
            'Cirebon',
            'Pontianak',
            'Banjarmasin',
            'Pekanbaru',
        ];
        for ($i = 1; $i <= 11; $i++) {
            $nama = $namaPelanggan[$i - 1];
            $namaEmail = strtolower(str_replace(' ', '', $nama));
            $users[] = [
                'nama' => $nama,
                'email' => $namaEmail . rand(10, 99) . '@mail.com',
                'password' => Hash::make('password'),
                'nomorTelepon' => '0812345679' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'peran' => 'pelanggan',
                'alamat' => $kotaPelanggan[$i - 1],
                'foto_profil' => "foto_profil/default.png", // default foto profil
            ];
        }

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Mentor;

class MentorSeeder extends Seeder
{
    public function run()
    {
        // Ambil semua user dengan peran mentor
        $mentorUsers = User::where('peran', 'mentor')->get();
        $dummyMentorData = [
            'rating' => 0,
            'biayaPerSesi' => 25000, // Semua mentor 25rb
            'biayaPerSesiOffline' => 30000, // offline 30rb
            'deskripsi' => 'Mentor berpengalaman di bidangnya.'
        ];
        foreach ($mentorUsers as $user) {
            Mentor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'rating' => $dummyMentorData['rating'],
                    'biayaPerSesi' => $dummyMentorData['biayaPerSesi'],
                    'biayaPerSesiOffline' => $dummyMentorData['biayaPerSesiOffline'],
                    'deskripsi' => $dummyMentorData['deskripsi'],
                    'status' => 'active', // atau 'pending' jika ingin dummy mentor belum diverifikasi
                    'dokumen_pendukung' => null, // atau path file dummy jika ingin testing upload
                ]
            );
        }

        // Update semua mentor yang sudah ada di database jika biayaPerSesi null saja (tanpa cek string kosong)
        \App\Models\Mentor::whereNull('biayaPerSesi')->update(['biayaPerSesi' => 25000]);
    }
}

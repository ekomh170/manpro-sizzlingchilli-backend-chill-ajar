<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Mentor;

class MentorSeeder extends Seeder
{
    public function run()
    {
        $mentorEmails = [
            'eko@demo.com',
            'firenze@demo.com',
            'fatiya@demo.com',
        ];
        // 4 mentor dummy
        for ($i = 1; $i <= 4; $i++) {
            $mentorEmails[] = "mentordummy$i@demo.com";
        }

        $dummyMentorData = [
            'rating' => 0,
            'biayaPerSesi' => 25000, // Semua mentor 25rb
            'deskripsi' => 'Mentor berpengalaman di bidangnya.'
        ];

        foreach ($mentorEmails as $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                Mentor::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'rating' => $dummyMentorData['rating'],
                        'biayaPerSesi' => $dummyMentorData['biayaPerSesi'],
                        'deskripsi' => $dummyMentorData['deskripsi'],
                    ]
                );
            }
        }

        // Update semua mentor yang sudah ada di database jika biayaPerSesi kosong/null
        \App\Models\Mentor::whereNull('biayaPerSesi')->orWhere('biayaPerSesi', '')->update(['biayaPerSesi' => 25000]);
    }
}

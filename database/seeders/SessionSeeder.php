<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Session;
use App\Models\Mentor;
use App\Models\Pelanggan;
use App\Models\CourseSchedule;

class SessionSeeder extends Seeder
{
    public function run()
    {
        $mentorIds = Mentor::pluck('id')->toArray();
        $pelangganIds = Pelanggan::pluck('id')->toArray();
        $scheduleIds = CourseSchedule::pluck('id')->toArray();
        $sessions = [];
        for ($i = 1; $i <= 10; $i++) {
            $sessions[] = [
                'mentor_id' => $mentorIds[array_rand($mentorIds)],
                'pelanggan_id' => $pelangganIds[array_rand($pelangganIds)],
                'course_schedule_id' => $scheduleIds ? $scheduleIds[array_rand($scheduleIds)] : null,
                'detailKursus' => 'Materi sesi ke-' . $i,
                'jadwal' => now()->addDays($i),
                'statusSesi' => 'booked',
            ];
        }
        foreach ($sessions as $session) {
            Session::create($session);
        }
    }
}

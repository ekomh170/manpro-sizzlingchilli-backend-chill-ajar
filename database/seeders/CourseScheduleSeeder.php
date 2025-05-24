<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseSchedule;
use App\Models\Course;

class CourseScheduleSeeder extends Seeder
{
    public function run()
    {
        $courseIds = Course::pluck('id')->toArray();
        for ($i = 1; $i <= 20; $i++) {
            CourseSchedule::create([
                'course_id' => $courseIds[array_rand($courseIds)],
                'tanggal' => now()->addDays(rand(1, 30)),
                'waktu' => sprintf('%02d:00:00', rand(8, 18)),
                'keterangan' => 'Jadwal dummy ke-' . $i,
            ]);
        }
    }
}

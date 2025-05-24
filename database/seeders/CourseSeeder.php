<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Mentor;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $mentorIds = Mentor::pluck('id')->toArray();
        $courses = [];
        for ($i = 1; $i <= 10; $i++) {
            $courses[] = [
                'namaCourse' => 'Course Dummy ' . $i,
                'deskripsi' => 'Deskripsi untuk Course Dummy ' . $i,
                'mentor_id' => $mentorIds[array_rand($mentorIds)],
                'gambar_kursus' => 'gambar_kursus/course_dummy_' . $i . '.jpg',
            ];
        }
        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}

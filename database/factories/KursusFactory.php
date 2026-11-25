<?php

namespace Database\Factories;

use App\Models\Mentor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kursus>
 */
class KursusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'namaKursus' => fake()->words(2, true),
            'deskripsi' => fake()->sentence(),
            'mentor_id' => Mentor::factory(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mentor>
 */
class MentorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['peran' => 'mentor']),
            'rating' => 0,
            'biayaPerSesi' => 25000,
            'deskripsi' => fake()->sentence(),
            'status' => 'active',
        ];
    }
}

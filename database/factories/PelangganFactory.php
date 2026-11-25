<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pelanggan>
 */
class PelangganFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['peran' => 'pelanggan']),
        ];
    }
}

<?php
// tests/Feature/PelangganApiTest.php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Mentor;
use App\Models\Course;
use App\Models\Session;
use App\Models\Payment;
use App\Models\Testimoni;

class PelangganApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_profil_saya_endpoint()
    {
        $user = User::factory()->create(['peran' => 'pelanggan']);
        $pelanggan = Pelanggan::factory()->create(['user_id' => $user->id]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/pelanggan/profil-saya');

        $response->assertStatus(200)
            ->assertJsonFragment(['user_id' => $user->id]);
    }

    public function test_daftar_course_endpoint()
    {
        $user = User::factory()->create(['peran' => 'pelanggan']);
        $token = $user->createToken('test')->plainTextToken;
        $mentor = Mentor::factory()->create();
        $course = Course::factory()->create(['mentor_id' => $mentor->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/pelanggan/daftar-course');

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $course->id]);
    }

    public function test_cari_mentor_endpoint()
    {
        $user = User::factory()->create(['peran' => 'pelanggan']);
        $token = $user->createToken('test')->plainTextToken;
        $mentor = Mentor::factory()->create();
        $course = Course::factory()->create(['mentor_id' => $mentor->id, 'namaCourse' => 'Matematika']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/pelanggan/cari-mentor?namaCourse=Matematika');

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $mentor->id]);
    }

    public function test_detail_mentor_endpoint()
    {
        $user = User::factory()->create(['peran' => 'pelanggan']);
        $token = $user->createToken('test')->plainTextToken;
        $mentor = Mentor::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/pelanggan/detail-mentor/' . $mentor->id);

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $mentor->id]);
    }
}

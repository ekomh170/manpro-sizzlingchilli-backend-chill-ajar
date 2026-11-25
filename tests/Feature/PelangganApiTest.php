<?php
// tests/Feature/PelangganApiTest.php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Mentor;
use App\Models\Kursus;

class PelangganApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_profil_saya_endpoint()
    {
        $user = User::factory()->create(['peran' => 'pelanggan']);
        Pelanggan::factory()->create(['user_id' => $user->id]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/pelanggan/profil-saya');

        $response->assertStatus(200)
            ->assertJsonFragment(['user_id' => $user->id]);
    }

    public function test_daftar_kursus_endpoint()
    {
        $mentor = Mentor::factory()->create();
        $kursus = Kursus::factory()->create(['mentor_id' => $mentor->id]);

        // Endpoint publik, tidak perlu token
        $response = $this->getJson('/api/pelanggan/daftar-kursus');

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $kursus->id]);
    }

    public function test_cari_mentor_endpoint()
    {
        $user = User::factory()->create(['peran' => 'pelanggan']);
        $token = $user->createToken('test')->plainTextToken;
        $mentor = Mentor::factory()->create();
        Kursus::factory()->create(['mentor_id' => $mentor->id, 'namaKursus' => 'Matematika']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/pelanggan/cari-mentor?namaKursus=Matematika');

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

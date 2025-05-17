<?php
// php artisan test --filter=RegisTest

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Test untuk fitur registrasi API pada Chill Ajar
class RegisTest extends TestCase
{
    use RefreshDatabase;

    // Test registrasi pelanggan berhasil
    public function test_registrasi_pelanggan_success()
    {
        $response = $this->postJson('/api/register', [
            'peran' => 'pelanggan',
            'nama' => 'Eko Muchamad Haryono',
            'email' => 'eko@example.com',
            'password' => 'passwordku',
            'nomorTelepon' => '08123456789',
            'alamat' => 'Jl. Kampus No. 1',
        ]);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user',
                'pelanggan',
                'token'
            ])
            ->assertJson([
                'message' => 'Pelanggan berhasil terdaftar',
                'user' => [
                    'email' => 'eko@example.com',
                    'peran' => 'pelanggan',
                ]
            ]);
        // Pastikan data benar-benar masuk ke database
        $this->assertDatabaseHas('users', [
            'email' => 'eko@example.com',
            'peran' => 'pelanggan',
            'nama' => 'Eko Muchamad Haryono',
        ]);
    }

    // Test registrasi mentor berhasil
    public function test_registrasi_mentor_success()
    {
        $response = $this->postJson('/api/register', [
            'peran' => 'mentor',
            'nama' => 'Fatiya Labibah',
            'email' => 'fatiya@example.com',
            'password' => 'passwordmentor',
            'biayaPerSesi' => 100000,
            'gayaMengajar' => 'Santai',
            'deskripsi' => 'Berpengalaman mengajar',
            'nomorTelepon' => '081298765432',
            'alamat' => 'Jl. Mentor No. 2',
        ]);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user',
                'mentor',
                'token'
            ])
            ->assertJson([
                'message' => 'Mentor berhasil terdaftar',
                'user' => [
                    'email' => 'fatiya@example.com',
                    'peran' => 'mentor',
                    'nama' => 'Fatiya Labibah',
                ]
            ]);
        // Pastikan data user mentor benar-benar masuk ke database
        $this->assertDatabaseHas('users', [
            'email' => 'fatiya@example.com',
            'peran' => 'mentor',
            'nama' => 'Fatiya Labibah',
        ]);
        // Pastikan data mentor juga masuk ke tabel mentor
        $this->assertDatabaseHas('mentor', [
            'biayaPerSesi' => 100000,
        ]);
    }

    // Test validasi gagal jika field kurang
    public function test_registrasi_gagal_kurang_field()
    {
        $response = $this->postJson('/api/register', [
            'peran' => 'pelanggan',
            // 'nama' tidak diisi
            'email' => 'salah@example.com',
            'password' => 'password',
        ]);
        $response->assertStatus(422);
    }
}

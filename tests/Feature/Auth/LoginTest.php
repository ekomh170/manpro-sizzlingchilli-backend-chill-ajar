<?php
// php artisan test --filter=LoginTest

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Test untuk fitur login API pada Chill Ajar
class LoginTest extends TestCase
{
    use RefreshDatabase; // Setiap test dijalankan dengan database fresh (rollback setelah test)

    // Test login berhasil dengan data user pelanggan yang sudah ada di database
    public function test_login_success()
    {
        // Dummy user pelanggan sudah ada di database (gunakan factory atau seeder jika di production)
        \App\Models\User::create([
            'nama' => 'Eko Muchamad Haryono',
            'email' => 'eko@example.com',
            'password' => bcrypt('passwordku'),
            'peran' => 'pelanggan',
            'nomorTelepon' => '08123456789',
            'alamat' => 'Jl. Kampus No. 1',
        ]);
        $response = $this->postJson('/api/login', [
            'email' => 'eko@example.com',
            'password' => 'passwordku',
        ]);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token',
                'user'
            ]);
    }

    // Test login gagal jika email/password salah
    public function test_login_fail()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'notfound@example.com',
            'password' => 'wrongpassword',
        ]);
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Tidak sah'
            ]);
    }

    // Test login berhasil sebagai mentor
    public function test_login_mentor_success()
    {
        // Dummy user mentor sudah ada di database
        \App\Models\User::create([
            'nama' => 'Fatiya Labibah',
            'email' => 'fatiya@example.com',
            'password' => bcrypt('passwordmentor'),
            'peran' => 'mentor',
            'nomorTelepon' => '081298765432',
            'alamat' => 'Jl. Mentor No. 2',
        ]);
        $response = $this->postJson('/api/login', [
            'email' => 'fatiya@example.com',
            'password' => 'passwordmentor',
        ]);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token',
                'user'
            ]);
    }
}

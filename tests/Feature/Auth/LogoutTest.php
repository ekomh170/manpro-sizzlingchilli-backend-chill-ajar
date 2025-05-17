<?php
// php artisan test --filter=LogoutTest

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_success()
    {
        // Buat user dan login untuk mendapatkan token
        $user = \App\Models\User::create([
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
        $token = $response->json('token');

        // Lakukan logout dengan token
        $logout = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');
        $logout->assertStatus(200)
            ->assertJson([
                'message' => 'Logout berhasil'
            ]);
    }

    public function test_logout_fail_unauthenticated()
    {
        // Coba logout tanpa token
        $logout = $this->postJson('/api/logout');
        $logout->assertStatus(401);
    }
}

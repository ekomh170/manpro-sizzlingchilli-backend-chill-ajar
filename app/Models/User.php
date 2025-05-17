<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',             // Kolom nama pengguna
        'email',            // Kolom email pengguna
        'password',         // Kolom password pengguna
        'nomorTelepon',     // Kolom nomor telepon pengguna
        'peran',            // Kolom peran pengguna (admin, mentor, pelanggan)
        'alamat',           // Kolom alamat pengguna
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',         // Menyembunyikan password saat diserialisasi
        'remember_token',   // Menyembunyikan token ingat saya saat diserialisasi
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Casting tanggal verifikasi email
            'password' => 'hashed',            // Casting password ke hashed
        ];
    }

    /**
     * Relasi dengan model Admin
     * Menunjukkan bahwa setiap User bisa menjadi Admin.
     */
    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    /**
     * Relasi dengan model Mentor
     * Menunjukkan bahwa setiap User bisa menjadi Mentor.
     */
    public function mentor()
    {
        return $this->hasOne(Mentor::class);
    }

    /**
     * Relasi dengan model Pelanggan
     * Menunjukkan bahwa setiap User bisa menjadi Pelanggan.
     */
    public function pelanggan()
    {
        return $this->hasOne(Pelanggan::class);
    }
}

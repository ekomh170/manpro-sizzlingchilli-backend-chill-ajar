<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    // Menentukan nama tabel yang digunakan oleh model ini
    protected $table = 'pelanggan';

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'user_id', // ID user yang terkait
    ];

    /**
     * Relasi dengan model User
     * Menunjukkan bahwa setiap Pelanggan berhubungan dengan satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi dengan model Sesi
     * Menunjukkan bahwa seorang Pelanggan bisa memiliki banyak Sesi.
     */
    public function sesi()
    {
        return $this->hasMany(Sesi::class, 'pelanggan_id');
    }

    /**
     * Relasi dengan model Transaksi
     * Menunjukkan bahwa seorang Pelanggan bisa memiliki banyak Transaksi.
     */
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'pelanggan_id');
    }
}

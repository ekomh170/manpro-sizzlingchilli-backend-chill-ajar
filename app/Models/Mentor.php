<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mentor extends Model
{
    use HasFactory;

    // Menentukan nama tabel yang digunakan oleh model ini
    protected $table = 'mentor';

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'user_id',       // ID user yang terkait
        'rating',        // Rating awal mentor
        'biayaPerSesi',  // Tarif mentor per sesi
        'deskripsi',     // Deskripsi tentang mentor (optional)
    ];

    /**
     * Relasi dengan model User
     * Menunjukkan bahwa setiap Mentor berhubungan dengan satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi dengan model Kursus
     * Menunjukkan bahwa seorang Mentor bisa memiliki banyak Kursus.
     */
    public function kursus()
    {
        return $this->hasMany(Kursus::class, 'mentor_id');
    }

    /**
     * Relasi dengan model Sesi
     * Menunjukkan bahwa seorang Mentor bisa memiliki banyak Sesi.
     */
    public function sesi()
    {
        return $this->hasMany(Sesi::class, 'mentor_id');
    }

    /**
     * Relasi dengan model Transaksi
     * Menunjukkan bahwa seorang Mentor bisa memiliki banyak Transaksi.
     */
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'mentor_id');
    }
}

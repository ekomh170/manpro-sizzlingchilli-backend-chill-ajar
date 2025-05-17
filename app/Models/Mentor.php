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
        'gayaMengajar',  // Gaya mengajar (optional)
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
     * Relasi dengan model Course
     * Menunjukkan bahwa seorang Mentor bisa memiliki banyak Course.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Relasi dengan model Session
     * Menunjukkan bahwa seorang Mentor bisa memiliki banyak Session.
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
}

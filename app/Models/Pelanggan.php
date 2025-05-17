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
        'user_id',          // ID user yang terkait
        'statusPembayaran', // Status pembayaran
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
     * Relasi dengan model Session
     * Menunjukkan bahwa seorang Pelanggan bisa memiliki banyak Session.
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Menentukan nama tabel yang digunakan oleh model ini
    protected $table = 'payment';

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'user_id',         // ID pengguna yang membayar (pelanggan)
        'mentor_id',       // ID mentor yang menerima pembayaran
        'session_id',      // ID sesi terkait
        'jumlah',          // Nominal pembayaran
        'statusPembayaran', // Status pembayaran
        'metodePembayaran', // Metode pembayaran, misalnya transfer, e-wallet
        'tanggalPembayaran', // Tanggal pembayaran
    ];

    /**
     * Relasi dengan model User
     * Menunjukkan bahwa setiap Payment berhubungan dengan satu User (pelanggan).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi dengan model Mentor
     * Menunjukkan bahwa setiap Payment berhubungan dengan satu Mentor.
     */
    public function mentor()
    {
        return $this->belongsTo(Mentor::class);
    }

    /**
     * Relasi dengan model Session
     * Menunjukkan bahwa setiap Payment berhubungan dengan satu Session.
     */
    public function session()
    {
        return $this->belongsTo(Session::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimoni extends Model
{
    use HasFactory;

    // Menentukan nama tabel yang digunakan oleh model ini
    protected $table = 'testimoni';

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'session_id',      // ID sesi yang dinilai
        'pelanggan_id',    // ID pelanggan yang memberi testimoni
        'mentor_id',       // ID mentor yang diberi testimoni
        'rating',          // Rating yang diberikan
        'komentar',        // Komentar opsional
        'tanggal',         // Tanggal pengisian testimoni
    ];

    /**
     * Relasi dengan model Session
     * Menunjukkan bahwa setiap Testimoni berhubungan dengan satu Session.
     */
    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Relasi dengan model Pelanggan
     * Menunjukkan bahwa setiap Testimoni berhubungan dengan satu Pelanggan.
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    /**
     * Relasi dengan model Mentor
     * Menunjukkan bahwa setiap Testimoni berhubungan dengan satu Mentor.
     */
    public function mentor()
    {
        return $this->belongsTo(Mentor::class);
    }
}

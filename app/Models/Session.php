<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    // Menentukan nama tabel yang digunakan oleh model ini
    protected $table = 'session';

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'mentor_id',      // ID mentor yang mengajar sesi ini
        'pelanggan_id',   // ID pelanggan yang mengikuti sesi ini
        'detailKursus',   // Materi yang dibahas dalam sesi
        'jadwal',         // Waktu pelaksanaan sesi
        'statusSesi',     // Status sesi, misalnya menunggu, berlangsung, selesai
    ];

    /**
     * Relasi dengan model Mentor
     * Menunjukkan bahwa setiap Session berhubungan dengan satu Mentor.
     */
    public function mentor()
    {
        return $this->belongsTo(Mentor::class);
    }

    /**
     * Relasi dengan model Pelanggan
     * Menunjukkan bahwa setiap Session berhubungan dengan satu Pelanggan.
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    /**
     * Relasi dengan model Payment
     * Menunjukkan bahwa setiap Session berhubungan dengan satu Payment.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Relasi dengan model Testimoni
     * Menunjukkan bahwa setiap Session berhubungan dengan satu Testimoni.
     */
    public function testimoni()
    {
        return $this->hasOne(Testimoni::class);
    }

    /**
     * Relasi dengan model CourseSchedule
     * Menunjukkan bahwa setiap Session berhubungan dengan satu CourseSchedule.
     */
    public function courseSchedule()
    {
        return $this->belongsTo(CourseSchedule::class);
    }
}

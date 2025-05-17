<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    // Menentukan nama tabel yang digunakan oleh model ini
    protected $table = 'course';

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'namaCourse',  // Nama kursus
        'deskripsi',   // Deskripsi kursus
        'mentor_id',   // ID mentor yang mengajar kursus ini
    ];

    /**
     * Relasi dengan model Mentor
     * Menunjukkan bahwa setiap Course berhubungan dengan satu Mentor.
     */
    public function mentor()
    {
        return $this->belongsTo(Mentor::class);
    }
}

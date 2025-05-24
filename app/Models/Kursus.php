<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kursus extends Model
{
    use HasFactory;

    protected $table = 'kursus';

    protected $fillable = [
        'namaKursus',
        'deskripsi',
        'mentor_id',
        'gayaMengajar',
        'fotoKursus',
    ];

    public function mentor()
    {
        return $this->belongsTo(Mentor::class);
    }

    public function jadwalKursus()
    {
        return $this->hasMany(JadwalKursus::class, 'kursus_id');
    }

    public function sesi()
    {
        return $this->hasMany(Sesi::class, 'kursus_id');
    }
}

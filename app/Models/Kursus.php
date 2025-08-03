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
        'fotoKursus',
    ];

    public function mentor()
    {
        return $this->belongsTo(Mentor::class, 'mentor_id');
    }

    public function jadwalKursus()
    {
        return $this->hasMany(JadwalKursus::class, 'kursus_id');
    }

    public function sesi()
    {
        return $this->hasMany(Sesi::class, 'kursus_id');
    }

    /**
     * Relasi ke visibilitas_paket (paket yang terlihat di kursus ini)
     */
    public function visibilitasPaket()
    {
        return $this->hasMany(\App\Models\VisibilitasPaket::class, 'kursus_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKursus extends Model
{
    use HasFactory;

    protected $table = 'jadwal_kursus';

    protected $fillable = [
        'kursus_id',
        'tanggal',
        'waktu',
        'keterangan',
        'tempat',
    ];

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    public function sesi()
    {
        return $this->hasMany(Sesi::class, 'jadwal_kursus_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sesi extends Model
{
    use HasFactory;

    protected $table = 'sesi';

    protected $fillable = [
        'mentor_id',
        'pelanggan_id',
        'kursus_id',
        'jadwal_kursus_id',
        'detailKursus',
        'statusSesi',
    ];

    public function mentor()
    {
        return $this->belongsTo(Mentor::class, 'mentor_id');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    public function jadwalKursus()
    {
        return $this->belongsTo(JadwalKursus::class, 'jadwal_kursus_id');
    }

    public function transaksi()
    {
        return $this->hasOne(Transaksi::class, 'sesi_id');
    }

    public function testimoni()
    {
        return $this->hasOne(Testimoni::class, 'sesi_id');
    }
}

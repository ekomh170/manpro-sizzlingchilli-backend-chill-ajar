<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'pelanggan_id',
        'mentor_id',
        'sesi_id',
        'jumlah',
        'statusPembayaran',
        'metodePembayaran',
        'tanggalPembayaran',
    ];

    protected $casts = [
        'tanggalPembayaran' => 'datetime',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function mentor()
    {
        return $this->belongsTo(Mentor::class, 'mentor_id');
    }

    public function sesi()
    {
        return $this->belongsTo(Sesi::class, 'sesi_id');
    }
}

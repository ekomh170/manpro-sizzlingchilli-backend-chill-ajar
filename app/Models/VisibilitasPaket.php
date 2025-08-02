<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisibilitasPaket extends Model
{
    protected $table = 'visibilitas_paket';
    protected $fillable = [
        'kursus_id',
        'paket_id',
        'visibilitas',
    ];

    public function paket()
    {
        return $this->belongsTo(Paket::class, 'paket_id');
    }

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }
}

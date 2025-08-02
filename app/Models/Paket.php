<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paket extends Model
{
    use SoftDeletes;
    protected $table = 'paket';
    protected $fillable = [
        'nama',
        'harga_dasar',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_berakhir',
        'max_pembelian_per_user',
    ];

    public function items()
    {
        return $this->belongsToMany(ItemPaket::class, 'relasi_item_paket', 'paket_id', 'item_paket_id')
            ->withPivot('jumlah_item')
            ->withTimestamps();
    }

    public function visibilitas()
    {
        return $this->hasMany(VisibilitasPaket::class, 'paket_id');
    }
}

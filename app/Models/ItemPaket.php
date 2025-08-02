<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemPaket extends Model
{
    use SoftDeletes;
    protected $table = 'item_paket';
    protected $fillable = [
        'nama',
        'harga',
        'diskon',
        'deskripsi',
    ];

    public function paket()
    {
        return $this->belongsToMany(Paket::class, 'relasi_item_paket', 'item_paket_id', 'paket_id')
            ->withPivot('jumlah_item')
            ->withTimestamps();
    }
}

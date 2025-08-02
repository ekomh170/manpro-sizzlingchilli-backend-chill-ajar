<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelasiItemPaket extends Model
{
    protected $table = 'relasi_item_paket';
    protected $fillable = [
        'paket_id',
        'item_paket_id',
        'jumlah_item',
    ];

    public function paket()
    {
        return $this->belongsTo(Paket::class, 'paket_id');
    }

    public function itemPaket()
    {
        return $this->belongsTo(ItemPaket::class, 'item_paket_id');
    }
}

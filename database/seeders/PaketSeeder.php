<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Paket;
use App\Models\ItemPaket;
use App\Models\RelasiItemPaket;

class PaketSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Data Paket
        $paket1 = Paket::create([
            'nama' => 'Paket NgeChill',
            'harga_dasar' => 0, // Tidak ada item, harga 0
            'deskripsi' => null,
            'tanggal_mulai' => null,
            'tanggal_berakhir' => null,
            'max_pembelian_per_user' => null,
        ]);
        $paket2 = Paket::create([
            'nama' => 'Paket NgeTask & Chill',
            'harga_dasar' => 10000, // 5k + 5k (Request Materi + Bantuan ngerjain)
            'deskripsi' => null,
            'tanggal_mulai' => null,
            'tanggal_berakhir' => null,
            'max_pembelian_per_user' => null,
        ]);
        $paket3 = Paket::create([
            'nama' => 'Paket Study Date & Chill',
            'harga_dasar' => 30000, // 5k + 25k (Request Materi + Unlimited Request materi)
            'deskripsi' => null,
            'tanggal_mulai' => null,
            'tanggal_berakhir' => null,
            'max_pembelian_per_user' => null,
        ]);

        // 2. Data Item Paket
        $item1 = ItemPaket::create([
            'nama' => 'Request Materi',
            'harga' => 5000,
            'diskon' => 0,
            'deskripsi' => null,
        ]);
        $item2 = ItemPaket::create([
            'nama' => 'Bantuan ngerjain',
            'harga' => 5000,
            'diskon' => 0,
            'deskripsi' => null,
        ]);
        $item3 = ItemPaket::create([
            'nama' => 'Unlimited Request materi',
            'harga' => 25000,
            'diskon' => 0,
            'deskripsi' => null,
        ]);

        // 3. Relasi Paket - Item Paket
        // Paket 1: Tidak ada item
        // Paket 2: Request Materi & Bantuan ngerjain
        RelasiItemPaket::create([
            'paket_id' => $paket2->id,
            'item_paket_id' => $item1->id,
            'jumlah_item' => 1,
        ]);
        RelasiItemPaket::create([
            'paket_id' => $paket2->id,
            'item_paket_id' => $item2->id,
            'jumlah_item' => 1,
        ]);
        // Paket 3: Request Materi & Unlimited Request materi
        RelasiItemPaket::create([
            'paket_id' => $paket3->id,
            'item_paket_id' => $item1->id,
            'jumlah_item' => 1,
        ]);
        RelasiItemPaket::create([
            'paket_id' => $paket3->id,
            'item_paket_id' => $item3->id,
            'jumlah_item' => 1,
        ]);
    }
}

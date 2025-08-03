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
            'harga_dasar' => 0,
            'diskon' => 0,
            'deskripsi' => 'Paket dasar untuk pembelajaran santai',
            'tanggal_mulai' => now(),
            'tanggal_berakhir' => null,
            'max_pembelian_per_user' => 1,
        ]);
        $paket2 = Paket::create([
            'nama' => 'Paket NgeTask & Chill',
            'harga_dasar' => 10000,
            'diskon' => 2000,
            'deskripsi' => 'Paket lengkap dengan bantuan tugas',
            'tanggal_mulai' => now(),
            'tanggal_berakhir' => now()->addMonths(3),
            'max_pembelian_per_user' => 2,
        ]);
        $paket3 = Paket::create([
            'nama' => 'Paket Study Date & Chill',
            'harga_dasar' => 30000,
            'diskon' => 5000,
            'deskripsi' => 'Paket premium dengan fitur lengkap',
            'tanggal_mulai' => now(),
            'tanggal_berakhir' => now()->addMonths(6),
            'max_pembelian_per_user' => 5,
        ]);

        // 2. Data Item Paket
        $item1 = ItemPaket::create([
            'nama' => 'Request Materi',
            'harga' => 5000,
            'diskon' => 0,
            'deskripsi' => 'Permintaan materi pembelajaran sesuai kebutuhan',
        ]);
        $item2 = ItemPaket::create([
            'nama' => 'Bantuan ngerjain',
            'harga' => 5000,
            'diskon' => 0,
            'deskripsi' => 'Bantuan mengerjakan tugas sekolah/kuliah',
        ]);
        $item3 = ItemPaket::create([
            'nama' => 'Unlimited Request materi',
            'harga' => 25000,
            'diskon' => 0,
            'deskripsi' => 'Permintaan materi tanpa batas selama masa aktif paket',
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

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sesi;
use Carbon\Carbon;

class HapusSesiExpired extends Command
{
    // Command artisan untuk menghapus sesi yang sudah lebih dari 24 jam dan belum ada pembayaran
    protected $signature = 'sesi:hapus-expired';
    protected $description = 'Hapus sesi yang sudah lebih dari 24 jam dan belum ada pembayaran';

    public function handle()
    {
        // Ambil waktu sekarang
        $now = Carbon::now();
        // Cari semua sesi dengan status 'booked', dibuat lebih dari 24 jam lalu, dan tidak punya relasi transaksi (belum dibayar)
        $expiredSesi = Sesi::where('statusSesi', 'booked')
            ->where('created_at', '<', $now->subDay())
            ->doesntHave('transaksi')
            ->get();

        $count = $expiredSesi->count();
        // Hapus semua sesi yang memenuhi kriteria
        foreach ($expiredSesi as $sesi) {
            $sesi->delete();
        }
        // Tampilkan info hasil penghapusan di console
        $this->info("Berhasil menghapus $count sesi yang expired dan belum dibayar.");
    }
}

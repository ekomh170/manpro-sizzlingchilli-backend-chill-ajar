<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->onDelete('cascade');
            $table->foreignId('mentor_id')->constrained('mentor')->onDelete('cascade');
            $table->foreignId('sesi_id')->constrained('sesi')->onDelete('cascade');
            // Kolom paket_id dipisah ke migration add_paket_id_to_transaksi_table
            $table->float('jumlah');
            $table->string('statusPembayaran');
            $table->string('metodePembayaran')->nullable();
            $table->dateTime('tanggalPembayaran')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};

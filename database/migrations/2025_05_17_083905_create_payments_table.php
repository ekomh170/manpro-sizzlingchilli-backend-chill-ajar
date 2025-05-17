<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel payment untuk menyimpan pembayaran sesi.
     */
    public function up(): void
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pembayar (pelanggan)
            $table->foreignId('mentor_id')->constrained('mentor')->onDelete('cascade'); // Penerima
            $table->foreignId('session_id')->constrained('session')->onDelete('cascade'); // Sesi terkait
            $table->float('jumlah'); // Nominal pembayaran
            $table->string('statusPembayaran'); // Status
            $table->string('metodePembayaran'); // Transfer, e-wallet, dll
            $table->dateTime('tanggalPembayaran'); // Tanggal transaksi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};

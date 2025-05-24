<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->constrained('pelanggans')->onDelete('cascade');
            $table->foreignId('mentor_id')->constrained('mentors')->onDelete('cascade');
            $table->foreignId('sesi_id')->constrained('sesi')->onDelete('cascade');
            $table->float('jumlah');
            $table->string('statusPembayaran');
            $table->string('metodePembayaran');
            $table->dateTime('tanggalPembayaran');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};

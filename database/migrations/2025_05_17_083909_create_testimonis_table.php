<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel testimoni untuk menyimpan ulasan dari pelanggan.
     */
    public function up(): void
    {
        Schema::create('testimoni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('session')->onDelete('cascade'); // Sesi yang dinilai
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->onDelete('cascade'); // Pengulas
            $table->foreignId('mentor_id')->constrained('mentor')->onDelete('cascade'); // Mentor yang dinilai
            $table->tinyInteger('rating'); // Nilai rating
            $table->text('komentar')->nullable(); // Komentar opsional
            $table->dateTime('tanggal'); // Tanggal pengisian testimoni
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimoni');
    }
};

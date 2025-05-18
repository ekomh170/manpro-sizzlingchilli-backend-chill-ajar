<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel session untuk menyimpan sesi mentoring.
     */
    public function up(): void
    {
        Schema::create('session', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('mentor')->onDelete('cascade'); // Mentor
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->onDelete('cascade'); // Pelanggan
            $table->text('detailKursus'); // Materi sesi
            $table->dateTime('jadwal'); // Waktu pelaksanaan
            $table->string('statusSesi')->default('aktif'); // Status sesi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session');
    }
};

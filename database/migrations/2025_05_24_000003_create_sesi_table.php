<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sesi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('mentor')->onDelete('cascade');
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->onDelete('cascade');
            $table->foreignId('kursus_id')->constrained('kursus')->onDelete('cascade');
            $table->foreignId('jadwal_kursus_id')->constrained('jadwal_kursus')->onDelete('cascade');
            $table->string('detailKursus')->nullable();
            $table->string('statusSesi'); //  ['pending', 'started', 'end', 'reviewed'];
            // pending : sesi belum dimulai
            // started : sesi sedang berlangsung
            // end : sesi telah selesai
            // reviewed : sesi telah direview oleh mentor

            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('sesi');
    }
};

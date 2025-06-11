<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jadwal_kursus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kursus_id')->constrained('kursus')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('waktu');
            $table->string('gayaMengajar'); // WAJIB, tidak nullable
            $table->string('keterangan')->nullable();
            $table->string('tempat')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('jadwal_kursus');
    }
};

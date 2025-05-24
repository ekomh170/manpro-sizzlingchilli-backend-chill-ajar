<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sesi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('mentors')->onDelete('cascade');
            $table->foreignId('pelanggan_id')->constrained('pelanggans')->onDelete('cascade');
            $table->foreignId('kursus_id')->constrained('kursus')->onDelete('cascade');
            $table->foreignId('jadwal_kursus_id')->constrained('jadwal_kursus')->onDelete('cascade');
            $table->string('detailKursus')->nullable();
            $table->string('statusSesi');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('sesi');
    }
};

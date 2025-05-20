<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel course_schedules untuk menyimpan jadwal kursus.
     */
    public function up(): void
    {
        Schema::create('course_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('course')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('waktu');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel course_schedules jika rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_schedules');
    }
};

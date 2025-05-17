<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel course untuk menyimpan informasi kursus.
     */
    public function up(): void
    {
        Schema::create('course', function (Blueprint $table) {
            $table->id();
            $table->string('namaCourse'); // Nama kursus
            $table->text('deskripsi')->nullable(); // Deskripsi kursus
            $table->foreignId('mentor_id')->constrained('mentor')->onDelete('cascade'); // Relasi ke mentor
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course');
    }
};

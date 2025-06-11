<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kursus', function (Blueprint $table) {
            $table->id();
            $table->string('namaKursus');
            $table->text('deskripsi')->nullable();
            $table->foreignId('mentor_id')->constrained('mentor')->onDelete('cascade');
            $table->string('fotoKursus')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('kursus');
    }
};

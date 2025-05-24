<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel mentor untuk menyimpan data mentor.
     */
    public function up(): void
    {
        Schema::create('mentor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relasi ke users
            $table->float('rating')->default(0); // Rating awal default 0
            $table->float('biayaPerSesi')->default(25000); // Tarif mentor per sesi, default 25rb
            $table->text('deskripsi')->nullable(); // Deskripsi tentang mentor
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentor');
    }
};

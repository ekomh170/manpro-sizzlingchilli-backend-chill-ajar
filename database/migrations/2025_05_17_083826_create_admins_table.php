<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel admin yang terhubung ke tabel users.
     */
    public function up(): void
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->id(); // ID admin
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relasi ke users
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel admin jika rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};

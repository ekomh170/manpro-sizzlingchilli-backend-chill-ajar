<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ===== Tabel paket =====
        Schema::create('paket', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100); // Nama paket
            $table->integer('harga_dasar')->default(0); // Harga dasar paket
            $table->integer('diskon')->default(0); // Diskon khusus paket
            $table->text('deskripsi')->nullable(); // Deskripsi paket
            $table->date('tanggal_mulai')->nullable(); // Tanggal mulai promo/aktif (NULL = unlimited)
            $table->date('tanggal_berakhir')->nullable(); // Tanggal berakhir promo/aktif (NULL = unlimited)
            $table->integer('max_pembelian_per_user')->nullable(); // Limit pembelian per user (NULL = unlimited)
            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // deleted_at (soft delete)
        });

        // ===== Tabel item_paket =====
        Schema::create('item_paket', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100); // Nama item
            $table->integer('harga'); // Harga item
            $table->integer('diskon')->default(0); // Diskon khusus item ini
            $table->text('deskripsi')->nullable(); // Deskripsi item
            $table->timestamps();
            $table->softDeletes();
        });

        // ===== Tabel relasi_item_paket =====
        Schema::create('relasi_item_paket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_id')->constrained('paket')->onDelete('cascade'); // Relasi ke paket
            $table->foreignId('item_paket_id')->constrained('item_paket')->onDelete('cascade'); // Relasi ke item_paket
            $table->integer('jumlah_item')->default(1); // Jumlah item dalam satu paket
            $table->timestamps();
        });

        // ===== Tabel visibilitas_paket =====
        Schema::create('visibilitas_paket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kursus_id')->constrained('kursus')->onDelete('cascade'); // Relasi ke kursus
            $table->foreignId('paket_id')->constrained('paket')->onDelete('cascade'); // Relasi ke paket
            $table->boolean('visibilitas')->default(true); // Status visibilitas paket di kursus
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visibilitas_paket');
        Schema::dropIfExists('relasi_item_paket');
        Schema::dropIfExists('item_paket');
        Schema::dropIfExists('paket');
    }
};

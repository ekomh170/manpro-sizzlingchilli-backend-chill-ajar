<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('testimoni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained('sesi')->onDelete('cascade');
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->onDelete('cascade');
            $table->foreignId('mentor_id')->constrained('mentor')->onDelete('cascade');
            $table->unsignedTinyInteger('rating');
            $table->string('komentar')->nullable();
            $table->date('tanggal');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('testimoni');
    }
};

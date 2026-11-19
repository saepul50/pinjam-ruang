<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_category_id')->constrained('room_categories')->restrictOnDelete();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->unsignedInteger('kapasitas');
            $table->string('lokasi');
            $table->text('fasilitas')->nullable();
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['tersedia', 'perbaikan', 'nonaktif'])->default('tersedia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};

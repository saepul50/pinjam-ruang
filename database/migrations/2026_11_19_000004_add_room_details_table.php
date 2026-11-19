<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('gedung', 100)->nullable();
            $table->unsignedTinyInteger('lantai')->nullable();
            $table->unsignedSmallInteger('luas_m2')->nullable();
            $table->string('foto_url', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['gedung', 'lantai', 'luas_m2', 'foto_url']);
        });
    }
};

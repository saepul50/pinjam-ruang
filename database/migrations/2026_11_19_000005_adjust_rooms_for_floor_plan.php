<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('rooms')->whereNull('lantai')->update(['lantai' => 1]);
        DB::table('rooms')->whereNull('gedung')->update(['gedung' => 'Gedung A']);

        Schema::table('rooms', function (Blueprint $table) {
            $table->string('gedung', 100)->default('Gedung A')->nullable(false)->change();
            $table->unsignedTinyInteger('lantai')->default(1)->nullable(false)->change();
            $table->dropColumn('luas_m2');
            $table->unsignedTinyInteger('posisi_denah')->nullable()->after('lantai');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->unique(['gedung', 'lantai', 'posisi_denah'], 'room_floor_slot_unique');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropUnique('room_floor_slot_unique');
            $table->dropColumn('posisi_denah');
            $table->unsignedSmallInteger('luas_m2')->nullable();
            $table->string('gedung', 100)->nullable()->change();
            $table->unsignedTinyInteger('lantai')->nullable()->change();
        });
    }
};

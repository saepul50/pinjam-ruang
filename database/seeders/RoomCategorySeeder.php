<?php

namespace Database\Seeders;

use App\Models\RoomCategory;
use Illuminate\Database\Seeder;

class RoomCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nama' => 'Ruang Kelas', 'deskripsi' => 'Ruang untuk kegiatan perkuliahan dan belajar.'],
            ['nama' => 'Laboratorium', 'deskripsi' => 'Ruang praktik dan penelitian.'],
            ['nama' => 'Aula', 'deskripsi' => 'Ruang serbaguna untuk acara berkapasitas besar.'],
            ['nama' => 'Ruang Rapat', 'deskripsi' => 'Ruang untuk rapat dan diskusi.'],
        ];

        foreach ($categories as $category) {
            RoomCategory::updateOrCreate(['nama' => $category['nama']], $category);
        }
    }
}

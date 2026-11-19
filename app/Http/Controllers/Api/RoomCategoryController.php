<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RoomCategory;
use Illuminate\Http\Request;

class RoomCategoryController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => RoomCategory::withCount('rooms')->orderBy('nama')->get(),
        ]);
    }

    public function show(string $id)
    {
        return response()->json([
            'data' => RoomCategory::withCount('rooms')->findOrFail($id),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:room_categories,nama',
            'deskripsi' => 'nullable|string',
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique' => 'Nama kategori sudah digunakan.',
        ]);

        $category = RoomCategory::create($validated);

        return response()->json([
            'message' => 'Kategori ruang berhasil ditambahkan.',
            'data' => $category,
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $category = RoomCategory::findOrFail($id);
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:room_categories,nama,' . $category->id,
            'deskripsi' => 'nullable|string',
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique' => 'Nama kategori sudah digunakan.',
        ]);

        $category->update($validated);

        return response()->json([
            'message' => 'Kategori ruang berhasil diperbarui.',
            'data' => $category,
        ]);
    }

    public function destroy(string $id)
    {
        $category = RoomCategory::withCount('rooms')->findOrFail($id);

        if ($category->rooms_count > 0) {
            return response()->json([
                'message' => 'Kategori tidak dapat dihapus karena masih memiliki ruang.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Kategori ruang berhasil dihapus.',
        ]);
    }
}

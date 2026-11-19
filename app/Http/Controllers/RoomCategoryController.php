<?php

namespace App\Http\Controllers;

use App\Models\RoomCategory;
use Illuminate\Http\Request;

class RoomCategoryController extends Controller
{
    public function index()
    {
        return view('room-categories.index', [
            'categories' => RoomCategory::withCount('rooms')->latest()->paginate(10),
        ]);
    }

    public function create()
    {
        return view('room-categories.create');
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

        RoomCategory::create($validated);

        return redirect()->route('room-categories.index')
            ->with('success', 'Kategori ruang berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        return view('room-categories.edit', [
            'category' => RoomCategory::findOrFail($id),
        ]);
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

        return redirect()->route('room-categories.index')
            ->with('success', 'Kategori ruang berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $category = RoomCategory::withCount('rooms')->findOrFail($id);

        if ($category->rooms_count > 0) {
            return back()->withErrors(['nama' => 'Kategori tidak dapat dihapus karena masih memiliki ruang.']);
        }

        $category->delete();

        return redirect()->route('room-categories.index')
            ->with('success', 'Kategori ruang berhasil dihapus.');
    }
}

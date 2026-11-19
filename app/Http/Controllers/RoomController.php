<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomCategory;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        return view('rooms.index', [
            'rooms' => Room::with('roomCategory')->latest()->paginate(10),
        ]);
    }

    public function create()
    {
        return view('rooms.create', [
            'categories' => RoomCategory::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_category_id' => 'required|integer|exists:room_categories,id',
            'kode' => 'required|string|max:255|unique:rooms,kode',
            'nama' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'lokasi' => 'required|string|max:255',
            'fasilitas' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:tersedia,perbaikan,nonaktif',
        ], [
            'room_category_id.required' => 'Kategori ruang wajib dipilih.',
            'kode.required' => 'Kode ruang wajib diisi.',
            'kode.unique' => 'Kode ruang sudah digunakan.',
            'nama.required' => 'Nama ruang wajib diisi.',
            'kapasitas.required' => 'Kapasitas ruang wajib diisi.',
            'lokasi.required' => 'Lokasi ruang wajib diisi.',
        ]);

        Room::create($validated);

        return redirect()->route('rooms.index')
            ->with('success', 'Ruang berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        return view('rooms.show', [
            'room' => Room::with('roomCategory')->findOrFail($id),
        ]);
    }

    public function edit(string $id)
    {
        return view('rooms.edit', [
            'room' => Room::findOrFail($id),
            'categories' => RoomCategory::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $room = Room::findOrFail($id);
        $validated = $request->validate([
            'room_category_id' => 'required|integer|exists:room_categories,id',
            'kode' => 'required|string|max:255|unique:rooms,kode,' . $room->id,
            'nama' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'lokasi' => 'required|string|max:255',
            'fasilitas' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:tersedia,perbaikan,nonaktif',
        ]);

        $room->update($validated);

        return redirect()->route('rooms.index')
            ->with('success', 'Ruang berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $room = Room::withCount(['bookings as active_bookings_count' => function ($query) {
            $query->whereIn('status', ['menunggu', 'disetujui']);
        }])->findOrFail($id);

        if ($room->active_bookings_count > 0) {
            return back()->withErrors(['nama' => 'Ruang tidak dapat dihapus karena masih memiliki peminjaman aktif.']);
        }

        $room->delete();

        return redirect()->route('rooms.index')
            ->with('success', 'Ruang berhasil dihapus.');
    }
}

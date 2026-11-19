<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomCategory;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::with('roomCategory');

        $gedung = $request->filled('gedung')
            ? $request->string('gedung')->toString()
            : Room::whereNotNull('gedung')->value('gedung');
        $lantai = $request->filled('lantai')
            ? $request->integer('lantai')
            : 1;

        if ($gedung) {
            $rooms->where('gedung', $gedung);
        }
        if ($lantai) {
            $rooms->where('lantai', $lantai);
        }
        if ($request->filled('kategori')) {
            $rooms->where('room_category_id', $request->integer('kategori'));
        }
        if ($request->filled('status')) {
            $rooms->where('status', $request->string('status'));
        }
        if ($request->filled('kapasitas_min')) {
            $rooms->where('kapasitas', '>=', $request->integer('kapasitas_min'));
        }
        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $rooms->where(function ($query) use ($search) {
                $query->where('kode', 'like', '%' . $search . '%')
                    ->orWhere('nama', 'like', '%' . $search . '%')
                    ->orWhere('lokasi', 'like', '%' . $search . '%');
            });
        }

        return view('rooms.index', [
            'rooms' => $rooms->latest()->paginate(10)->withQueryString(),
            'categories' => RoomCategory::orderBy('nama')->get(),
            'gedungs' => Room::whereNotNull('gedung')->distinct()->orderBy('gedung')->pluck('gedung'),
            'gedungAktif' => $gedung,
            'lantaiAktif' => $lantai,
            'ruangDenah' => (clone $rooms)->orderBy('nama')->get(),
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
            'gedung' => 'nullable|string|max:100',
            'lantai' => 'nullable|integer|min:1|max:20',
            'luas_m2' => 'nullable|integer|min:1',
            'foto_url' => 'nullable|url:http,https|max:255',
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

    public function show(Request $request, string $id)
    {
        $room = Room::with('roomCategory')->findOrFail($id);
        $tanggal = $request->filled('tanggal')
            ? $request->string('tanggal')->toString()
            : now()->toDateString();

        return view('rooms.show', [
            'room' => $room,
            'tanggal' => $tanggal,
            'jadwalTerisi' => $room->bookings()
                ->whereDate('tanggal', $tanggal)
                ->whereIn('status', ['menunggu', 'disetujui'])
                ->orderBy('jam_mulai')
                ->get(),
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
            'gedung' => 'nullable|string|max:100',
            'lantai' => 'nullable|integer|min:1|max:20',
            'luas_m2' => 'nullable|integer|min:1',
            'foto_url' => 'nullable|url:http,https|max:255',
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

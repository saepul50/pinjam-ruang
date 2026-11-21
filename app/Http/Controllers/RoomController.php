<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role === 'peminjam') {
            return $this->explore($request);
        }

        return $this->list($request);
    }

    public function list(Request $request)
    {
        $rooms = Room::with('roomCategory');
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
        ]);
    }

    private function explore(Request $request)
    {
        $gedung = $request->filled('gedung')
            ? $request->string('gedung')->toString()
            : Room::whereNotNull('gedung')->value('gedung');

        $lantaiList = Room::where('gedung', $gedung)
            ->whereNotNull('lantai')
            ->distinct()
            ->orderBy('lantai')
            ->pluck('lantai');

        $lantai = $request->filled('lantai')
            ? $request->integer('lantai')
            : ($lantaiList->first() ? (int) $lantaiList->first() : 1);

        if (! $lantaiList->contains($lantai)) {
            $lantai = (int) $lantaiList->first();
        }

        $ruangDenah = Room::with('roomCategory')
            ->where('gedung', $gedung)
            ->where('lantai', $lantai)
            ->orderBy('posisi_denah')
            ->get();

        return view('rooms.explore', [
            'gedungs' => Room::whereNotNull('gedung')->distinct()->orderBy('gedung')->pluck('gedung'),
            'gedungAktif' => $gedung,
            'lantaiAktif' => $lantai,
            'lantaiList' => $lantaiList,
            'ruangDenah' => $ruangDenah,
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
            'lokasi' => 'nullable|string|max:150',
            'fasilitas' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:tersedia,perbaikan,nonaktif',
            'gedung' => 'required|string|max:100',
            'lantai' => 'required|integer|min:1|max:20',
            'posisi_denah' => 'nullable|integer|between:1,4',
            'foto_url' => 'nullable|url:http,https|max:255',
        ], [
            'room_category_id.required' => 'Kategori ruang wajib dipilih.',
            'kode.required' => 'Kode ruang wajib diisi.',
            'kode.unique' => 'Kode ruang sudah digunakan.',
            'nama.required' => 'Nama ruang wajib diisi.',
            'kapasitas.required' => 'Kapasitas ruang wajib diisi.',
            'lokasi.required' => 'Lokasi ruang wajib diisi.',
        ]);

        DB::transaction(function () use ($validated) {
            $validated['posisi_denah'] = $this->resolveDenahSlot(
                $validated['gedung'],
                $validated['lantai'],
                $validated['posisi_denah'] ?? null
            );
            Room::create($validated);
        });

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
            'lokasi' => 'nullable|string|max:150',
            'fasilitas' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:tersedia,perbaikan,nonaktif',
            'gedung' => 'required|string|max:100',
            'lantai' => 'required|integer|min:1|max:20',
            'posisi_denah' => 'nullable|integer|between:1,4',
            'foto_url' => 'nullable|url:http,https|max:255',
        ]);

        DB::transaction(function () use ($room, $validated) {
            $validated['posisi_denah'] = $this->resolveDenahSlot(
                $validated['gedung'],
                $validated['lantai'],
                $validated['posisi_denah'] ?? null,
                $room->id
            );
            $room->update($validated);
        });

        return redirect()->route('rooms.index')
            ->with('success', 'Ruang berhasil diperbarui.');
    }

    private function resolveDenahSlot(
        string $gedung,
        int $lantai,
        ?int $posisi,
        ?int $exceptId = null
    ): int {
        $query = Room::where('gedung', $gedung)
            ->where('lantai', $lantai)
            ->lockForUpdate();

        if ($exceptId) {
            $query->whereKeyNot($exceptId);
        }

        $rooms = $query->get(['posisi_denah']);

        if ($rooms->count() >= 4) {
            throw ValidationException::withMessages([
                'lantai' => 'Lantai ini sudah penuh (maksimal 4 ruang)',
            ]);
        }

        if ($posisi !== null) {
            if ($rooms->contains('posisi_denah', $posisi)) {
                throw ValidationException::withMessages([
                    'posisi_denah' => 'Slot denah sudah terpakai',
                ]);
            }

            return $posisi;
        }

        $terpakai = $rooms->pluck('posisi_denah')->filter()->map(fn ($slot) => (int) $slot)->all();

        for ($slot = 1; $slot <= 4; $slot++) {
            if (! in_array($slot, $terpakai, true)) {
                return $slot;
            }
        }

        throw ValidationException::withMessages([
            'lantai' => 'Lantai ini sudah penuh (maksimal 4 ruang)',
        ]);
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

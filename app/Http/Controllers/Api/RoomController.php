<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::with('roomCategory');

        if ($request->filled('kategori')) {
            $rooms->whereHas('roomCategory', function ($query) use ($request) {
                $query->where('nama', 'like', '%' . $request->query('kategori') . '%');
            });
        }

        if ($request->filled('status')) {
            $rooms->where('status', $request->query('status'));
        }

        if ($request->filled('kapasitas_min')) {
            $rooms->where('kapasitas', '>=', (int) $request->query('kapasitas_min'));
        }
        if ($request->filled('gedung')) {
            $rooms->where('gedung', $request->query('gedung'));
        }
        if ($request->filled('lantai')) {
            $rooms->where('lantai', (int) $request->query('lantai'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $rooms->where(function ($query) use ($search) {
                $query->where('kode', 'like', '%' . $search . '%')
                    ->orWhere('nama', 'like', '%' . $search . '%')
                    ->orWhere('lokasi', 'like', '%' . $search . '%');
            });
        }

        return response()->json([
            'data' => $rooms->orderBy('nama')->paginate(10),
        ]);
    }

    public function show(string $id)
    {
        return response()->json([
            'data' => Room::with('roomCategory')->findOrFail($id),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRoom($request);
        $room = DB::transaction(function () use ($validated) {
            $validated['posisi_denah'] = $this->resolveDenahSlot(
                $validated['gedung'],
                $validated['lantai'],
                $validated['posisi_denah'] ?? null
            );

            return Room::create($validated);
        });

        return response()->json([
            'message' => 'Ruang berhasil ditambahkan.',
            'data' => $room->load('roomCategory'),
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $room = Room::findOrFail($id);
        $validated = $this->validateRoom($request, $room->id);
        DB::transaction(function () use ($room, $validated) {
            $validated['posisi_denah'] = $this->resolveDenahSlot(
                $validated['gedung'],
                $validated['lantai'],
                $validated['posisi_denah'] ?? null,
                $room->id
            );
            $room->update($validated);
        });

        return response()->json([
            'message' => 'Ruang berhasil diperbarui.',
            'data' => $room->load('roomCategory'),
        ]);
    }

    public function destroy(string $id)
    {
        $room = Room::whereHas('bookings', function ($query) {
            $query->whereIn('status', ['menunggu', 'disetujui']);
        })->find($id);

        if ($room) {
            return response()->json([
                'message' => 'Ruang tidak dapat dihapus karena masih memiliki peminjaman aktif.',
            ], 422);
        }

        $room = Room::findOrFail($id);
        $room->delete();

        return response()->json([
            'message' => 'Ruang berhasil dihapus.',
        ]);
    }

    public function availability(Request $request, string $id)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
        ]);

        Room::findOrFail($id);

        $bookings = Booking::where('room_id', $id)
            ->where('tanggal', $validated['tanggal'])
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->orderBy('jam_mulai')
            ->get(['id', 'jam_mulai', 'jam_selesai', 'status']);

        return response()->json([
            'room_id' => (int) $id,
            'tanggal' => $validated['tanggal'],
            'data' => $bookings,
        ]);
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

        if ($rooms->count() >= 7) {
            throw ValidationException::withMessages([
                'lantai' => 'Lantai ini sudah penuh (maksimal 7 ruang)',
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

        for ($slot = 1; $slot <= 7; $slot++) {
            if (! in_array($slot, $terpakai, true)) {
                return $slot;
            }
        }

        throw ValidationException::withMessages([
            'lantai' => 'Lantai ini sudah penuh (maksimal 7 ruang)',
        ]);
    }

    private function validateRoom(Request $request, ?int $roomId = null): array
    {
        $kodeRule = 'required|string|max:255|unique:rooms,kode';
        if ($roomId) {
            $kodeRule .= ',' . $roomId;
        }

        return $request->validate([
            'room_category_id' => 'required|integer|exists:room_categories,id',
            'kode' => $kodeRule,
            'nama' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'lokasi' => 'nullable|string|max:150',
            'fasilitas' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:tersedia,perbaikan,nonaktif',
            'gedung' => 'required|string|max:100',
            'lantai' => 'required|integer|min:1|max:20',
            'posisi_denah' => 'nullable|integer|between:1,7',
            'foto_url' => 'nullable|url:http,https|max:255',
        ], [
            'room_category_id.required' => 'Kategori ruang wajib dipilih.',
            'kode.required' => 'Kode ruang wajib diisi.',
            'kode.unique' => 'Kode ruang sudah digunakan.',
            'nama.required' => 'Nama ruang wajib diisi.',
            'kapasitas.required' => 'Kapasitas ruang wajib diisi.',
            'lokasi.required' => 'Lokasi ruang wajib diisi.',
        ]);
    }
}

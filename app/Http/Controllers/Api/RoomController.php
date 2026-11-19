<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;

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
        $room = Room::create($validated);

        return response()->json([
            'message' => 'Ruang berhasil ditambahkan.',
            'data' => $room->load('roomCategory'),
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $room = Room::findOrFail($id);
        $validated = $this->validateRoom($request, $room->id);
        $room->update($validated);

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
    }
}

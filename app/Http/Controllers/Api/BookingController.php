<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $bookings = Booking::with(['room.roomCategory', 'user', 'approver']);

        if (! in_array($user->role, ['admin', 'petugas'], true)) {
            $bookings->where('user_id', $user->id);
        }

        return response()->json([
            'data' => $bookings->latest()->paginate(10),
        ]);
    }

    public function show(Request $request, string $id)
    {
        $booking = Booking::with(['room.roomCategory', 'user', 'approver'])->findOrFail($id);
        $this->authorizeViewer($request, $booking);

        return response()->json([
            'data' => $booking,
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->role === 'peminjam', 403);

        $validated = $request->validate([
            'room_id' => 'required|integer|exists:rooms,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keperluan' => 'required|string|max:255',
            'jumlah_peserta' => 'nullable|integer|min:1',
        ], [
            'tanggal.after_or_equal' => 'Tanggal peminjaman tidak boleh sebelum hari ini.',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
        ]);

        $booking = DB::transaction(function () use ($validated, $request) {
            $room = Room::whereKey($validated['room_id'])->lockForUpdate()->firstOrFail();

            if ($room->status !== 'tersedia') {
                throw ValidationException::withMessages([
                    'room_id' => 'Ruang sedang tidak tersedia untuk dipinjam.',
                ]);
            }

            if (! empty($validated['jumlah_peserta']) && $validated['jumlah_peserta'] > $room->kapasitas) {
                throw ValidationException::withMessages([
                    'jumlah_peserta' => 'Jumlah peserta tidak boleh melebihi kapasitas ruang.',
                ]);
            }

            $jadwalAktif = Booking::where('room_id', $room->id)
                ->whereIn('status', ['menunggu', 'disetujui'])
                ->lockForUpdate()
                ->get(['tanggal', 'jam_mulai', 'jam_selesai'])
                ->filter(fn (Booking $booking) => $booking->tanggal->toDateString() === $validated['tanggal']);

            $berbenturan = $jadwalAktif->contains(function (Booking $booking) use ($validated) {
                return $validated['jam_mulai'] < $booking->jam_selesai
                    && $validated['jam_selesai'] > $booking->jam_mulai;
            });

            if ($berbenturan) {
                throw ValidationException::withMessages([
                    'jam_mulai' => 'Jadwal bentrok dengan peminjaman yang menunggu atau sudah disetujui.',
                ]);
            }

            $booking = new Booking($validated);
            $booking->user_id = $request->user()->id;
            $booking->status = 'menunggu';
            $booking->save();

            return $booking;
        });

        return response()->json([
            'message' => 'Pengajuan peminjaman berhasil dibuat.',
            'data' => $booking->load(['room.roomCategory', 'user']),
        ], 201);
    }

    public function updateStatus(Request $request, string $id)
    {
        $booking = Booking::findOrFail($id);
        $user = $request->user();

        abort_unless(in_array($user->role, ['admin', 'petugas'], true), 403);

        $validated = $request->validate([
            'status' => 'required|in:menunggu,disetujui,ditolak,dibatalkan,selesai',
            'catatan_admin' => 'nullable|string',
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status peminjaman tidak valid.',
        ]);

        if ($validated['status'] === 'ditolak' && empty($validated['catatan_admin'])) {
            return response()->json([
                'message' => 'Catatan penolakan wajib diisi.',
                'errors' => ['catatan_admin' => ['Catatan penolakan wajib diisi.']],
            ], 422);
        }

        if ($validated['status'] === 'disetujui') {
            $berbenturan = Booking::where('room_id', $booking->room_id)
                ->whereKeyNot($booking->id)
                ->whereDate('tanggal', $booking->tanggal->toDateString())
                ->whereIn('status', ['menunggu', 'disetujui'])
                ->lockForUpdate()
                ->get(['jam_mulai', 'jam_selesai'])
                ->contains(function (Booking $lain) use ($booking) {
                    return $booking->jam_mulai < $lain->jam_selesai
                        && $booking->jam_selesai > $lain->jam_mulai;
                });

            if ($berbenturan) {
                return response()->json([
                    'message' => 'Persetujuan ditolak karena jadwal bentrok dengan peminjaman lain.',
                    'errors' => ['status' => ['Jadwal peminjaman bentrok.']],
                ], 422);
            }
        }

        DB::transaction(function () use ($booking, $validated, $user) {
            $booking->update([
                'status' => $validated['status'],
                'approved_by' => in_array($validated['status'], ['disetujui', 'ditolak'], true)
                    ? $user->id
                    : $booking->approved_by,
                'catatan_admin' => $validated['catatan_admin'] ?? $booking->catatan_admin,
            ]);
        });

        return response()->json([
            'message' => 'Status peminjaman berhasil diperbarui.',
            'data' => $booking->load(['room', 'user', 'approver']),
        ]);
    }

    private function authorizeViewer(Request $request, Booking $booking): void
    {
        $user = $request->user();

        abort_unless(
            in_array($user->role, ['admin', 'petugas'], true) || $booking->user_id === $user->id,
            403
        );
    }
}

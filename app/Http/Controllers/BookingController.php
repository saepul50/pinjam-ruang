<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function guide()
    {
        return view('bookings.guide');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $bookings = Booking::with(['room', 'user', 'approver']);

        if (! in_array($user->role, ['admin', 'petugas'], true)) {
            $bookings->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $bookings->where('status', $request->string('status'));
        }

        $bookings = $bookings->latest()->paginate(10)->withQueryString();

        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        abort_unless(auth()->user()->role === 'peminjam', 403);

        return view('bookings.create', [
            'rooms' => Room::where('status', 'tersedia')->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->role === 'peminjam', 403);

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
            'jumlah_peserta.min' => 'Jumlah peserta minimal 1.',
        ]);

        DB::transaction(function () use ($validated) {
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
            $booking->user_id = auth()->id();
            $booking->status = 'menunggu';
            $booking->save();
        });

        return redirect()->route('bookings.index')
            ->with('success', 'Pengajuan peminjaman berhasil dibuat.');
    }

    public function show(string $id)
    {
        $booking = Booking::with(['room', 'user', 'approver'])->findOrFail($id);
        $this->authorizeViewer($booking);

        return view('bookings.show', compact('booking'));
    }

    public function cancel(string $id)
    {
        $booking = Booking::findOrFail($id);
        $user = auth()->user();

        abort_unless($booking->user_id === $user->id, 403);
        abort_unless($booking->status === 'menunggu', 422);

        $booking->update(['status' => 'dibatalkan']);

        return redirect()->route('bookings.index')
            ->with('success', 'Peminjaman berhasil dibatalkan.');
    }

    public function approve(string $id)
    {
        $booking = Booking::findOrFail($id);
        $validated = request()->validate([
            'catatan_admin' => 'nullable|string',
        ]);

        DB::transaction(function () use ($booking, $validated) {
            $room = Room::whereKey($booking->room_id)->lockForUpdate()->firstOrFail();
            $berbenturan = Booking::where('room_id', $room->id)
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
                throw ValidationException::withMessages([
                    'jam_mulai' => 'Persetujuan ditolak karena jadwal bentrok dengan peminjaman lain.',
                ]);
            }

            $booking->update([
                'status' => 'disetujui',
                'approved_by' => auth()->id(),
                'catatan_admin' => $validated['catatan_admin'] ?? null,
            ]);
        });

        return redirect()->route('bookings.index')
            ->with('success', 'Peminjaman berhasil disetujui.');
    }

    public function reject(Request $request, string $id)
    {
        $booking = Booking::findOrFail($id);
        $validated = $request->validate([
            'catatan_admin' => 'required|string',
        ], [
            'catatan_admin.required' => 'Catatan penolakan wajib diisi.',
        ]);

        $booking->update([
            'status' => 'ditolak',
            'approved_by' => auth()->id(),
            'catatan_admin' => $validated['catatan_admin'],
        ]);

        return redirect()->route('bookings.index')
            ->with('success', 'Peminjaman berhasil ditolak.');
    }

    public function complete(string $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update([
            'status' => 'selesai',
            'approved_by' => $booking->approved_by ?? auth()->id(),
        ]);

        return redirect()->route('bookings.index')
            ->with('success', 'Peminjaman ditandai selesai.');
    }

    private function authorizeViewer(Booking $booking): void
    {
        $user = auth()->user();

        abort_unless(
            in_array($user->role, ['admin', 'petugas'], true) || $booking->user_id === $user->id,
            403
        );
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $bookings = in_array($user->role, ['admin', 'petugas'], true)
            ? Booking::with(['room', 'user', 'approver'])->latest()->paginate(10)
            : Booking::with(['room', 'approver'])->where('user_id', $user->id)->latest()->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        return view('bookings.create', [
            'rooms' => Room::where('status', 'tersedia')->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
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

        $room = Room::findOrFail($validated['room_id']);

        if ($room->status !== 'tersedia') {
            return back()->withErrors(['room_id' => 'Ruang sedang tidak tersedia untuk dipinjam.'])->withInput();
        }

        if (! empty($validated['jumlah_peserta']) && $validated['jumlah_peserta'] > $room->kapasitas) {
            return back()->withErrors([
                'jumlah_peserta' => 'Jumlah peserta tidak boleh melebihi kapasitas ruang.',
            ])->withInput();
        }

        $berbenturan = Booking::where('room_id', $room->id)
            ->where('tanggal', $validated['tanggal'])
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->where('jam_mulai', '<', $validated['jam_selesai'])
            ->where('jam_selesai', '>', $validated['jam_mulai'])
            ->exists();

        if ($berbenturan) {
            return back()->withErrors([
                'jam_mulai' => 'Jadwal bentrok dengan peminjaman yang menunggu atau sudah disetujui.',
            ])->withInput();
        }

        $booking = Booking::create([
            ...$validated,
            'user_id' => auth()->id(),
            'status' => 'menunggu',
        ]);

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

        $booking->update([
            'status' => 'disetujui',
            'approved_by' => auth()->id(),
            'catatan_admin' => $validated['catatan_admin'] ?? null,
        ]);

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

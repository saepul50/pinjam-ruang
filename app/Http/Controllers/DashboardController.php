<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $data = [
            'totalRuang' => Room::count(),
        ];

        if (in_array($user->role, ['admin', 'petugas'], true)) {
            $data['peminjamanMenunggu'] = Booking::where('status', 'menunggu')->count();
            $data['peminjamanHariIni'] = Booking::whereDate('tanggal', now()->toDateString())->count();
        } else {
            $data['jumlahPeminjamanSaya'] = Booking::where('user_id', $user->id)->count();
            $data['peminjamanTerdekat'] = Booking::with('room')
                ->where('user_id', $user->id)
                ->whereIn('status', ['menunggu', 'disetujui'])
                ->whereDate('tanggal', '>=', now()->toDateString())
                ->orderBy('tanggal')
                ->orderBy('jam_mulai')
                ->first();
            $data['gedungs'] = Room::whereNotNull('gedung')->distinct()->orderBy('gedung')->pluck('gedung');
            $data['gedungAktif'] = Room::whereNotNull('gedung')->value('gedung');
            $data['lantaiAktif'] = 1;
            $data['ruangDenah'] = Room::with('roomCategory')
                ->where('gedung', $data['gedungAktif'])
                ->where('lantai', 1)
                ->orderBy('nama')
                ->get();
        }

        return view('dashboard', $data);
    }
}

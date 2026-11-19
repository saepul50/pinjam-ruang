<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $bookingQuery = in_array($user->role, ['admin', 'petugas'], true)
            ? Booking::query()
            : Booking::where('user_id', $user->id);

        return view('dashboard', [
            'totalRuang' => Room::count(),
            'peminjamanMenunggu' => (clone $bookingQuery)->where('status', 'menunggu')->count(),
            'peminjamanHariIni' => (clone $bookingQuery)->whereDate('tanggal', now()->toDateString())->count(),
        ]);
    }
}

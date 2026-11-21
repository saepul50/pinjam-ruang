<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
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
            $data['gedungAktif'] = $request->filled('gedung')
                ? $request->string('gedung')->toString()
                : (Room::whereNotNull('gedung')->distinct()->orderBy('gedung')->value('gedung') ?? 'Gedung A');
            $data['lantaiList'] = Room::where('gedung', $data['gedungAktif'])
                ->whereNotNull('lantai')
                ->distinct()
                ->orderBy('lantai')
                ->pluck('lantai');
            $data['lantaiAktif'] = $request->filled('lantai')
                ? $request->integer('lantai')
                : ((int) ($data['lantaiList']->first() ?? 1));
            if (! $data['lantaiList']->contains($data['lantaiAktif'])) {
                $data['lantaiAktif'] = (int) ($data['lantaiList']->first() ?? 1);
            }
            $data['ruangDenah'] = Room::with('roomCategory')
                ->where('gedung', $data['gedungAktif'])
                ->where('lantai', $data['lantaiAktif'])
                ->orderBy('posisi_denah')
                ->get();
        }

        return view('dashboard', $data);
    }
}

@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
    <h1>Detail Peminjaman</h1>
    <p><a href="{{ route('bookings.index') }}">&larr; Kembali</a></p>

    <table>
        <tr><th>Peminjam</th><td>{{ $booking->user->name }}</td></tr>
        <tr><th>Ruang</th><td>{{ $booking->room->nama }}</td></tr>
        <tr><th>Tanggal</th><td>{{ $booking->tanggal->format('d-m-Y') }}</td></tr>
        <tr><th>Jam</th><td>{{ $booking->jam_mulai }} - {{ $booking->jam_selesai }}</td></tr>
        <tr><th>Keperluan</th><td>{{ $booking->keperluan }}</td></tr>
        <tr><th>Jumlah Peserta</th><td>{{ $booking->jumlah_peserta ?? '-' }}</td></tr>
        <tr><th>Status</th><td><span class="badge badge-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td></tr>
        <tr><th>Catatan Admin</th><td>{{ $booking->catatan_admin ?? '-' }}</td></tr>
        <tr><th>Disetujui Oleh</th><td>{{ $booking->approver->name ?? '-' }}</td></tr>
    </table>

    @if (auth()->id() === $booking->user_id && $booking->status === 'menunggu')
        <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Batalkan peminjaman ini?')">Batalkan</button>
        </form>
    @endif

    @if (in_array(auth()->user()->role, ['admin', 'petugas'], true) && $booking->status === 'menunggu')
        <form action="{{ route('bookings.approve', $booking) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <label for="catatan_setuju">Catatan Admin</label>
            <input type="text" name="catatan_admin" id="catatan_setuju" value="{{ old('catatan_admin') }}">
            <button type="submit" class="btn btn-success">Setujui</button>
        </form>

        <form action="{{ route('bookings.reject', $booking) }}" method="POST">
            @csrf
            @method('PATCH')
            <label for="catatan_tolak">Catatan Penolakan</label>
            <textarea name="catatan_admin" id="catatan_tolak" rows="3">{{ old('catatan_admin') }}</textarea>
            @error('catatan_admin') <div class="error">{{ $message }}</div> @enderror
            <button type="submit" class="btn btn-danger">Tolak</button>
        </form>
    @endif

    @if (in_array(auth()->user()->role, ['admin', 'petugas'], true) && $booking->status === 'disetujui')
        <form action="{{ route('bookings.complete', $booking) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-primary">Tandai Selesai</button>
        </form>
    @endif
@endsection

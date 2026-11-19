@extends('layouts.app')

@section('title', 'Peminjaman')

@section('content')
    <h1>Peminjaman</h1>
    <p><a href="{{ route('bookings.create') }}" class="btn btn-primary">+ Ajukan Peminjaman</a></p>

    @if (in_array(auth()->user()->role, ['admin', 'petugas'], true))
        <form method="GET" action="{{ route('bookings.index') }}">
            <label for="status">Filter Status</label>
            <select name="status" id="status">
                <option value="">Semua</option>
                @foreach (['menunggu', 'disetujui', 'ditolak', 'dibatalkan', 'selesai'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </form>
    @endif

    <table>
        <thead>
            <tr>
                @if (in_array(auth()->user()->role, ['admin', 'petugas'], true))
                    <th>Peminjam</th>
                @endif
                <th>Ruang</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Keperluan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bookings as $booking)
                <tr>
                    @if (in_array(auth()->user()->role, ['admin', 'petugas'], true))
                        <td>{{ $booking->user->name }}</td>
                    @endif
                    <td>{{ $booking->room->nama }}</td>
                    <td>{{ $booking->tanggal->format('d-m-Y') }}</td>
                    <td>{{ $booking->jam_mulai }} - {{ $booking->jam_selesai }}</td>
                    <td>{{ $booking->keperluan }}</td>
                    <td><span class="badge badge-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td>
                    <td><a class="btn btn-primary" href="{{ route('bookings.show', $booking) }}">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="7">Belum ada peminjaman.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $bookings->links() }}
@endsection

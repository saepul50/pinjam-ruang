@extends('layouts.app')

@section('title', 'Peminjaman')

@section('content')
    @include('partials.page-header', [
        'eyebrow' => 'Pengajuan Ruang',
        'title' => 'Peminjaman',
        'subtitle' => in_array(auth()->user()->role, ['admin', 'petugas'], true)
            ? 'Kelola seluruh pengajuan peminjaman ruang.'
            : 'Pantau pengajuan peminjaman milik Anda.',
    ])
    @if (auth()->user()->role === 'peminjam')
        <p><a href="{{ route('bookings.create') }}" class="btn btn-primary">+ Ajukan Peminjaman</a></p>
    @endif

    @if (in_array(auth()->user()->role, ['admin', 'petugas'], true))
        <form class="searchbar" method="GET" action="{{ route('bookings.index') }}">
            <div class="searchfield">
                <label for="status">Filter Status</label>
                <select name="status" id="status">
                    <option value="">Semua</option>
                    @foreach (['menunggu', 'disetujui', 'ditolak', 'dibatalkan', 'selesai'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </form>
    @endif

    <div class="card table-wrap">
    <table class="table">
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
                    <td>@include('partials.status-badge', ['status' => $booking->status])</td>
                    <td><a class="btn btn-sm btn-secondary" href="{{ route('bookings.show', $booking) }}">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="7">Belum ada peminjaman.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>

    {{ $bookings->links() }}
@endsection

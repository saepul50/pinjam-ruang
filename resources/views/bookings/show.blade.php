@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
    @include('partials.page-header', [
        'eyebrow' => 'Detail Pengajuan',
        'title' => 'Peminjaman Ruang',
        'subtitle' => 'Informasi lengkap dan tindakan persetujuan.',
    ])

    <p><a class="btn btn-secondary" href="{{ route('bookings.index') }}">&larr; Kembali</a></p>

    <div class="workspace">
        <section class="card">
            @include('partials.status-badge', ['status' => $booking->status])
            <div class="table-wrap">
                <table class="table">
                    <tr><th>Peminjam</th><td>{{ $booking->user->name }}</td></tr>
                    <tr><th>Ruang</th><td>{{ $booking->room->nama }}</td></tr>
                    <tr><th>Tanggal</th><td>{{ $booking->tanggal->format('d-m-Y') }}</td></tr>
                    <tr><th>Jam</th><td>{{ $booking->jam_mulai }} - {{ $booking->jam_selesai }}</td></tr>
                    <tr><th>Keperluan</th><td>{{ $booking->keperluan }}</td></tr>
                    <tr><th>Jumlah Peserta</th><td>{{ $booking->jumlah_peserta ?? '-' }}</td></tr>
                    <tr><th>Status</th><td>@include('partials.status-badge', ['status' => $booking->status])</td></tr>
                    <tr><th>Catatan Admin</th><td>{{ $booking->catatan_admin ?? '-' }}</td></tr>
                    <tr><th>Disetujui Oleh</th><td>{{ $booking->approver->name ?? '-' }}</td></tr>
                </table>
            </div>
        </section>

        <aside class="detailcard">
            <div class="detailbody">
                <h2>Aksi Peminjaman</h2>

                @if (auth()->id() === $booking->user_id && $booking->status === 'menunggu')
                    <form action="{{ route('bookings.cancel', $booking) }}" method="POST" data-confirm-delete>
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-full" data-confirm-delete>Batalkan</button>
                    </form>
                @endif

                @if (in_array(auth()->user()->role, ['admin', 'petugas'], true) && $booking->status === 'menunggu')
                    <form action="{{ route('bookings.approve', $booking) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="field">
                            <label for="catatan_setuju">Catatan Admin</label>
                            <input class="input" type="text" name="catatan_admin" id="catatan_setuju" value="{{ old('catatan_admin') }}">
                        </div>
                        <button type="submit" class="btn btn-primary btn-full">Setujui</button>
                    </form>

                    <form action="{{ route('bookings.reject', $booking) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="field">
                            <label for="catatan_tolak">Catatan Penolakan</label>
                            <textarea class="textarea" name="catatan_admin" id="catatan_tolak" rows="3">{{ old('catatan_admin') }}</textarea>
                            @error('catatan_admin') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-danger btn-full">Tolak</button>
                    </form>
                @endif

                @if (in_array(auth()->user()->role, ['admin', 'petugas'], true) && $booking->status === 'disetujui')
                    <form action="{{ route('bookings.complete', $booking) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-primary btn-full">Tandai Selesai</button>
                    </form>
                @endif
            </div>
        </aside>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Ajukan Peminjaman')

@section('content')
    <h1>Ajukan Peminjaman</h1>
    <p><a href="{{ route('bookings.index') }}">&larr; Kembali</a></p>

    <form action="{{ route('bookings.store') }}" method="POST">
        @csrf

        <label for="room_id">Ruang</label>
        <select name="room_id" id="room_id">
            <option value="">-- Pilih Ruang --</option>
            @foreach ($rooms as $room)
                <option value="{{ $room->id }}" @selected(old('room_id', request('room_id')) == $room->id)>
                    {{ $room->nama }} (Kapasitas {{ $room->kapasitas }})
                </option>
            @endforeach
        </select>
        @error('room_id') <div class="error">{{ $message }}</div> @enderror

        <label for="tanggal">Tanggal</label>
        <input type="date" name="tanggal" id="tanggal" min="{{ now()->toDateString() }}" value="{{ old('tanggal', now()->toDateString()) }}">
        @error('tanggal') <div class="error">{{ $message }}</div> @enderror

        <label for="jam_mulai">Jam Mulai</label>
        <input type="time" name="jam_mulai" id="jam_mulai" value="{{ old('jam_mulai') }}">
        @error('jam_mulai') <div class="error">{{ $message }}</div> @enderror

        <label for="jam_selesai">Jam Selesai</label>
        <input type="time" name="jam_selesai" id="jam_selesai" value="{{ old('jam_selesai') }}">
        @error('jam_selesai') <div class="error">{{ $message }}</div> @enderror

        <label for="keperluan">Keperluan</label>
        <input type="text" name="keperluan" id="keperluan" value="{{ old('keperluan') }}">
        @error('keperluan') <div class="error">{{ $message }}</div> @enderror

        <label for="jumlah_peserta">Jumlah Peserta</label>
        <input type="number" min="1" name="jumlah_peserta" id="jumlah_peserta" value="{{ old('jumlah_peserta') }}">
        @error('jumlah_peserta') <div class="error">{{ $message }}</div> @enderror

        <button type="submit" class="btn btn-primary">Ajukan</button>
    </form>
@endsection

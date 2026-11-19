@extends('layouts.app')

@section('title', 'Ubah Ruang')

@section('content')
    <h1>Ubah Ruang</h1>
    <p><a href="{{ route('rooms.index') }}">&larr; Kembali</a></p>
    <form action="{{ route('rooms.update', $room) }}" method="POST">
        @method('PUT')
        @include('rooms._form')
    </form>
@endsection

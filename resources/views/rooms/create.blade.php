@extends('layouts.app')

@section('title', 'Tambah Ruang')

@section('content')
    <h1>Tambah Ruang</h1>
    <p><a href="{{ route('rooms.index') }}">&larr; Kembali</a></p>
    <form action="{{ route('rooms.store') }}" method="POST">
        @include('rooms._form')
    </form>
@endsection

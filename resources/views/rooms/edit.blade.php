@extends('layouts.app')

@section('title', 'Ubah Ruang')

@section('content')
    @include('partials.page-header', ['eyebrow' => 'Kelola Ruang', 'title' => 'Ubah Ruang', 'subtitle' => 'Perbarui informasi ruang dan statusnya.'])
    <p><a class="btn btn-secondary" href="{{ route('rooms.index') }}">&larr; Kembali</a></p>
    <form class="card" action="{{ route('rooms.update', $room) }}" method="POST">
        @method('PUT')
        @include('rooms._form')
    </form>
@endsection

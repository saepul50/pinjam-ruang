@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
    <h1>403 - Akses Ditolak</h1>
    <p>Anda tidak memiliki izin untuk membuka halaman ini.</p>
    <a class="btn btn-primary" href="{{ route('dashboard') }}">Kembali ke Dashboard</a>
@endsection

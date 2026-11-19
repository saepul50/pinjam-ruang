@extends('layouts.app')

@section('title', 'Tidak Ditemukan')

@section('content')
    <h1>404 - Halaman Tidak Ditemukan</h1>
    <p>Data atau halaman yang Anda cari tidak tersedia.</p>
    <a class="btn btn-primary" href="{{ route('dashboard') }}">Kembali ke Dashboard</a>
@endsection

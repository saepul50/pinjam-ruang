@extends('layouts.app')

@section('title', 'Tidak Ditemukan')

@section('content')
    <section class="card error-page-card">
        <div class="eyebrow">404</div>
        <h1 class="page-title">Halaman Tidak Ditemukan</h1>
        <p class="page-intro">Data atau halaman yang Anda cari tidak tersedia.</p>
        <a class="btn btn-primary" href="{{ route('dashboard') }}">Kembali ke Dashboard</a>
    </section>
@endsection

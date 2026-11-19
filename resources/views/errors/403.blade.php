@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
    <section class="card error-page-card">
        <div class="eyebrow">403</div>
        <h1 class="page-title">Akses Ditolak</h1>
        <p class="page-intro">Anda tidak memiliki izin untuk membuka halaman ini.</p>
        <a class="btn btn-primary" href="{{ route('dashboard') }}">Kembali ke Dashboard</a>
    </section>
@endsection

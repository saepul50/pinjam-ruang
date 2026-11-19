@extends('layouts.app')

@section('title', 'Jelajahi Ruang')

@section('content')
    @include('partials.page-header', [
        'eyebrow' => 'ROOM DISCOVERY',
        'title' => 'Temukan ruang yang tepat untuk setiap ide.',
        'subtitle' => 'Jelajahi gedung, pilih lantai, dan temukan ruangan kampus yang tersedia.',
    ])

    <form class="searchbar" method="GET" action="{{ route('rooms.index') }}">
        <div class="searchfield">
            <label for="gedung">Gedung</label>
            <select name="gedung" id="gedung">
                @foreach ($gedungs as $gedung)
                    <option value="{{ $gedung }}" @selected($gedungAktif === $gedung)>{{ $gedung }}</option>
                @endforeach
            </select>
        </div>
        <div class="searchfield">
            <label for="lantai">Lantai</label>
            <select name="lantai" id="lantai">
                @foreach ([1, 2, 3] as $lantai)
                    <option value="{{ $lantai }}" @selected($lantaiAktif === $lantai)>Lantai {{ $lantai }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Tampilkan Denah →</button>
    </form>

    @include('partials.explore-workspace')
@endsection

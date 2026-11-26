@extends('layouts.app')

@section('title', 'Jelajahi Ruang')

@section('content')
    @include('partials.page-header', [
        'eyebrow' => 'JELAJAHI KAMPUS',
        'title' => 'Pilih ruang langsung dari denah.',
        'subtitle' => 'Pilih lantai untuk melihat ruang dari seluruh gedung kampus.',
    ])

    {{-- <form class="searchbar" method="GET" action="{{ route('rooms.index') }}">
        <div class="searchfield">
            <label for="lantai">Lantai</label>
            <select name="lantai" id="lantai">
                @foreach ($lantaiList as $lantai)
                    <option value="{{ $lantai }}" @selected($lantaiAktif === $lantai)>Lantai {{ $lantai }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Tampilkan Denah →</button>
    </form> --}}

    @include('partials.explore-workspace', ['floorRoute' => 'rooms.index'])
@endsection

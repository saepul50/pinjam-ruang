<article class="roomtile">
    @if (! empty($room->foto_url))
        <div class="detailimage"><img src="{{ $room->foto_url }}" alt="{{ $room->nama }}"></div>
    @else
        <div class="roomtile-placeholder" aria-label="Foto ruang belum tersedia">⌂</div>
    @endif
    <div class="roomtile-body">
        <strong>{{ $room->nama }}</strong>
        <div class="roomtile-meta">{{ $room->kode }} · {{ $room->kapasitas }} orang</div>
        <div class="roomtile-foot">
            @include('partials.status-badge', ['status' => $room->status])
            <a class="btn btn-sm btn-secondary" href="{{ route('rooms.show', $room) }}">Lihat →</a>
        </div>
    </div>
</article>

<article class="roomtile">
    @if (! empty($room->foto_url))
        <img src="{{ $room->foto_url }}" alt="{{ $room->nama }}">
    @else
        <div class="roomtile-placeholder" aria-label="Foto ruang belum tersedia">⌂</div>
    @endif
    <div class="tilebody">
        <strong>{{ $room->nama }}</strong>
        <span>{{ $room->kode }} · {{ $room->kapasitas }} orang</span>
        <div class="tilefoot">
            @include('partials.status-badge', ['status' => $room->status])
            <a href="{{ route('rooms.show', $room) }}" style="font-size:11px;color:#4169e1;font-weight:700">Lihat →</a>
        </div>
    </div>
</article>

<section>
    <div class="sectionhead">
        <div>
            <h2>Jelajahi berdasarkan denah</h2>
            <p>Pilih lantai dan ruang untuk melihat informasi ruangan.</p>
        </div>
    </div>

    <div class="workspace">
        <div class="mapcard">
            <div class="maptop">
                <div class="building">
                    {{ $gedungAktif }}
                    <small>Jl. Kampus Raya · Area Akademik</small>
                </div>
                <div class="floor-switch">
                    @foreach ([1, 2, 3] as $lantai)
                        <a class="{{ $lantaiAktif === $lantai ? 'active' : '' }}"
                           href="{{ route('rooms.index', ['gedung' => $gedungAktif, 'lantai' => $lantai]) }}">
                            Lantai {{ $lantai }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="legend">
                <span><i class="dot dot-selected"></i>Dipilih</span>
                <span><i class="dot dot-available"></i>Tersedia</span>
                <span><i class="dot dot-common"></i>Area umum</span>
            </div>

            <div class="mapcanvas">
                <div class="floorplan">
                    @for ($slot = 1; $slot <= 4; $slot++)
                        @php
                            $room = $ruangDenah->first(fn ($item) => (int) $item->posisi_denah === $slot);
                            $selected = $room
                                ? ((int) request('room_id') === $room->id || ((int) request('room_id') === 0 && $slot === 1))
                                : false;
                        @endphp

                        @if ($room)
                            <div class="room-cell {{ $selected ? 'selected' : '' }} {{ in_array($room->status, ['perbaikan', 'nonaktif']) ? 'is-unavailable' : '' }}">
                                <span class="room-name">{{ $room->nama }}</span>
                                <a class="roompin"
                                   href="{{ route('rooms.index', ['gedung' => $gedungAktif, 'lantai' => $lantaiAktif, 'room_id' => $room->id]) }}"
                                   aria-label="Pilih {{ $room->nama }}">
                                    <span>{{ $room->kode }}</span>
                                </a>
                            </div>
                        @else
                            <div class="room-cell room-empty"><span>Kosong</span></div>
                        @endif
                    @endfor
                </div>
                <div class="maplabel">DENAH ILUSTRASI · LANTAI {{ $lantaiAktif }}</div>
            </div>
        </div>

        @php
            $ruangTerpilih = $ruangDenah->firstWhere('id', (int) request('room_id')) ?? $ruangDenah->first();
        @endphp

        <aside class="detailcard">
            @if ($ruangTerpilih)
                @if (! empty($ruangTerpilih->foto_url))
                    <div class="detailimage">
                        <img src="{{ $ruangTerpilih->foto_url }}" alt="{{ $ruangTerpilih->nama }}">
                        @include('partials.status-badge', ['status' => $ruangTerpilih->status])
                    </div>
                @else
                    <div class="detailimage">
                        <span class="detail-placeholder">⌂</span>
                        @include('partials.status-badge', ['status' => $ruangTerpilih->status])
                    </div>
                @endif
                <div class="detailbody">
                    <h3>{{ $ruangTerpilih->nama }}</h3>
                    <div class="sub">{{ $ruangTerpilih->lokasi_lengkap }}</div>
                    <div class="stats">
                        @include('partials.stat-card', ['label' => 'Kapasitas', 'value' => $ruangTerpilih->kapasitas . ' orang'])
                        @include('partials.stat-card', ['label' => 'Lokasi', 'value' => ($ruangTerpilih->lantai ? 'Lantai ' . $ruangTerpilih->lantai : '-')])
                        @include('partials.stat-card', ['label' => 'Status', 'value' => ucfirst($ruangTerpilih->status)])
                    </div>
                    <h4>Fasilitas ruangan</h4>
                    <div class="chips">
                        @forelse (array_filter(array_map('trim', explode(',', (string) $ruangTerpilih->fasilitas))) as $fasilitas)
                            <span class="chip">{{ $fasilitas }}</span>
                        @empty
                            <span class="chip">Fasilitas belum tersedia</span>
                        @endforelse
                    </div>
                    @if ($ruangTerpilih->status === 'tersedia')
                        <a class="btn btn-primary btn-full" href="{{ route('rooms.show', $ruangTerpilih) }}">Lihat detail &amp; booking →</a>
                    @else
                        <a class="btn btn-secondary btn-full" href="{{ route('rooms.show', $ruangTerpilih) }}">Lihat detail ruang →</a>
                    @endif
                </div>
            @else
                @include('partials.empty-state', ['text' => 'Pilih lantai yang memiliki ruang.'])
            @endif
        </aside>
    </div>

    <div class="sectionhead" id="daftar">
        <div>
            <h2>Ruangan di lantai ini</h2>
            <p>Klik salah satu kartu untuk melihat overview ruangan.</p>
        </div>
    </div>
    <section class="rooms">
        @forelse ($ruangDenah as $room)
            @include('partials.room-card', ['room' => $room])
        @empty
            @include('partials.empty-state', ['text' => 'Belum ada ruangan pada lantai ini.'])
        @endforelse
    </section>
</section>

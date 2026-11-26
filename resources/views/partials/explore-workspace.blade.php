<section>
    <div class="sectionhead">
        <div>
            <h2>Jelajahi berdasarkan denah</h2>
            <p>Pilih lantai untuk melihat ruang dari seluruh gedung kampus.</p>
        </div>
    </div>

    <div class="workspace">
        <div class="mapcard">
            <div class="maptop">
                <div class="building">
                    {{ $gedungAktif }}
                    <small>{{ $ruangDenah->count() }} ruang di lantai {{ $lantaiAktif }}</small>
                </div>
                <div class="floor-switch">
                    @foreach ($lantaiList as $lantai)
                        <a class="{{ $lantaiAktif === $lantai ? 'active' : '' }}"
                           href="{{ route($floorRoute, ['lantai' => $lantai]) }}">
                            Lantai {{ $lantai }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="legend">
                <span><i class="dot dot-available"></i>Tersedia</span>
                <span><i class="dot dot-used"></i>Digunakan</span>
                <span><i class="dot dot-maintenance"></i>Dalam perawatan</span>
            </div>

            <div class="mapcanvas">
                @php
                    $ruangTerpilih = $ruangDenah->firstWhere('id', (int) request('room_id')) ?? $ruangDenah->first();
                    $posisiRuang = [
                        1 => ['x' => 50, 'y' => 58, 'width' => 215, 'height' => 208],
                        2 => ['x' => 275, 'y' => 58, 'width' => 215, 'height' => 208],
                        3 => ['x' => 500, 'y' => 58, 'width' => 215, 'height' => 208],
                        4 => ['x' => 725, 'y' => 58, 'width' => 225, 'height' => 208],
                        5 => ['x' => 150, 'y' => 456, 'width' => 215, 'height' => 190],
                        6 => ['x' => 375, 'y' => 456, 'width' => 215, 'height' => 190],
                        7 => ['x' => 600, 'y' => 456, 'width' => 215, 'height' => 190],
                    ];
                @endphp
                <svg class="floorplan-svg" viewBox="0 0 1000 700" role="img" aria-label="Denah seluruh gedung, lantai {{ $lantaiAktif }}, {{ $ruangDenah->count() }} ruang">
                    <rect class="plan-ground" x="0" y="0" width="1000" height="700" rx="10"/>
                    <path class="plan-building" d="M34 40H966V660H34z"/>
                    <path class="plan-window" d="M75 40h120v10H75zm245 0h120v10H320zm245 0h120v10H565zm215 0h120v10H780z"/>
                    <path class="plan-wall" d="M270 50v220m225-220v220m225-220v220M40 276H137m40 0H362m40 0H587m40 0H817m40 0H960M145 448H237m40 0H462m40 0H687m40 0H850M370 450v198m225-198v198"/>

                    <g class="plan-restroom">
                        <path d="M48 294h108v66H48zm0 78h108v66H48z"/>
                        <text x="102" y="324" text-anchor="middle">TOILET PRIA</text>
                        <text x="102" y="408" text-anchor="middle">TOILET WANITA</text>
                    </g>
                    <g class="plan-stairs">
                        <path d="M852 294h100v66H852z"/>
                        <path d="M865 348v-40h12v40m8 0v-40h12v40m8 0v-40h12v40m8 0v-40h12v40"/>
                        <text x="902" y="377" text-anchor="middle">TANGGA</text>
                    </g>
                    <g class="plan-lift">
                        <path d="M852 388h100v66H852z"/>
                        <rect x="883" y="400" width="38" height="32" rx="3"/>
                        <path d="M902 405v20m-7-7 7 7 7-7"/>
                        <text x="902" y="449" text-anchor="middle">LIFT</text>
                    </g>

                    @for ($slot = 1; $slot <= 7; $slot++)
                        @php
                            $room = $ruangDenah->values()->get($slot - 1);
                            $position = $posisiRuang[$slot] ?? null;
                            $isSelected = $room && $ruangTerpilih && $ruangTerpilih->id === $room->id;
                            $roomStatus = $room
                                ? ($room->status !== 'tersedia'
                                    ? 'maintenance'
                                    : ($room->booking_disetujui_pada_tanggal ? 'used' : 'available'))
                                : null;
                            $roomNameLines = [];
                            if ($room) {
                                $currentNameLine = '';
                                foreach (preg_split('/\s+/u', trim($room->nama)) as $word) {
                                    while (mb_strlen($word) > 16) {
                                        $wordPart = mb_substr($word, 0, 16);
                                        if ($currentNameLine !== '') {
                                            $roomNameLines[] = $currentNameLine;
                                            $currentNameLine = '';
                                        }
                                        $roomNameLines[] = $wordPart;
                                        $word = mb_substr($word, 16);
                                    }
                                    if ($currentNameLine !== '' && mb_strlen($currentNameLine . ' ' . $word) > 16) {
                                        $roomNameLines[] = $currentNameLine;
                                        $currentNameLine = '';
                                    }
                                    $currentNameLine .= ($currentNameLine === '' ? '' : ' ') . $word;
                                }
                                if ($currentNameLine !== '') {
                                    $roomNameLines[] = $currentNameLine;
                                }
                            }
                        @endphp
                        @if ($room)
                            <a class="floor-room-link" href="{{ route($floorRoute, ['lantai' => $lantaiAktif, 'room_id' => $room->id]) }}" aria-label="Pilih {{ $room->nama }} dari {{ $room->gedung }}, {{ $roomStatus === 'available' ? 'tersedia' : ($roomStatus === 'used' ? 'digunakan hari ini' : 'dalam perawatan atau tidak tersedia') }}">
                                <g class="floor-room is-{{ $roomStatus }} {{ $isSelected ? 'is-selected' : '' }}">
                                    <rect class="room-floor" x="{{ $position['x'] }}" y="{{ $position['y'] }}" width="{{ $position['width'] }}" height="{{ $position['height'] }}"/>
                                    <text class="room-name" text-anchor="middle" dominant-baseline="middle">
                                        @foreach ($roomNameLines as $line)
                                            <tspan x="{{ $position['x'] + $position['width'] / 2 }}" @if ($loop->first) y="{{ $position['y'] + $position['height'] / 2 - ((count($roomNameLines) - 1) * 13) }}" @else dy="26" @endif>{{ $line }}</tspan>
                                        @endforeach
                                    </text>
                                    <circle class="room-status-dot is-{{ $roomStatus }}" cx="{{ $position['x'] + $position['width'] - 18 }}" cy="{{ $position['y'] + 18 }}" r="9">
                                        <title>{{ $roomStatus === 'available' ? 'Tersedia' : ($roomStatus === 'used' ? 'Digunakan hari ini' : 'Dalam perawatan atau tidak tersedia') }}</title>
                                    </circle>
                                </g>
                            </a>
                        @else
                            <g class="floor-room is-off" aria-label="Slot ruang tidak terpakai">
                                <rect class="room-floor" x="{{ $position['x'] }}" y="{{ $position['y'] }}" width="{{ $position['width'] }}" height="{{ $position['height'] }}"/>
                            </g>
                        @endif
                    @endfor
                    <path class="plan-entrance" d="M440 660v-20h120v20m-60 0v-20"/>
                </svg>
                <div class="maplabel">Denah Ruangan Lantai {{ $lantaiAktif }}</div>
            </div>
        </div>

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

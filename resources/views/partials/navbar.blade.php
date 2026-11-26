<aside class="topbar" aria-label="Navigasi samping">
    <a class="brand" href="{{ route('dashboard') }}">
        <span class="brandmark" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none">
                <path d="M3 9.5 12 4l9 5.5M5 10v9m4-9v9m6-9v9m4-9v9M3 20h18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        <span class="brandname">pinjam<span>ruang.</span></span>
    </a>

    @auth
        <button type="button" class="menu-toggle" aria-label="Buka navigasi" aria-expanded="false" aria-controls="sidebar-content">
            <span></span><span></span><span></span>
        </button>
        <button type="button" class="nav-backdrop" aria-label="Tutup navigasi" tabindex="-1"></button>

        <div class="sidebar-content" id="sidebar-content">
            <nav class="nav" aria-label="Navigasi utama">
                @if (auth()->user()->role === 'peminjam')
                    <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3.5 10 8.5-7 8.5 7v10h-6v-6h-5v6h-6z"/></svg><span>Beranda</span>
                    </a>
                    <a class="{{ request()->routeIs('rooms.index') ? 'active' : '' }}" href="{{ route('rooms.index') }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-6.2 7-12a7 7 0 1 0-14 0c0 5.8 7 12 7 12Z"/><circle cx="12" cy="9" r="2.2"/></svg><span>Jelajahi</span>
                    </a>
                    <a class="{{ request()->routeIs('rooms.list') ? 'active' : '' }}" href="{{ route('rooms.list') }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5h12M8 12h12M8 19h12"/><circle cx="4" cy="5" r=".7"/><circle cx="4" cy="12" r=".7"/><circle cx="4" cy="19" r=".7"/></svg><span>Daftar Ruang</span>
                    </a>
                    <a class="{{ request()->routeIs('booking.guide') ? 'active' : '' }}" href="{{ route('booking.guide') }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M9.6 9a2.5 2.5 0 1 1 4.5 1.5c-1.3 1.4-2.1 1.8-2.1 3.5M12 17.5h.01"/></svg><span>Cara Booking</span>
                    </a>
                    <a class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}" href="{{ route('bookings.index') }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="5" width="16" height="16" rx="2"/><path d="M8 3v4m8-4v4M4 10h16"/></svg><span>Peminjaman Saya</span>
                    </a>
                @else
                    <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3.5 10 8.5-7 8.5 7v10h-6v-6h-5v6h-6z"/></svg><span>Dashboard</span>
                    </a>
                    <a class="{{ request()->routeIs('rooms.*') ? 'active' : '' }}" href="{{ route('rooms.index') }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-6.2 7-12a7 7 0 1 0-14 0c0 5.8 7 12 7 12Z"/><circle cx="12" cy="9" r="2.2"/></svg><span>Ruang</span>
                    </a>
                    <a class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}" href="{{ route('bookings.index') }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="5" width="16" height="16" rx="2"/><path d="M8 3v4m8-4v4M4 10h16"/></svg><span>Peminjaman</span>
                    </a>
                @endif
                @if (auth()->user()->role === 'admin')
                    <a class="{{ request()->routeIs('room-categories.*') ? 'active' : '' }}" href="{{ route('room-categories.index') }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16M4 12h16M4 19h16"/><circle cx="8" cy="5" r="2"/><circle cx="15" cy="12" r="2"/></svg><span>Kategori Ruang</span>
                    </a>
                @endif
            </nav>

            <div class="profile">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <span class="profile-greeting"><small>Masuk sebagai</small>{{ explode(' ', auth()->user()->name)[0] }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-button" aria-label="Keluar" title="Keluar">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 4H5v16h5m5-4 4-4-4-4m4 4H9"/></svg>
                    </button>
                </form>
            </div>
        </div>
    @endauth
</aside>

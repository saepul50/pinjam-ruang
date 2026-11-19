<header class="topbar">
    <a class="brand" href="{{ route('dashboard') }}">
        <span class="brandmark">⌂</span>
        <span>pinjam</span><span>ruang.</span>
    </a>

    @auth
        <button type="button" class="menu-toggle" aria-label="Buka menu" aria-expanded="false">☰</button>
        <nav class="nav" aria-label="Navigasi utama">
            <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
            <a class="{{ request()->routeIs('rooms.*') ? 'active' : '' }}" href="{{ route('rooms.index') }}">Ruang</a>
            <a class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}" href="{{ route('bookings.index') }}">Peminjaman</a>
            @if (auth()->user()->role === 'admin')
                <a class="{{ request()->routeIs('room-categories.*') ? 'active' : '' }}" href="{{ route('room-categories.index') }}">Kategori Ruang</a>
            @endif
        </nav>

        <div class="profile">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <span class="profile-greeting">Halo, {{ explode(' ', auth()->user()->name)[0] }}</span>
            <details>
                <summary aria-label="Menu profil">⌄</summary>
                <ul>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-full">Logout</button>
                        </form>
                    </li>
                </ul>
            </details>
        </div>
    @endauth
</header>

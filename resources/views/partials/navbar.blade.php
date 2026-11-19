<nav class="navbar">
    <a class="brand" href="{{ route('dashboard') }}">🏫 Pinjam-Ruang</a>

    @auth
        <ul class="nav-links">
            <li><a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a></li>
            <li><a class="{{ request()->routeIs('rooms.*') ? 'active' : '' }}" href="{{ route('rooms.index') }}">Ruang</a></li>
            <li><a class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}" href="{{ route('bookings.index') }}">Peminjaman</a></li>
            @if (auth()->user()->role === 'admin')
                <li><a class="{{ request()->routeIs('room-categories.*') ? 'active' : '' }}" href="{{ route('room-categories.index') }}">Kategori Ruang</a></li>
            @endif
        </ul>

        <div class="navbar-user">
            <span>{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn btn-secondary">Logout</button>
            </form>
        </div>
    @endauth
</nav>

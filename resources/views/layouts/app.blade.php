<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pinjam-Ruang')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @include('partials.navbar')

    <main class="container">
        @include('partials.alert')
        @yield('content')
    </main>

    <footer>
        &copy; {{ date('Y') }} Sistem Peminjaman Ruang Kampus
    </footer>
</body>
</html>

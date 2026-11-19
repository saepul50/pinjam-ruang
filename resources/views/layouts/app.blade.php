<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pinjam-Ruang')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@700;800&display=swap" rel="stylesheet">
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
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>

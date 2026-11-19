@extends('layouts.guest')

@section('title', 'Login - Pinjam-Ruang')

@section('content')
    <section class="card login-card">
        <a class="brand" href="{{ route('login') }}">
            <span class="brandmark">⌂</span>
            <span>pinjam</span><span>ruang.</span>
        </a>
        @include('partials.alert')

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input class="input" type="email" name="email" id="email" value="{{ old('email') }}" autofocus>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input class="input" type="password" name="password" id="password">
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>
            <label class="checkbox-row">
                <input type="checkbox" name="remember" value="1"> Ingat saya
            </label>
            <button type="submit" class="btn btn-primary btn-full">Masuk</button>
        </form>
    </section>
@endsection

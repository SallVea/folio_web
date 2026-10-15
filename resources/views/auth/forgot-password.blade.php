@extends('layouts.public')

@section('title', 'Lupa Password')

@section('content')

<div class="min-h-[100svh] flex items-center justify-center px-6 py-16">
    <div class="w-full" style="max-width:460px;" data-animate>
        <a href="{{ route('home') }}" class="wordmark" style="font-size:24px; display:block; margin-bottom:40px;">Folio.</a>

        <h1 class="wordmark" style="font-size:clamp(2.5rem, 5vw, 3.5rem); margin:0 0 12px;">Lupa password.</h1>
        <p style="color:var(--muted); margin:0 0 32px;">Masukkan email. Kami kirim link reset.</p>

        @if (session('success'))
            <p style="color:var(--ink); margin:0 0 24px;">{{ session('success') }}</p>
        @endif

        <form method="POST" action="{{ route('password.email') }}" style="display:grid; gap:24px;">
            @csrf
            <div style="display:grid; gap:8px;">
                <label for="email" style="font-size:17px;">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
                @error('email')
                    <p style="color:#991b1b; font-size:17px;">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="btn" style="justify-self:start;">Kirim link reset</button>
        </form>

        <p style="margin-top:32px; color:var(--muted);">
            Ingat password? <a href="{{ route('login') }}">Masuk</a>
        </p>
    </div>
</div>

@endsection
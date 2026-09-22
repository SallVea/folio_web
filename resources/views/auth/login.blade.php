@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
<div class="min-h-screen flex items-center justify-center px-6">
    <div class="w-full max-w-sm">
        <a href="{{ route('home') }}" class="font-display text-2xl font-700 block text-center mb-10">
            Folio<span class="text-violet">.</span>
        </a>

        <div class="bg-surface border border-border rounded-2xl p-7">
            <h1 class="font-display text-xl font-600 mb-1">Selamat datang kembali</h1>
            <p class="text-muted text-sm mb-7">Masuk untuk kelola portofolio kamu.</p>

            @if ($errors->any())
                <div class="mb-5 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-400 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs text-muted font-medium block mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs text-muted font-medium">Password</label>
                        <a href="{{ route('password.request') }}" class="text-xs text-violetb hover:underline">Lupa password?</a>
                    </div>
                    <input type="password" name="password" required
                           class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
                </div>
                <button type="submit"
                        class="w-full bg-violet hover:bg-violetb text-white py-2.5 rounded-lg font-medium transition mt-2">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-muted mt-6">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-violetb hover:underline">Daftar di sini</a>
        </p>
    </div>
</div>
@endsection

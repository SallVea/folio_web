@extends('layouts.guest')

@section('title', 'Daftar')

@section('content')
<div class="min-h-screen flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-sm">
        <a href="{{ route('home') }}" class="font-display text-2xl font-700 block text-center mb-10">
            Folio<span class="text-violet">.</span>
        </a>

        <div class="bg-surface border border-border rounded-2xl p-7">
            <h1 class="font-display text-xl font-600 mb-1">Buat akun Folio</h1>
            <p class="text-muted text-sm mb-7">Gratis, dan cuma butuh beberapa menit.</p>

            @if ($errors->any())
                <div class="mb-5 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-400 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.submit') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs text-muted font-medium block mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
                </div>
                <div>
                    <label class="text-xs text-muted font-medium block mb-1.5">Username</label>
                    <div class="flex items-center bg-bg border border-border rounded-lg px-4 focus-within:border-violet transition">
                        <span class="font-mono text-sm text-muted">folio.app/</span>
                        <input type="text" name="username" value="{{ old('username') }}" required
                               class="flex-1 bg-transparent py-2.5 text-sm focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="text-xs text-muted font-medium block mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
                </div>
                <div>
                    <label class="text-xs text-muted font-medium block mb-1.5">Password</label>
                    <input type="password" name="password" required
                           class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
                </div>
                <div>
                    <label class="text-xs text-muted font-medium block mb-1.5">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
                </div>
                <button type="submit"
                        class="w-full bg-violet hover:bg-violetb text-white py-2.5 rounded-lg font-medium transition mt-2">
                    Daftar
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-muted mt-6">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-violetb hover:underline">Masuk di sini</a>
        </p>
    </div>
</div>
@endsection

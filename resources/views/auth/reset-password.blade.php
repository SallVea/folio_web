@extends('layouts.guest')

@section('title', 'Buat Password Baru')

@section('content')
<div class="min-h-screen flex items-center justify-center px-6">
    <div class="w-full max-w-sm">
        <a href="{{ route('home') }}" class="font-display text-2xl font-700 block text-center mb-10">
            Folio<span class="text-violet">.</span>
        </a>

        <div class="bg-surface border border-border rounded-2xl p-7">
            <h1 class="font-display text-xl font-600 mb-1">Password Baru</h1>
            <p class="text-muted text-sm mb-7">Silakan buat password baru untuk akun Anda.</p>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="text-xs text-muted font-medium block mb-1.5">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ request()->email }}" required readonly
                        class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm text-muted focus:outline-none cursor-not-allowed">
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="text-xs text-muted font-medium block mb-1.5">Password Baru</label>
                    <input type="password" id="password" name="password" required autofocus
                        class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="text-xs text-muted font-medium block mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
                </div>

                <button type="submit"
                    class="w-full bg-violet hover:bg-violetb text-white py-2.5 rounded-lg font-medium transition mt-2">
                    Simpan Password Baru
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

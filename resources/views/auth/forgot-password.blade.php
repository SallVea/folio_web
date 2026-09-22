@extends('layouts.guest')

@section('title', 'Lupa Password')

@section('content')
<div class="min-h-screen flex items-center justify-center px-6">
    <div class="w-full max-w-sm">
        <a href="{{ route('home') }}" class="font-display text-2xl font-700 block text-center mb-10">
            Folio<span class="text-violet">.</span>
        </a>

        <div class="bg-surface border border-border rounded-2xl p-7">
            <h1 class="font-display text-xl font-600 mb-1">Lupa Password?</h1>
            <p class="text-muted text-sm mb-7">Masukkan email Anda dan kami akan mengirimkan link untuk mereset password Anda.</p>

            @if (session('success'))
                <div class="mb-5 px-4 py-3 rounded-lg bg-green-500/10 border border-green-500/30 text-green-400 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="text-xs text-muted font-medium block mb-1.5">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition"
                        placeholder="nama@email.com">
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-violet hover:bg-violetb text-white py-2.5 rounded-lg font-medium transition mt-2">
                    Kirim Link Reset
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-muted mt-6">
            Ingat password Anda? 
            <a href="{{ route('login') }}" class="text-violetb hover:underline">Masuk di sini</a>
        </p>
    </div>
</div>
@endsection

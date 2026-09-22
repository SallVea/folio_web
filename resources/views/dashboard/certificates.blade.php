@extends('layouts.dashboard')

@section('title', 'Sertifikat')

@section('content')
<div x-data="{ showCreate: false }">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display text-2xl font-600">Sertifikat</h1>
            <p class="text-muted text-sm mt-1">{{ $certificates->count() }} sertifikat ditambahkan</p>
        </div>
        <button @click="showCreate = !showCreate"
                class="bg-violet hover:bg-violetb text-white px-5 py-2.5 rounded-lg text-sm font-medium transition">
            <span x-text="showCreate ? '✕ Tutup' : '+ Tambah Sertifikat'"></span>
        </button>
    </div>

    <div x-show="showCreate" x-cloak x-transition class="bg-surface border border-border rounded-xl p-6 mb-8">
        <form method="POST" action="{{ route('certificates.store') }}" class="space-y-4">
            @csrf
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-muted font-medium block mb-1.5">Judul Sertifikat *</label>
                    <input type="text" name="title" required
                           class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
                </div>
                <div>
                    <label class="text-xs text-muted font-medium block mb-1.5">Penerbit *</label>
                    <input type="text" name="issuer" required
                           class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-muted font-medium block mb-1.5">Tanggal Terbit</label>
                    <input type="date" name="issued_date"
                           class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
                </div>
                <div>
                    <label class="text-xs text-muted font-medium block mb-1.5">Tanggal Kadaluarsa</label>
                    <input type="date" name="expiry_date"
                           class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
                </div>
            </div>
            <div>
                <label class="text-xs text-muted font-medium block mb-1.5">URL Kredensial</label>
                <input type="url" name="credential_url"
                       class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
            </div>
            <button type="submit" class="bg-violet hover:bg-violetb text-white px-5 py-2.5 rounded-lg text-sm font-medium transition">
                Simpan Sertifikat
            </button>
        </form>
    </div>

    @if ($certificates->isEmpty())
        <div class="bg-surface border border-border rounded-xl p-14 text-center">
            <p class="text-muted text-sm">Belum ada sertifikat.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($certificates as $c)
                <div class="bg-surface border border-border rounded-xl p-5 flex items-center justify-between">
                    <div>
                        <p class="font-medium text-sm">{{ $c->title }}</p>
                        <p class="font-mono text-xs text-violetb mt-0.5">{{ $c->issuer }}</p>
                        @if ($c->issued_date)
                            <p class="text-xs text-muted mt-1">Diterbitkan {{ \Carbon\Carbon::parse($c->issued_date)->translatedFormat('d M Y') }}</p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('certificates.destroy', $c->id) }}" onsubmit="return confirm('Hapus sertifikat ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-muted hover:text-red-400 transition">Hapus</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

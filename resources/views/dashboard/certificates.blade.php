@extends('layouts.dashboard')

@section('title', 'Sertifikat')

@section('content')
<div x-data="{ showCreate: false }">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display text-2xl font-600">Sertifikat</h1>
            <p class="text-muted text-sm mt-1">{{ $certificates->count() }} sertifikat ditambahkan</p>
        </div>
        <button @click="showCreate = !showCreate" class="bg-violet hover:bg-violetb text-white px-5 py-2.5 text-sm font-medium transition">
            <span x-text="showCreate ? 'Tutup' : 'Tambah Sertifikat'"></span>
        </button>
    </div>

    <div x-show="showCreate" x-cloak x-transition class="bg-surface p-6 mb-8">
        <form method="POST" action="{{ route('certificates.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-muted font-medium block mb-1.5">Judul Sertifikat *</label>
                    <input type="text" name="title" required class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
                </div>
                <div>
                    <label class="text-xs text-muted font-medium block mb-1.5">Penerbit *</label>
                    <input type="text" name="issuer" required class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-muted font-medium block mb-1.5">Tanggal Terbit</label>
                    <input type="date" name="issued_date" class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
                </div>
                <div>
                    <label class="text-xs text-muted font-medium block mb-1.5">Tanggal Kadaluarsa</label>
                    <input type="date" name="expiry_date" class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
                </div>
            </div>
            <div>
                <label class="text-xs text-muted font-medium block mb-1.5">URL Kredensial</label>
                <input type="url" name="credential_url" class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
            </div>
            <div>
                <label class="text-xs text-muted font-medium block mb-1.5">Upload Bukti (gambar)</label>
                <input type="file" name="proof" accept="image/*,.pdf" class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
            </div>
            <button type="submit" class="bg-violet hover:bg-violetb text-white px-5 py-2.5 text-sm font-medium transition">
                Simpan Sertifikat
            </button>
        </form>
    </div>

    @if ($certificates->isEmpty())
        <div class="bg-surface p-14 text-center">
            <p class="text-muted text-sm">Belum ada sertifikat.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($certificates as $c)
                @php
                    $expiry = $c->expiry_date;
                    $reminder = null;
                    if ($expiry) {
                        $days = (int) now()->startOfDay()->diffInDays($expiry->startOfDay(), false);
                        if ($days < 0) {
                            $reminder = 'Kadaluarsa';
                        } elseif ($days <= 30) {
                            $reminder = 'Segera kadaluarsa ('.$days.' hari)';
                        }
                    }
                    $status = $c->credential_status ?: 'unknown';
                @endphp

                <div x-data="{ editing: false }" class="bg-surface p-5">
                    <!-- View -->
                    <div x-show="!editing">
                        <div class="flex items-start justify-between gap-4 flex-wrap">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-sm">{{ $c->title }}</p>
                                <p class="text-xs text-muted mt-0.5">{{ $c->issuer }}</p>
                                @if ($c->issued_date)
                                    <p class="text-xs text-muted mt-1">Diterbitkan {{ \Carbon\Carbon::parse($c->issued_date)->translatedFormat('d M Y') }}</p>
                                @endif
                                @if ($reminder)
                                    <p class="text-xs mt-1 {{ $reminder === 'Kadaluarsa' ? 'text-red-700' : 'text-[#232323]' }}">{{ $reminder }}</p>
                                @endif
                                <p class="text-xs text-muted mt-1">
                                    Status kredensial:
                                    <span class="{{ $status === 'valid' ? 'text-[#232323]' : ($status === 'invalid' ? 'text-red-700' : 'text-muted') }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </p>
                            </div>

                            @if ($c->image)
                                @if (str_ends_with(strtolower($c->image), '.pdf'))
                                    <a href="{{ \Storage::disk('public')->url($c->image) }}" target="_blank" class="w-20 h-20 bg-bg flex items-center justify-center text-xs">PDF</a>
                                @else
                                    <a href="{{ \Storage::disk('public')->url($c->image) }}" target="_blank" class="w-20 h-20 bg-bg overflow-hidden inline-block">
                                        <img src="{{ \Storage::disk('public')->url($c->image) }}" class="w-full h-full object-cover" alt="Bukti">
                                    </a>
                                @endif
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-3 mt-4">
                            <button type="button" @click="editing = true" class="text-xs text-muted hover:text-[#232323] transition">Edit</button>

                            @if ($c->credential_url)
                                <form method="POST" action="{{ route('certificates.verify', $c->id) }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-muted hover:text-[#232323] transition">Verifikasi Ulang</button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('certificates.proof', $c->id) }}" enctype="multipart/form-data" class="flex items-center gap-2">
                                @csrf
                                <input type="file" name="proof" accept="image/*,.pdf" class="text-xs text-muted">
                                <button type="submit" class="text-xs text-muted hover:text-[#232323] transition">Upload Bukti</button>
                            </form>

                            <form method="POST" action="{{ route('certificates.destroy', $c->id) }}" onsubmit="return confirm('Hapus sertifikat ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-muted hover:text-red-700 transition">Hapus</button>
                            </form>
                        </div>
                    </div>

                    <!-- Edit -->
                    <div x-show="editing" x-cloak>
                        <form method="POST" action="{{ route('certificates.update', $c->id) }}" class="space-y-4">
                            @csrf @method('PUT')
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs text-muted font-medium block mb-1.5">Judul *</label>
                                    <input type="text" name="title" value="{{ $c->title }}" required class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-xs text-muted font-medium block mb-1.5">Penerbit *</label>
                                    <input type="text" name="issuer" value="{{ $c->issuer }}" required class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs text-muted font-medium block mb-1.5">Tanggal Terbit</label>
                                    <input type="date" name="issued_date" value="{{ $c->issued_date?->format('Y-m-d') }}" class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-xs text-muted font-medium block mb-1.5">Tanggal Kadaluarsa</label>
                                    <input type="date" name="expiry_date" value="{{ $c->expiry_date?->format('Y-m-d') }}" class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="text-xs text-muted font-medium block mb-1.5">URL Kredensial</label>
                                <input type="url" name="credential_url" value="{{ $c->credential_url }}" class="w-full bg-bg px-4 py-2.5 text-sm focus:outline-none">
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <button type="submit" class="bg-violet hover:bg-violetb text-white px-5 py-2.5 text-sm font-medium transition">Simpan Perubahan</button>
                                <button type="button" @click="editing = false" class="text-sm text-muted hover:text-[#232323] px-5 py-2.5 transition">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
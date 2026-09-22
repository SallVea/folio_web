@extends('layouts.dashboard')

@section('title', 'Overview')

@section('content')
<div class="mb-8">
    <h1 class="font-display text-2xl font-600">Halo, {{ auth()->user()->name }} 👋</h1>
    <p class="text-muted text-sm mt-1">Begini kondisi portofolio kamu hari ini.</p>
</div>

<!-- ─── Stats ────────────────────────────────────── -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-10">
    @php
        $stats = [
            ['label' => 'Portfolio',  'value' => $stats['portfolios']],
            ['label' => 'Skills',     'value' => $stats['skills']],
            ['label' => 'Sertifikat', 'value' => $stats['certificates']],
            ['label' => 'Views',      'value' => $stats['views']],
        ];
    @endphp
    @foreach ($stats as $s)
        <div class="bg-surface border border-border rounded-xl p-5">
            <p class="font-display text-3xl font-700 text-violetb">{{ $s['value'] }}</p>
            <p class="text-xs text-muted mt-1">{{ $s['label'] }}</p>
        </div>
    @endforeach
</div>

<!-- ─── Link Publik ──────────────────────────────── -->
<div class="bg-surface border border-border rounded-xl p-6 mb-10 flex items-center justify-between flex-wrap gap-4">
    <div>
        <p class="text-xs text-muted mb-1">Link Portofolio Publik</p>
        <p class="font-mono text-violetb">folio.app/{{ auth()->user()->username }}</p>
    </div>
    <a href="{{ route('public.show', auth()->user()->username) }}" target="_blank"
       class="bg-violet hover:bg-violetb text-white px-5 py-2.5 rounded-lg text-sm font-medium transition">
        Lihat Halaman →
    </a>
</div>

<!-- ─── Portfolio Terbaru ────────────────────────── -->
<div class="flex items-center justify-between mb-4">
    <h2 class="font-display text-lg font-600">Portfolio Terbaru</h2>
    <a href="{{ route('portfolios.index') }}" class="text-sm text-violetb hover:underline">Lihat semua →</a>
</div>

@if ($recentPortfolios->isEmpty())
    <div class="bg-surface border border-border rounded-xl p-10 text-center">
        <p class="text-muted text-sm">Belum ada portfolio. <a href="{{ route('portfolios.index') }}" class="text-violetb hover:underline">Tambahkan sekarang</a></p>
    </div>
@else
    <div class="grid md:grid-cols-3 gap-4">
        @foreach ($recentPortfolios as $p)
            <div class="bg-surface border border-border rounded-xl overflow-hidden">
                <div class="h-28 bg-surface2">
                    @if ($p->thumbnail)
                        <img src="{{ \Storage::disk('public')->url($p->thumbnail) }}" class="w-full h-full object-cover" alt="">
                    @endif
                </div>
                <div class="p-4">
                    <p class="font-medium text-sm">{{ $p->title }}</p>
                    @if ($p->category)
                        <p class="font-mono text-xs text-violetb mt-1">{{ $p->category }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection

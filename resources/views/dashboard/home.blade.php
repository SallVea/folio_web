@extends('layouts.dashboard')

@section('title', 'Overview')

@section('content')
<div class="mb-8">
    <h1 class="font-display text-2xl font-600">Halo, {{ auth()->user()->name }} </h1>
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

@php $publicUrl = route('public.show', auth()->user()->username); @endphp

<!-- ─── Kelengkapan & Aksi Cepat ─────────────────── -->
<div class="bg-surface p-6 mb-10">
    <div class="flex items-center justify-between flex-wrap gap-4 mb-4">
        <div>
            <p class="text-xs text-muted mb-1">Kelengkapan Profil</p>
            <h2 class="font-display text-2xl font-600">{{ $completion }}%</h2>
        </div>
        <div class="text-sm text-muted">
            {{ array_sum($profileChecks) }} dari {{ count($profileChecks) }} siap
        </div>
    </div>
    <div style="height:6px; background:#e9e9e9;" class="mb-5">
        <div style="height:6px; width:{{ $completion }}%; background:#232323;"></div>
    </div>
    <div class="grid md:grid-cols-2 gap-2 text-sm">
        @foreach ($profileChecks as $label => $ok)
            <p class="{{ $ok ? 'text-[#232323]' : 'text-muted' }}">
                {{ $ok ? '✓' : '•' }} {{ $label }}
            </p>
        @endforeach
    </div>
    <div class="flex flex-wrap gap-3 mt-6">
        <button type="button" onclick="copyPublicLink()" class="bg-surface2 px-5 py-2.5 text-sm font-medium transition">
            Salin Link
        </button>
        <a href="{{ $publicUrl }}" target="_blank" title="Membuka preview portofolio publik di tab baru"
           class="bg-violet hover:bg-violetb text-white px-5 py-2.5 text-sm font-medium transition">
            Preview
        </a>
        <a href="{{ route('portfolios.index') }}" class="px-5 py-2.5 text-sm text-muted hover:text-[#232323] transition">
            Tambah Portfolio
        </a>
    </div>
</div>

<!-- ─── Link Publik ──────────────────────────────── -->
<div class="bg-surface border border-border rounded-xl p-6 mb-10 flex items-center justify-between flex-wrap gap-4">
    <div>
        <p class="text-xs text-muted mb-1">Link Portofolio Publik</p>
        <p class="font-body text-[#232323]" style="font-size:18px;">folio.app/{{ auth()->user()->username }}</p>
        <p class="text-xs text-muted mt-2">Bagikan link ini ke siapa saja untuk melihat portofolio Anda.</p>
    </div>
    <div class="flex flex-col items-end gap-2">
        <a href="{{ route('public.show', auth()->user()->username) }}" target="_blank"
           title="Membuka preview portofolio publik di tab baru"
           class="bg-violet hover:bg-violetb text-white px-5 py-2.5 rounded-lg text-sm font-medium transition">
            Lihat Halaman
        </a>
        <p class="text-xs text-muted text-right max-w-[180px]">
            Tombol ini membuka preview halaman publik terbaru di tab baru.
        </p>
    </div>
</div>

@if ($certificateReminders->isNotEmpty())
    <div class="bg-surface p-6 mb-10">
        <h2 class="font-display text-2xl font-600 mb-4">Reminder Sertifikat</h2>
        <div class="space-y-2">
            @foreach ($certificateReminders as $cert)
                @php $days = (int) now()->startOfDay()->diffInDays($cert->expiry_date->startOfDay(), false); @endphp
                <p class="{{ $days < 0 ? 'text-red-700' : 'text-[#232323]' }}">
                    {{ $cert->title }} — {{ $days < 0 ? 'sudah kadaluarsa' : 'kadaluarsa dalam '.$days.' hari' }}
                </p>
            @endforeach
        </div>
    </div>
@endif

<!-- ─── Analytics Charts ─────────────────────────── -->
<div class="grid md:grid-cols-2 gap-4 mb-10">
    <div class="bg-surface border border-border rounded-xl p-6">
        <p class="text-sm font-medium mb-3">Views (7 hari)</p>
        <canvas id="viewsChart" height="180"></canvas>
    </div>
    <div class="bg-surface border border-border rounded-xl p-6">
        <p class="text-sm font-medium mb-3">Portfolio per Kategori</p>
        <canvas id="categoryChart" height="180"></canvas>
    </div>
    <div class="bg-surface border border-border rounded-xl p-6">
        <p class="text-sm font-medium mb-3">Views (30 hari)</p>
        <canvas id="viewsChart30" height="180"></canvas>
    </div>
    <div class="bg-surface border border-border rounded-xl p-6">
        <p class="text-sm font-medium mb-3">Sumber Trafik Teratas</p>
        @forelse ($topReferrers as $ref => $total)
            <p class="text-sm py-1 border-b border-border truncate">{{ $ref }} — {{ $total }}</p>
        @empty
            <p class="text-sm text-muted">Belum ada data referrer.</p>
        @endforelse
    </div>
</div>
<div class="bg-surface border border-border rounded-xl p-6 mb-10">
    <p class="text-sm font-medium mb-3">Skills per Level</p>
    <canvas id="skillsChart" height="140"></canvas>
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
                        <p class="font-body text-xs text-violetb mt-1">{{ $p->category }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    const viewLabels = @json($viewTrend->pluck('date'));
    const viewData = @json($viewTrend->pluck('count'));
    const categoryLabels = @json($portfolioByCategory->keys());
    const categoryData = @json($portfolioByCategory->values());
    const skillLabels = @json($skillsByLevel->keys());
    const skillData = @json($skillsByLevel->values());

    if (window.Chart) {
        new Chart(document.getElementById('viewsChart'), {
            type: 'line',
            data: { labels: viewLabels, datasets: [{ label: 'Views', data: viewData, borderColor: '#232323', backgroundColor: 'rgba(35,35,35,.1)', fill: true, tension: .35 }] },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
        new Chart(document.getElementById('viewsChart30'), {
            type: 'line',
            data: { labels: @json($viewTrend30->pluck('date')), datasets: [{ label: 'Views', data: @json($viewTrend30->pluck('count')), borderColor: '#7c3aed', backgroundColor: 'rgba(124,58,237,.1)', fill: true, tension: .35 }] },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: { labels: categoryLabels, datasets: [{ data: categoryData, backgroundColor: ['#232323','#a1ffcb','#6e6e6e','#f3f3f3','#d4d4d4'] }] },
            options: { responsive: true }
        });
        new Chart(document.getElementById('skillsChart'), {
            type: 'bar',
            data: { labels: skillLabels, datasets: [{ label: 'Jumlah', data: skillData, backgroundColor: '#232323' }] },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    }

    function copyPublicLink() {
        const url = @json($publicUrl);
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(function () {
                alert('Link disalin: ' + url);
            }).catch(function () {
                window.prompt('Salin link ini:', url);
            });
        } else {
            window.prompt('Salin link ini:', url);
        }
    }
</script>
@endsection

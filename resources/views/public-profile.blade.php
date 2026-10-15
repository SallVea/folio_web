@extends('layouts.public')

@section('title', $user->name.' ('.$user->username.') — Folio')

@push('meta')
@php
    $profileUrl = route('public.show', $user->username);
    $ogImage = $user->profile_photo ? url(\Storage::disk('public')->url($user->profile_photo)) : ($user->portfolios->first()?->thumbnail ? url(\Storage::disk('public')->url($user->portfolios->first()->thumbnail)) : null);
@endphp
@php
    $ringkas = request('mode') === 'ringkas';
    $orderList = $user->section_order ?? [];
    foreach (['portfolio','skills','certificates'] as $sec) { if (! in_array($sec, $orderList)) { $orderList[] = $sec; } }
    $secPos = array_flip(array_values($orderList));
@endphp
<meta name="description" content="{{ $user->bio ?: $user->job_title ?: $user->name.' — portfolio' }}">
<link rel="canonical" href="{{ $profileUrl }}">
<meta property="og:type" content="profile">
<meta property="og:title" content="{{ $user->name.' — '.$user->job_title }}">
<meta property="og:description" content="{{ $user->bio ?: $user->job_title ?: 'Portfolio' }}">
<meta property="og:url" content="{{ $profileUrl }}">
@if ($ogImage)
<meta property="og:image" content="{{ $ogImage }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $user->name }}">
<meta name="twitter:description" content="{{ $user->bio ?: $user->job_title }}">
@endpush

@section('content')

<div class="max-w-4xl mx-auto px-6 py-14 md:py-20" style="display:flex; flex-direction:column;">

    <!-- ─── Header ─────────────────────────────── -->
    <header class="text-center mb-20" data-animate>
        <div class="w-28 h-28 mx-auto mb-8 overflow-hidden surface flex items-center justify-center">
            @if ($user->profile_photo)
                <img src="{{ \Storage::disk('public')->url($user->profile_photo) }}" class="w-full h-full object-cover" alt="{{ $user->name }}">
            @else
                <span class="wordmark" style="font-size:48px;">{{ substr($user->name, 0, 1) }}</span>
            @endif
        </div>

        <h1 class="wordmark" style="font-size:clamp(2.5rem, 6vw, 4.5rem); line-height:1.05;">{{ $user->name }}</h1>
        <p class="mt-4" style="font-size:20px; color:var(--ink);">folio.app/{{ $user->username }}</p>

        @if ($user->job_title)
            <p class="mt-6" style="font-size:22px; max-width:42ch; margin-left:auto; margin-right:auto;">{{ $user->job_title }}</p>
        @endif
        @if ($user->location)
            <p class="mt-2" style="color:var(--muted);">{{ $user->location }}</p>
        @endif
        @if ($user->bio)
            <p class="mt-6" style="color:var(--ink); max-width:58ch; margin-left:auto; margin-right:auto;">{{ $user->bio }}</p>
        @endif

        @if ($user->socialLinks->isNotEmpty())
            <div class="flex flex-wrap justify-center gap-6 mt-8" style="font-size:17px;">
                @foreach ($user->socialLinks as $link)
                    <a href="{{ $link->url }}" target="_blank" rel="noopener">{{ $link->platform }}</a>
                @endforeach
            </div>
        @endif

        <div class="flex flex-wrap justify-center items-center gap-6 mt-8" style="font-size:16px;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($profileUrl) }}" alt="QR profil {{ $user->username }}" width="120" height="120">
            <div class="flex flex-col gap-3">
                <a href="{{ route('public.vcard', $user->username) }}" class="btn" style="font-size:16px;">Unduh vCard</a>
                <a href="{{ $profileUrl }}" target="_blank" rel="noopener">Bagikan profil</a>
            </div>
        </div>
    </header>

    <!-- ─── Portfolio ──────────────────────────── -->
    @if ($user->show_portfolio && $user->portfolios->isNotEmpty())
        <section class="mb-20" data-animate style="order:{{ ($secPos['portfolio'] ?? 0) + 1 }};">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
                <h2 class="wordmark" style="font-size:clamp(1.75rem, 4vw, 2.5rem); margin:0;">Portfolio</h2>
                <input id="portfolio-search" type="search" placeholder="Cari portfolio, kategori, atau tech..." style="max-width:360px;">
                <div class="flex gap-4" style="font-size:15px;">
                    <a href="?mode=detail" style="text-decoration:{{ $ringkas ? 'none' : 'underline' }};">Detail</a>
                    <a href="?mode=ringkas" style="text-decoration:{{ $ringkas ? 'underline' : 'none' }};">Ringkas</a>
                </div>
            </div>
            @foreach ($user->portfolios->groupBy(fn ($p) => $p->category ?: 'Lainnya') as $group => $items)
            <div class="portfolio-group mb-10">
                <h3 style="font-family:var(--display); font-size:20px; margin:0 0 16px;">{{ $group }}</h3>
                <div class="grid md:grid-cols-2 gap-8">
                    @foreach ($items as $p)
                        <article data-animate data-search="{{ strtolower($p->title.' '.($p->category ?? '').' '.implode(' ', $p->tech_stack ?? [])) }}">
                            <div class="surface overflow-hidden mb-4">
                                @if ($p->thumbnail)
                                    <img src="{{ \Storage::disk('public')->url($p->thumbnail) }}" class="drift w-full" style="aspect-ratio:3/2; object-fit:cover;" alt="{{ $p->title }}">
                                @else
                                    <div style="aspect-ratio:3/2;" class="surface"></div>
                                @endif
                            </div>
                            <h3 style="font-size:22px; font-family:var(--display); font-weight:700; margin:0 0 6px;">{{ $p->title }}</h3>
                            @if ($p->is_featured)
                                <p style="color:var(--muted); font-size:17px; margin:0 0 8px;">Unggulan</p>
                            @endif
                            @unless ($ringkas)
                                @if ($p->description)
                                    <p style="color:var(--ink); margin:0 0 8px;">{{ $p->description }}</p>
                                @endif
                            @endunless
                            @if ($p->tech_stack)
                                <p style="color:var(--muted);">{{ implode(', ', $p->tech_stack) }}</p>
                            @endif
                            @if ($p->project_url || $p->github_url)
                                <div class="flex gap-6 mt-4">
                                    @if ($p->project_url)
                                        <a href="{{ $p->project_url }}" target="_blank" rel="noopener">Demo</a>
                                    @endif
                                    @if ($p->github_url)
                                        <a href="{{ $p->github_url }}" target="_blank" rel="noopener">GitHub</a>
                                    @endif
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
            @endforeach
            <p id="portfolio-empty" style="color:var(--muted); display:none;">Tidak ada portfolio yang cocok.</p>
        </section>
    @endif

    <!-- ─── Skills ─────────────────────────────── -->
    @if ($user->show_skills && $user->skills->isNotEmpty())
        @php $levelPercent = ['Beginner'=>20,'Elementary'=>40,'Intermediate'=>60,'Advanced'=>80,'Expert'=>100]; @endphp
        <section class="mb-20" data-animate style="order:{{ ($secPos['skills'] ?? 0) + 1 }};">
            <h2 class="wordmark" style="font-size:clamp(1.75rem, 4vw, 2.5rem); margin:0 0 24px;">Skills</h2>
            <div class="space-y-6">
                @foreach ($user->skills as $s)
                    <div data-animate>
                        <div class="flex justify-between mb-2" style="font-size:19px;">
                            <span>{{ $s->name }}</span>
                            <span style="color:var(--muted);">{{ $s->level }}</span>
                        </div>
                        <div style="height:6px; background:var(--surface);">
                            <div style="height:6px; width:{{ $levelPercent[$s->level] ?? 60 }}%; background:var(--ink);"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- ─── Certificates ───────────────────────── -->
    @if ($user->show_certificates && $user->certificates->isNotEmpty())
        <section class="mb-20" data-animate style="order:{{ ($secPos['certificates'] ?? 0) + 1 }};">
            <h2 class="wordmark" style="font-size:clamp(1.75rem, 4vw, 2.5rem); margin:0 0 24px;">Sertifikat</h2>
            <div class="space-y-4">
                @foreach ($user->certificates as $c)
                    <div data-animate class="surface px-6 py-5 flex items-center justify-between gap-4 flex-wrap">
                        <div class="flex items-center gap-4">
                            @if ($c->image)
                                @if (str_ends_with(strtolower($c->image), '.pdf'))
                                    <a href="{{ \Storage::disk('public')->url($c->image) }}" target="_blank" rel="noopener" style="width:64px;height:64px;display:flex;align-items:center;justify-content:center;background:var(--surface);font-size:12px;flex:none;">PDF</a>
                                @else
                                    <a href="{{ \Storage::disk('public')->url($c->image) }}" target="_blank" rel="noopener" style="width:64px;height:64px;overflow:hidden;display:block;flex:none;">
                                        <img src="{{ \Storage::disk('public')->url($c->image) }}" alt="Bukti {{ $c->title }}" style="width:100%;height:100%;object-fit:cover;">
                                    </a>
                                @endif
                            @endif
                            <div>
                                <p style="font-size:20px; font-family:var(--display); font-weight:700; margin:0 0 4px;">{{ $c->title }}</p>
                                <p style="color:var(--muted); margin:0;">{{ $c->issuer }}</p>
                                @if ($c->credential_status === 'valid')
                                    <p style="color:var(--muted); margin:4px 0 0; font-size:14px;">Terverifikasi</p>
                                @elseif ($c->credential_url)
                                    <p style="color:var(--muted); margin:4px 0 0; font-size:14px;">Belum terverifikasi</p>
                                @endif
                            </div>
                        </div>
                        @if ($c->credential_url)
                            <a href="{{ $c->credential_url }}" target="_blank" rel="noopener">Verifikasi</a>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- ─── Atmosphere ──────────────────────────── -->
    <section class="mb-20" data-animate style="order:10;">
        <h2 class="wordmark" style="font-size:clamp(1.75rem, 4vw, 2.5rem); margin:0 0 24px;">Suasana.</h2>
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            @foreach ([63,39,23,486] as $pid)
                <img src="https://picsum.photos/id/{{ $pid }}/720/480"
                     alt="Still life photograph from an open collection, used as atmosphere."
                     width="720" height="480" loading="lazy" class="drift w-full"
                     style="aspect-ratio:3/2; object-fit:cover;" data-animate>
            @endforeach
        </div>
        <div style="aspect-ratio:16/9; overflow:hidden;">
            <video autoplay muted loop playsinline preload="metadata"
                   poster="assets/hunt-and-peck-typing-monkeytype-benchmark-poster.jpg"
                   width="854" height="480" aria-hidden="true"
                   style="width:100%; height:100%; object-fit:cover;">
                <source src="assets/hunt-and-peck-typing-monkeytype-benchmark.webm" type="video/webm">
            </video>
        </div>
    </section>

    <footer class="pt-8" style="order:11; color:var(--muted); font-size:17px; text-align:center;">
        <p style="margin:0 0 16px;">
            Photographs: Lorem Picsum (Unsplash licence). Video: “Hunt and peck typing — Monkeytype benchmark” by C40115, CC BY 4.0, via Wikimedia Commons.
        </p>
        <a href="{{ route('home') }}">Folio</a>
    </footer>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('portfolio-search');
    if (!input) return;
    input.addEventListener('input', () => {
        const q = input.value.trim().toLowerCase();
        let visible = 0;
        document.querySelectorAll('[data-search]').forEach(el => {
            const hay = el.getAttribute('data-search') || '';
            const match = hay.includes(q);
            el.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        document.querySelectorAll('.portfolio-group').forEach(group => {
            const anyVisible = Array.from(group.querySelectorAll('[data-search]')).some(el => el.style.display !== 'none');
            group.style.display = anyVisible ? '' : 'none';
        });
        const empty = document.getElementById('portfolio-empty');
        if (empty) empty.style.display = visible === 0 ? '' : 'none';
    });
});
</script>

@endsection
@extends('layouts.guest')

@section('title', 'Folio')

@section('content')

<!-- ─── Nav ──────────────────────────────────────── -->
<header class="max-w-6xl mx-auto px-6 md:px-10 py-6 flex items-center justify-between">
    <span class="font-display text-xl font-700">Folio<span class="text-violet">.</span></span>
    <nav class="flex items-center gap-3 text-sm">
        <a href="{{ route('login') }}" class="text-muted hover:text-white transition px-3 py-2">Masuk</a>
        <a href="{{ route('register') }}" class="bg-violet hover:bg-violetb text-white px-4 py-2 rounded-lg font-medium transition">
            Daftar
        </a>
    </nav>
</header>

<!-- ─── Hero ─────────────────────────────────────── -->
<section class="max-w-6xl mx-auto px-6 md:px-10 pt-12 md:pt-20 pb-24 grid md:grid-cols-2 gap-12 items-center">
    <div>
        <p class="font-mono text-sm text-violetb mb-5">→ folio.app/<span class="text-white">username</span></p>
        <h1 class="font-display text-4xl md:text-[3.4rem] leading-[1.08] font-700 mb-6">
            Portofolio yang nggak perlu<br class="hidden md:block">
            di-update di <span class="text-violet">lima tempat</span> berbeda.
        </h1>
        <p class="text-muted text-lg leading-relaxed mb-9 max-w-md">
            Tambah karya dari HP, link-nya langsung update. Satu URL buat semua —
            dosen, HRD, atau siapapun yang nanya "portofolio kamu mana?"
        </p>
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('register') }}"
               class="bg-violet hover:bg-violetb text-white px-6 py-3.5 rounded-lg font-medium transition">
                Buat Portofolio — Gratis
            </a>
            <a href="#modules" class="text-sm text-muted hover:text-white transition font-mono">
                lihat isinya ↓
            </a>
        </div>
    </div>

    <!-- Signature visual: browser mockup showing the actual product -->
    <div class="relative">
        <div class="bracket-hover rounded-2xl bg-surface border border-border shadow-2xl shadow-violet/5 rotate-2 hover:rotate-0 transition-transform duration-500">
            <div class="flex items-center gap-2 px-4 py-3 border-b border-border">
                <div class="flex gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#FF5F57]"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-[#FEBC2E]"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-[#28C840]"></span>
                </div>
                <div class="flex-1 text-center">
                    <span class="font-mono text-xs text-muted bg-bg px-3 py-1 rounded-md">folio.app/sallvea</span>
                </div>
            </div>
            <div class="p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-12 h-12 rounded-full bg-violet flex items-center justify-center font-display font-700 text-white">S</div>
                    <div>
                        <p class="font-medium">Daffa Atsaal Mishbah</p>
                        <p class="text-xs text-muted">Cybersecurity Engineering Student</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-surface2 rounded-lg p-3 border border-border">
                        <div class="h-12 rounded bg-gradient-to-br from-violet/30 to-transparent mb-2"></div>
                        <p class="text-xs font-medium">SIMRS Security Audit</p>
                        <p class="font-mono text-[10px] text-violetb mt-1">› NestJS · PostgreSQL</p>
                    </div>
                    <div class="bg-surface2 rounded-lg p-3 border border-border">
                        <div class="h-12 rounded bg-gradient-to-br from-violet/30 to-transparent mb-2"></div>
                        <p class="text-xs font-medium">Folio Mobile App</p>
                        <p class="font-mono text-[10px] text-violetb mt-1">› Kotlin · Compose</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <span class="font-mono text-[10px] bg-bg border border-border px-2 py-1 rounded text-muted">Laravel</span>
                    <span class="font-mono text-[10px] bg-bg border border-border px-2 py-1 rounded text-muted">PostgreSQL</span>
                    <span class="font-mono text-[10px] bg-bg border border-border px-2 py-1 rounded text-muted">Docker</span>
                </div>
            </div>
        </div>
        <!-- Ambient glow -->
        <div class="absolute -inset-10 bg-violet/10 blur-3xl rounded-full -z-10"></div>
    </div>
</section>

<!-- ─── Modules (apa yang ada di dalam) ─────────────── -->
<section id="modules" class="max-w-6xl mx-auto px-6 md:px-10 py-20 border-t border-border">
    <p class="font-mono text-sm text-violetb mb-3">isi folio kamu</p>
    <h2 class="font-display text-2xl md:text-3xl font-600 mb-12 max-w-lg">
        Empat hal yang biasanya dicari orang lain dari kamu, jadi satu.
    </h2>

    <div class="grid md:grid-cols-4 gap-px bg-border rounded-2xl overflow-hidden border border-border">
        @php
            $modules = [
                ['label' => 'Portfolio', 'desc' => 'Project, link demo, repo GitHub — lengkap dengan galeri foto.'],
                ['label' => 'Skills', 'desc' => 'Level kemampuan kamu, dari Beginner sampai Expert.'],
                ['label' => 'Sertifikat', 'desc' => 'Bukti sertifikasi dengan link verifikasi.'],
                ['label' => 'Sosial', 'desc' => 'GitHub, LinkedIn, dan akun lain dalam satu tempat.'],
            ];
        @endphp
        @foreach ($modules as $m)
            <div class="bg-bg p-6 hover:bg-surface transition-colors">
                <p class="font-display font-600 mb-2">{{ $m['label'] }}</p>
                <p class="text-sm text-muted leading-relaxed">{{ $m['desc'] }}</p>
            </div>
        @endforeach
    </div>
</section>

<!-- ─── CTA ──────────────────────────────────────── -->
<section class="max-w-6xl mx-auto px-6 md:px-10 py-24 text-center border-t border-border">
    <h2 class="font-display text-3xl md:text-4xl font-700 mb-5">
        Link-nya tinggal di-<span class="text-violet">share</span>.
    </h2>
    <p class="text-muted mb-9 max-w-md mx-auto">Bikin akun, isi sekali, dan biarkan link-nya bekerja sendiri.</p>
    <a href="{{ route('register') }}"
       class="inline-block bg-violet hover:bg-violetb text-white px-8 py-4 rounded-lg font-medium transition">
        Mulai Sekarang
    </a>
</section>

<footer class="max-w-6xl mx-auto px-6 md:px-10 py-8 border-t border-border flex items-center justify-between text-sm text-muted">
    <span class="font-display">Folio<span class="text-violet">.</span></span>
    <span class="font-mono text-xs">built by Sall</span>
</footer>

@endsection

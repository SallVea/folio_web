<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name }} (@{{ $user->username }}) — Folio</title>
    <meta name="description" content="{{ $user->bio ?? $user->name.' — Portfolio di Folio' }}">
    @if ($user->profile_photo)
        <meta property="og:image" content="{{ \Storage::disk('public')->url($user->profile_photo) }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bg: '#0B0B0F', surface: '#14141B', surface2: '#1C1C26',
                        border: '#26262F', violet: '#7C3AED', violetb: '#A78BFA', muted: '#9090A0',
                    },
                    fontFamily: {
                        display: ['"Space Grotesk"', 'sans-serif'],
                        body: ['"Inter"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #0B0B0F; }
        .bracket-hover { position: relative; }
        .bracket-hover::before, .bracket-hover::after {
            content: ''; position: absolute; width: 14px; height: 14px;
            border-color: #7C3AED; opacity: 0; transition: opacity .25s, transform .25s; pointer-events: none;
        }
        .bracket-hover::before { top: -1px; left: -1px; border-top: 2px solid; border-left: 2px solid; transform: translate(4px,4px); }
        .bracket-hover::after  { bottom: -1px; right: -1px; border-bottom: 2px solid; border-right: 2px solid; transform: translate(-4px,-4px); }
        .bracket-hover:hover::before, .bracket-hover:hover::after { opacity: 1; transform: translate(0,0); }
    </style>
</head>
<body class="font-body text-[#F4F4F6] antialiased">

<div class="max-w-3xl mx-auto px-6 py-14 md:py-20">

    <!-- ─── Header ───────────────────────────────── -->
    <div class="flex flex-col items-center text-center mb-14">
        <div class="w-24 h-24 rounded-full bg-violet mb-5 overflow-hidden flex items-center justify-center">
            @if ($user->profile_photo)
                <img src="{{ \Storage::disk('public')->url($user->profile_photo) }}" class="w-full h-full object-cover" alt="{{ $user->name }}">
            @else
                <span class="font-display text-3xl font-700 text-white">{{ substr($user->name, 0, 1) }}</span>
            @endif
        </div>
        <h1 class="font-display text-2xl font-700">{{ $user->name }}</h1>
        <p class="font-mono text-sm text-violetb mt-1">@{{ $user->username }}</p>

        @if ($user->job_title)
            <p class="text-sm mt-3">{{ $user->job_title }}</p>
        @endif
        @if ($user->location)
            <p class="text-sm text-muted mt-1">📍 {{ $user->location }}</p>
        @endif
        @if ($user->bio)
            <p class="text-muted text-sm mt-4 max-w-md leading-relaxed">{{ $user->bio }}</p>
        @endif

        @if ($user->socialLinks->isNotEmpty())
            <div class="flex gap-4 mt-5">
                @foreach ($user->socialLinks as $link)
                    <a href="{{ $link->url }}" target="_blank" class="text-xs font-mono text-muted hover:text-violetb transition">
                        {{ $link->platform }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <!-- ─── Portfolio ────────────────────────────── -->
    @if ($user->portfolios->isNotEmpty())
        <section class="mb-14">
            <p class="font-mono text-xs text-violetb mb-4">// portfolio</p>
            <div class="grid md:grid-cols-2 gap-4">
                @foreach ($user->portfolios as $p)
                    <div class="bracket-hover bg-surface border border-border rounded-xl overflow-hidden">
                        <div class="h-36 bg-surface2">
                            @if ($p->thumbnail)
                                <img src="{{ \Storage::disk('public')->url($p->thumbnail) }}" class="w-full h-full object-cover" alt="{{ $p->title }}">
                            @endif
                        </div>
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-2">
                                <p class="font-medium">{{ $p->title }}</p>
                                @if ($p->is_featured)
                                    <span class="font-mono text-[9px] bg-violet/15 text-violetb px-2 py-0.5 rounded flex-shrink-0">★ unggulan</span>
                                @endif
                            </div>
                            @if ($p->category)
                                <p class="font-mono text-xs text-violetb mt-1">{{ $p->category }}</p>
                            @endif
                            @if ($p->description)
                                <p class="text-sm text-muted mt-2 leading-relaxed">{{ $p->description }}</p>
                            @endif
                            @if ($p->tech_stack)
                                <div class="flex gap-1.5 mt-3 flex-wrap">
                                    @foreach ($p->tech_stack as $tech)
                                        <span class="font-mono text-[10px] bg-bg border border-border px-2 py-1 rounded text-muted">{{ $tech }}</span>
                                    @endforeach
                                </div>
                            @endif
                            @if ($p->project_url || $p->github_url)
                                <div class="flex gap-4 mt-4">
                                    @if ($p->project_url)
                                        <a href="{{ $p->project_url }}" target="_blank" class="text-xs font-mono text-violetb hover:underline">↗ demo</a>
                                    @endif
                                    @if ($p->github_url)
                                        <a href="{{ $p->github_url }}" target="_blank" class="text-xs font-mono text-violetb hover:underline">↗ github</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- ─── Skills ───────────────────────────────── -->
    @if ($user->skills->isNotEmpty())
        @php $levelPercent = ['Beginner'=>20,'Elementary'=>40,'Intermediate'=>60,'Advanced'=>80,'Expert'=>100]; @endphp
        <section class="mb-14">
            <p class="font-mono text-xs text-violetb mb-4">// skills</p>
            <div class="space-y-3">
                @foreach ($user->skills as $s)
                    <div>
                        <div class="flex justify-between text-sm mb-1.5">
                            <span>{{ $s->name }}</span>
                            <span class="font-mono text-xs text-muted">{{ $s->level }}</span>
                        </div>
                        <div class="h-1.5 bg-surface rounded-full overflow-hidden">
                            <div class="h-full bg-violet rounded-full" style="width: {{ $levelPercent[$s->level] ?? 60 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- ─── Sertifikat ───────────────────────────── -->
    @if ($user->certificates->isNotEmpty())
        <section class="mb-14">
            <p class="font-mono text-xs text-violetb mb-4">// sertifikat</p>
            <div class="space-y-3">
                @foreach ($user->certificates as $c)
                    <div class="bg-surface border border-border rounded-lg px-5 py-4 flex items-center justify-between">
                        <div>
                            <p class="font-medium text-sm">{{ $c->title }}</p>
                            <p class="font-mono text-xs text-violetb mt-0.5">{{ $c->issuer }}</p>
                        </div>
                        @if ($c->credential_url)
                            <a href="{{ $c->credential_url }}" target="_blank" class="text-xs font-mono text-muted hover:text-violetb transition">verifikasi ↗</a>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <footer class="text-center pt-8 border-t border-border">
        <a href="{{ route('home') }}" class="font-mono text-xs text-muted hover:text-violetb transition">
            built with Folio<span class="text-violet">.</span>
        </a>
    </footer>
</div>

</body>
</html>

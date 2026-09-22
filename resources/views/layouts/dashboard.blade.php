<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Folio</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bg:      '#0B0B0F',
                        surface: '#14141B',
                        surface2:'#1C1C26',
                        border:  '#26262F',
                        violet:  '#7C3AED',
                        violetb: '#A78BFA',
                        muted:   '#9090A0',
                    },
                    fontFamily: {
                        display: ['"Space Grotesk"', 'sans-serif'],
                        body:    ['"Inter"', 'sans-serif'],
                        mono:    ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        body { background-color: #0B0B0F; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-thumb { background: #26262F; border-radius: 4px; }
    </style>
</head>
<body class="font-body text-[#F4F4F6] antialiased" x-data="{ sidebarOpen: false }">

    <div class="flex min-h-screen">
        <!-- ─── Sidebar ─────────────────────────────── -->
        <aside class="hidden md:flex md:flex-col w-64 border-r border-border bg-surface px-5 py-6 fixed h-screen">
            <a href="{{ route('home') }}" class="font-display text-xl font-700 text-white mb-10">
                Folio<span class="text-violet">.</span>
            </a>

            <nav class="flex flex-col gap-1 flex-1">
                @php
                    $navItems = [
                        ['route' => 'dashboard', 'label' => 'Overview', 'icon' => '◆'],
                        ['route' => 'portfolios.index', 'label' => 'Portfolio', 'icon' => '▣'],
                        ['route' => 'skills.index', 'label' => 'Skills', 'icon' => '◇'],
                        ['route' => 'certificates.index', 'label' => 'Sertifikat', 'icon' => '◈'],
                        ['route' => 'settings.index', 'label' => 'Pengaturan', 'icon' => '⚙'],
                    ];
                    if (auth()->check() && auth()->user()->isAdmin()) {
                        $navItems[] = ['route' => 'admin.dashboard', 'label' => 'Admin Panel', 'icon' => '★'];
                    }
                @endphp
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                              {{ request()->routeIs($item['route']) ? 'bg-violet/15 text-violetb' : 'text-muted hover:bg-surface2 hover:text-white' }}">
                        <span class="font-mono text-xs">{{ $item['icon'] }}</span>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-border pt-4">
                <a href="{{ route('public.show', auth()->user()->username) }}" target="_blank"
                   class="flex items-center gap-2 px-3 py-2 text-xs font-mono text-muted hover:text-violetb transition mb-2">
                    ↗ /{{ auth()->user()->username }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-sm text-muted hover:text-red-400 transition">
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- ─── Mobile top bar ──────────────────────── -->
        <div class="md:hidden fixed top-0 inset-x-0 z-30 bg-surface border-b border-border px-4 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}" class="font-display text-lg font-700 text-white">Folio<span class="text-violet">.</span></a>
            <button @click="sidebarOpen = !sidebarOpen" class="text-muted">☰</button>
        </div>
        <div x-show="sidebarOpen" x-cloak class="md:hidden fixed inset-0 z-20 bg-black/60" @click="sidebarOpen = false">
            <div class="bg-surface w-64 h-full p-5 pt-16" @click.stop>
                <nav class="flex flex-col gap-1">
                    @foreach ($navItems as $item)
                        <a href="{{ route($item['route']) }}" class="px-3 py-2.5 rounded-lg text-sm text-muted hover:bg-surface2 hover:text-white">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        <!-- ─── Main content ────────────────────────── -->
        <main class="flex-1 md:ml-64 px-5 md:px-10 py-8 md:py-10 pt-20 md:pt-10 max-w-5xl">
            @if (session('success'))
                <div class="mb-6 px-4 py-3 rounded-lg bg-violet/10 border border-violet/30 text-violetb text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>

</body>
</html>

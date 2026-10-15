<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Folio</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Franklin:wght@700&family=Source+Serif+4:ital,wght@0,400;0,600;1,400;1,600&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bg:      '#ffffff',
                        surface: '#f3f3f3',
                        surface2:'#e9e9e9',
                        border:  '#d4d4d4',
                        violet:  '#232323',
                        violetb: '#232323',
                        muted:   '#6e6e6e',
                    },
                    fontFamily: {
                        display: ['"Libre Franklin"', 'sans-serif'],
                        body:    ['"Source Serif 4"', 'Georgia', 'serif'],
                        mono:    ['"Source Serif 4"', 'Georgia', 'serif'],
                    }
                }
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        body { background-color: #ffffff; }
        button { color: #ffffff; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-thumb { background: #d4d4d4; }
    </style>
</head>
<body class="font-body text-[#232323] antialiased" x-data="{ sidebarOpen: false }">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="hidden md:flex md:flex-col w-64 border-r border-border bg-surface px-5 py-6 fixed h-screen">
            <a href="{{ route('home') }}" class="font-display text-xl font-700 mb-10" style="color:#232323;">
                Folio.
            </a>

            <nav class="flex flex-col gap-1 flex-1">
                @php
                    $navItems = [
                        ['route' => 'dashboard', 'label' => 'Overview', 'icon' => '◆'],
                        ['route' => 'portfolios.index', 'label' => 'Portfolio', 'icon' => '▣'],
                        ['route' => 'skills.index', 'label' => 'Skills', 'icon' => '◇'],
                        ['route' => 'certificates.index', 'label' => 'Sertifikat', 'icon' => '◈'],
                        ['route' => 'notifications.index', 'label' => 'Notifikasi', 'icon' => '✉', 'badge' => auth()->user()->unreadNotifications()->count()],
                        ['route' => 'settings.index', 'label' => 'Pengaturan', 'icon' => '⚙'],
                    ];
                    if (auth()->check() && auth()->user()->isAdmin()) {
                        $navItems[] = ['route' => 'admin.dashboard', 'label' => 'Admin Panel', 'icon' => '★'];
                    }
                @endphp
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium transition
                              {{ request()->routeIs($item['route']) ? 'bg-violet/15 text-[#232323]' : 'text-[#6e6e6e] hover:bg-surface2 hover:text-[#232323]' }}">
                        <span class="text-xs">{{ $item['icon'] }}</span>
                        {{ $item['label'] }}
                        @if (! empty($item['badge']))
                            <span class="ml-auto text-[10px] px-1.5 py-0.5 bg-[#232323] text-white">{{ $item['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-border pt-4">
                <a href="{{ route('public.show', auth()->user()->username) }}" target="_blank"
                   class="flex items-center gap-2 px-3 py-2 text-xs text-[#6e6e6e] hover:text-[#232323] transition mb-2">
                    folio.app/{{ auth()->user()->username }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-sm text-[#6e6e6e] hover:text-red-700 transition">
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- Mobile top bar -->
<!-- taste-skill-ignore -->        <div class="md:hidden fixed top-0 inset-x-0 z-30 bg-surface border-b border-border px-4 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}" class="font-display text-lg font-700" style="color:#232323;">Folio.</a>
            <button @click="sidebarOpen = !sidebarOpen" class="text-[#6e6e6e]">☰</button>
        </div>
        <div x-show="sidebarOpen" x-cloak class="md:hidden fixed inset-0 z-20 bg-black/20" @click="sidebarOpen = false">
            <div class="bg-surface w-64 h-full p-5 pt-16" @click.stop>
                <nav class="flex flex-col gap-1">
                    @foreach ($navItems as $item)
                        <a href="{{ route($item['route']) }}" class="px-3 py-2.5 text-sm text-[#6e6e6e] hover:bg-surface2 hover:text-[#232323]">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        <!-- Main content -->
        <main class="flex-1 md:ml-64 px-5 md:px-10 py-8 md:py-10 pt-20 md:pt-10 max-w-5xl">
            @if (session('success'))
                <div class="mb-6 px-4 py-3" style="background:#e9e9e9; color:#232323;">
                    {{ session('success') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>

</body>
</html>
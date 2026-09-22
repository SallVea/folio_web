<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Folio') — Portfolio Showcase</title>

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

    <style>
        body { background-color: #0B0B0F; }
        .bracket-hover { position: relative; }
        .bracket-hover::before, .bracket-hover::after {
            content: ''; position: absolute; width: 14px; height: 14px;
            border-color: #7C3AED; opacity: 0; transition: opacity .25s, transform .25s;
        }
        .bracket-hover::before { top: -1px; left: -1px; border-top: 2px solid; border-left: 2px solid; transform: translate(4px,4px); }
        .bracket-hover::after  { bottom: -1px; right: -1px; border-bottom: 2px solid; border-right: 2px solid; transform: translate(-4px,-4px); }
        .bracket-hover:hover::before, .bracket-hover:hover::after { opacity: 1; transform: translate(0,0); }
    </style>
</head>
<body class="font-body text-[#F4F4F6] antialiased">
    @yield('content')
</body>
</html>

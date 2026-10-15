<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Folio')</title>
    @stack('meta')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Franklin:wght@700&family=Source+Serif+4:ital,wght@0,400;0,600;1,400;1,600&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --bg: #ffffff;
            --surface: #f3f3f3;
            --ink: #232323;
            --muted: #6e6e6e;
            --accent: #a1ffcb;
            --accent-ink: #232323;
            --serif: "Source Serif 4", Georgia, serif;
            --display: "Libre Franklin", "Helvetica Neue", sans-serif;
        }
        * { box-sizing: border-box; min-width: 0; }
        html, body { margin: 0; overflow-x: clip; }
        body {
            background: var(--bg);
            color: var(--ink);
            font-family: var(--serif);
            font-size: 19px;
            line-height: 1.55;
            -webkit-font-smoothing: antialiased;
        }
        h1, h2, h3, .wordmark { font-family: var(--display); font-weight: 700; letter-spacing: -0.035em; }
        a { color: inherit; text-decoration: none; }
        a:hover { text-decoration: underline; }
        a:focus-visible, button:focus-visible, input:focus-visible { outline: 2px solid var(--ink); outline-offset: 3px; }
        img { display: block; max-width: 100%; }
        .js [data-animate] { opacity: 0; transform: translateY(24px); }
        [data-animate] { animation: riseIn 700ms cubic-bezier(.2,.7,.2,1) both; animation-play-state: paused; }
        .js [data-animate].is-in { animation-play-state: running; }
        @keyframes riseIn { to { opacity: 1; transform: none; } }
        @media (prefers-reduced-motion: reduce) {
            [data-animate] { opacity: 1 !important; transform: none !important; animation: none !important; }
            .drift { animation: none !important; }
        }
        .drift { animation: drift 22s ease-in-out infinite alternate; }
        @keyframes drift { from { transform: scale(1); } to { transform: scale(1.06); } }
        .surface { background: var(--surface); }
        .btn {
            display: inline-block;
            padding: 12px 20px;
            background: var(--ink);
            color: var(--bg);
            font-family: var(--display);
            font-weight: 700;
            font-size: 19px;
            border: 0;
            cursor: pointer;
            transition: transform 180ms ease;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn:active { transform: translateY(0); }
        input {
            background: var(--surface);
            border: 0;
            padding: 12px 14px;
            font-family: var(--serif);
            font-size: 19px;
            color: var(--ink);
            width: 100%;
        }
        input::placeholder { color: var(--muted); }
    </style>
</head>
<body>
    <script>document.documentElement.classList.add('js');</script>
    @yield('content')
    <script>
        document.querySelectorAll('[data-animate]').forEach(el => el.classList.add('is-in'));
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.querySelectorAll('video').forEach(v => { v.pause(); v.removeAttribute('autoplay'); });
        }
    </script>
</body>
</html>
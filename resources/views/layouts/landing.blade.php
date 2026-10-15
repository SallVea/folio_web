<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Folio') — portfolio showcase</title>
    <meta name="description" content="Folio — satu URL untuk semua karya, portfolio, skill, sertifikat, dan sosial.">
    <meta property="og:title" content="Folio — portfolio showcase">
    <meta property="og:description" content="Satu URL untuk semua karya.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Franklin:wght@700&family=Source+Serif+4:ital,wght@0,400;0,600;1,400;1,600&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>

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
        * { box-sizing: border-box; }
        html, body { margin: 0; }
        body {
            background: var(--bg);
            color: var(--ink);
            font-family: var(--serif);
            font-size: clamp(1.0625rem, 1rem + 0.25vw, 1.1875rem);
            line-height: 1.55;
            -webkit-font-smoothing: antialiased;
        }
        h1, h2, h3, .wordmark { font-family: var(--display); font-weight: 700; letter-spacing: -0.035em; }
        a { color: inherit; }
        a:focus-visible, button:focus-visible { outline: 2px solid var(--ink); outline-offset: 3px; }
        img { display: block; max-width: 100%; }
        * { min-width: 0; }
        html, body { overflow-x: clip; }
        h1, h2, h3, li, p { max-width: 100%; overflow-wrap: anywhere; }

        /* JS gate for motion (content stays visible without JS) */
        .js [data-animate] { opacity: 0; transform: translateY(24px); }
        [data-animate] { transition: opacity 700ms cubic-bezier(.2,.7,.2,1), transform 700ms cubic-bezier(.2,.7,.2,1); }
        .js [data-animate].is-in { opacity: 1; transform: none; }
        @media (prefers-reduced-motion: reduce) {
            [data-animate] { opacity: 1 !important; transform: none !important; transition: none !important; }
            .drift { animation: none !important; }
        }

        .hero-word {
            font-family: var(--display);
            font-weight: 700;
            font-size: clamp(220px, 32vw, 520px);
            line-height: 0.8;
            letter-spacing: -0.04em;
            white-space: nowrap;
            margin: 0;
            color: var(--ink);
        }
        .hero {
            position: relative;
            min-height: 112svh;
            overflow: hidden;
            padding: 28px;
            isolation: isolate;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .hero-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            z-index: 2;
        }
        .hero-body {
            z-index: 2;
            max-width: 34ch;
            margin-top: 24px;
        }
        .hero-body p { font-size: clamp(1.25rem, 1.1rem + 0.7vw, 1.625rem); line-height: 1.4; margin: 0 0 24px; }
        .hero-word-wrap {
            position: relative;
            left: 0;
            right: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            z-index: 1;
            overflow: hidden;
        }
        .btn {
            display: inline-block;
            padding: 14px 22px;
            background: var(--accent);
            color: var(--accent-ink);
            font-family: var(--display);
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            border: 0;
            cursor: pointer;
            transition: transform 180ms ease, filter 180ms ease;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn:active { transform: translateY(0); }
        .btn-dark { background: var(--ink); color: var(--bg); }

        .section {
            padding: 120px 28px;
            max-width: 1280px;
            margin: 0 auto;
        }
        .section-head { font-size: clamp(2rem, 1.3rem + 3.2vw, 4rem); line-height: 1.05; margin: 0 0 64px; }

        .module {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            max-width: 640px;
            margin-bottom: 96px;
        }
        .module-panel {
            flex: 0 0 360px;
            width: 360px;
            min-height: 120px;
            background: var(--surface);
            overflow: hidden;
        }
        .module-panel img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            aspect-ratio: 3 / 2;
            transition: transform 9s ease;
        }
        .drift { animation: drift 22s ease-in-out infinite alternate; }
        @keyframes drift { from { transform: scale(1); } to { transform: scale(1.06); } }
        .module-text { width: 250px; }
        .module-text h3 { font-size: 18px; font-weight: 600; margin: 0 0 8px; font-family: var(--serif); }
        .module-text p { font-size: 19px; color: var(--ink); margin: 0 0 6px; }
        .module-text p:first-of-type { color: var(--ink); }

        .cta {
            background: var(--ink);
            color: var(--bg);
            min-height: 90svh;
            padding: 64px 28px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .cta h2 {
            font-size: clamp(3.5rem, 10vw, 9rem);
            line-height: 0.9;
            margin: 0;
            max-width: 12ch;
        }
        .cta-row {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 24px;
        }
        .cta-row p { font-size: 20px; margin: 0; max-width: 40ch; }
        .cta .btn { background: var(--accent); color: var(--accent-ink); }

        footer.site-footer {
            background: var(--ink);
            color: var(--bg);
            min-height: 100svh;
            padding: 40px 28px 20px;
            display: grid;
            grid-template-rows: auto 1fr auto;
            position: relative;
        }
        .footer-cols {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 24px;
            font-size: 15px;
        }
        .footer-cols h3 { font-size: 16px; font-weight: 700; margin: 0 0 12px; font-family: var(--display); letter-spacing: -0.02em; }
        .footer-cols ul { list-style: none; margin: 0; padding: 0; }
        .footer-cols li { margin: 0 0 6px; color: #f2f2f2; font-size: 16px; }
        .footer-cols li:first-child { color: var(--bg); }
        .footer-wordmark {
            font-family: var(--display);
            font-weight: 700;
            font-size: clamp(120px, 24vw, 320px);
            line-height: 0.85;
            letter-spacing: -0.04em;
            margin: 0;
            white-space: nowrap;
            color: var(--bg);
            transform-origin: left bottom;
            animation: wordScale 1.6s ease both;
        }
        @keyframes wordScale { from { transform: scale(0.96); } to { transform: scale(1); } }
        .footer-legal { font-size: 16px; color: #d0d0d0; display: flex; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
        .credits { font-size: 16px; color: #d0d0d0; margin-top: 24px; max-width: 60ch; }

        @media (max-width: 760px) {
            .hero-body { max-width: calc(100% - 56px); }
            .hero-word { writing-mode: vertical-rl; transform: rotate(180deg); font-size: clamp(180px, 62vw, 300px); }
            .hero-word-wrap { left: auto; right: auto; top: auto; bottom: auto; width: auto; display: block; justify-content: initial; }
            .hero-top { flex-wrap: wrap; }
            .nav-links { display: none; }
            .module { flex-direction: column; max-width: 100%; }
            .module-panel { width: 100%; flex-basis: auto; }
            .module-text { width: 100%; }
            .cta-row { flex-direction: column; align-items: stretch; }
            .cta-row p { max-width: none; }
            .footer-cols { grid-template-columns: 1fr 1fr; }
        }
        main { position: relative; z-index: 1; }
    </style>
</head>
<body>
    <script>document.documentElement.classList.add('js');</script>
    @yield('content')
    <script>
        // entrances
        const io = ('IntersectionObserver' in window) ? new IntersectionObserver((entries)=>{
            entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('is-in'); io.unobserve(e.target);} });
        },{threshold:.15}) : null;
        if(io) document.querySelectorAll('[data-animate]').forEach(el=>io.observe(el));

        // scaling wordmark on scroll (signature move)
        const word = document.querySelector('.hero-word');
        const onScroll = () => {
            if(!word) return;
            const r = word.getBoundingClientRect();
            const vh = window.innerHeight;
            const p = Math.min(1, Math.max(0, (vh - r.top) / (vh + r.height)));
            const s = 1 - (p * 0.06);
            word.style.transform = 'scale(' + s.toFixed(4) + ')';
        };
        window.addEventListener('scroll', onScroll, {passive:true});
        onScroll();

        // live username thing
        const input = document.querySelector('#username');
        const out = document.querySelector('#url-out');
        if(input && out){
            const sync = () => {
                const v = input.value.trim().toLowerCase().replace(/[^a-z0-9_-]/g,'');
                out.textContent = v ? 'folio.app/' + v : 'folio.app/username';
                const preview = document.querySelector('#preview-url');
                if (preview) preview.textContent = v ? 'folio.app/' + v : 'folio.app/username';
            };
            input.addEventListener('input', sync);
            sync();
        }

        // respect reduced motion for the moving picture
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.querySelectorAll('video').forEach(v => { v.pause(); v.removeAttribute('autoplay'); });
        }
    </script>
</body>
</html>
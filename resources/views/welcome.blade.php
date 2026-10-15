@extends('layouts.landing')

@section('title', 'Folio')

@section('content')

<!-- ─── Hero ─────────────────────────────────────── -->
<section class="hero">
    <div class="hero-top" data-animate>
        <a href="/" class="wordmark" style="font-size:16px;">Folio<span style="color:var(--accent);">.</span></a>
        <nav class="nav-links flex items-center gap-6" style="font-family:var(--display);">
            <a href="{{ route('login') }}">Masuk</a>
            <a href="{{ route('register') }}" class="btn" style="padding:8px 14px;">Daftar</a>
        </nav>
    </div>
    <div class="hero-body" data-animate>
        <p>
            Satu URL untuk semua karya. Tambah dari HP, link langsung update. Buat dosen, HRD, atau siapapun yang tanya.
        </p>
        <form id="hero-form" class="mt-4" action="{{ route('register') }}" method="get" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            <label for="username" class="sr-only">Username</label>
            <input id="username" name="username" type="text" placeholder="username" style="background:transparent; border:0; border-bottom:1px solid var(--ink); padding:8px 0; font-family:var(--serif); font-size:1rem; width:200px;">
            <span id="url-out" style="font-family:var(--display); font-size:1.25rem;">folio.app/username</span>
            <button type="button" id="hero-cta" class="btn">Lihat Contoh</button>
        </form>

        <!-- Mini preview profil (muncul saat username valid) -->
        <div id="hero-preview" style="display:none; margin-top:16px; max-width:34ch; border:1px solid #d4d4d4; padding:12px 14px; background:#f3f3f3;">
            <p style="margin:0; font-family:var(--display); font-weight:600;" id="preview-name">—</p>
            <p style="margin:4px 0 8px; color:var(--muted); font-size:.95rem;" id="preview-job">—</p>
            <p style="margin:0; font-size:.9rem;" id="preview-stats">0 portfolio · 0 skill · 0 sertifikat</p>
            <a id="preview-link" href="#" target="_blank" rel="noopener" style="display:inline-block; margin-top:8px; font-size:.9rem; text-decoration:underline;">Buka profil →</a>
        </div>
        <p id="hero-preview-empty" style="display:none; margin-top:12px; color:var(--muted); font-size:.9rem;">
            Username belum ditemukan. <a href="{{ route('register') }}" style="text-decoration:underline;">Daftar sekarang</a> untuk ambil namanya.
        </p>

    </div>
    <div class="hero-word-wrap" aria-hidden="true">
        <h1 class="hero-word">FOLIO</h1>
    </div>
</section>

<script>
(function () {
    const input  = document.querySelector('#username');
    const out    = document.querySelector('#url-out');
    const btn    = document.querySelector('#hero-cta');
    const box    = document.querySelector('#hero-preview');
    const empty  = document.querySelector('#hero-preview-empty');
    const pName  = document.querySelector('#preview-name');
    const pJob   = document.querySelector('#preview-job');
    const pStats = document.querySelector('#preview-stats');
    const pLink  = document.querySelector('#preview-link');

    if (!input || !out) return;

    let timer = null;
    let lastFound = null;

    const syncUrl = () => {
        const v = input.value.trim().toLowerCase().replace(/[^a-z0-9_-]/g, '');
        out.textContent = v ? 'folio.app/' + v : 'folio.app/username';
        if (v) {
            input.value = v;
        }
    };

    const fetchPreview = (v) => {
        if (!v) {
            box.style.display = 'none';
            empty.style.display = 'none';
            lastFound = null;
            return;
        }
        fetch('/api/v1/u/' + encodeURIComponent(v), { headers: { 'Accept': 'application/json' } })
            .then(r => r.ok ? r.json() : null)
            .then(data => {
                if (data && data.success && data.data) {
                    const d = data.data;
                    lastFound = v;
                    pName.textContent  = d.name || d.username;
                    pJob.textContent   = d.job_title || d.bio || '';
                    const port = Array.isArray(d.portfolios) ? d.portfolios.length : (d.portfolios?.total ?? 0);
                    const skl  = Array.isArray(d.skills) ? d.skills.length : (d.skills?.total ?? 0);
                    const cert = Array.isArray(d.certificates) ? d.certificates.length : (d.certificates?.total ?? 0);
                    pStats.textContent = port + ' portfolio · ' + skl + ' skill · ' + cert + ' sertifikat';
                    pLink.href = '/' + v;
                    box.style.display = 'block';
                    empty.style.display = 'none';
                } else {
                    lastFound = null;
                    box.style.display = 'none';
                    empty.style.display = 'block';
                }
            })
            .catch(() => {
                lastFound = null;
                box.style.display = 'none';
                empty.style.display = 'none';
            });
    };

    input.addEventListener('input', () => {
        syncUrl();
        const v = input.value.trim().toLowerCase().replace(/[^a-z0-9_-]/g, '');
        clearTimeout(timer);
        timer = setTimeout(() => fetchPreview(v), 400);
    });
    syncUrl();

    if (btn) {
        btn.addEventListener('click', () => {
            const v = input.value.trim().toLowerCase().replace(/[^a-z0-9_-]/g, '');
            if (v && lastFound === v) {
                window.open('/' + v, '_blank', 'noopener');
            } else {
                window.location.href = '{{ route('register') }}' + (v ? ('?username=' + encodeURIComponent(v)) : '');
            }
        });
    }
})();
</script>


<!-- ─── Mark / supporting picture ───────────────── -->
<section class="section" style="padding-top:80px;">
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:32px; align-items:end;" data-animate>
        <h2 class="section-head" style="margin:0;">
            Empat bagian.
        </h2>
        <p style="color:var(--ink); max-width:42ch;">
            Portfolio, skill, sertifikat, dan sosial. Satu tempat yang update sendiri tiap kamu tambah karya
            dari HP. Tidak perlu edit di lima tempat berbeda.
        </p>
    </div>
</section>

<!-- ─── Modules ──────────────────────────────────── -->
<section class="section" style="padding-top:0;">
    @php
        $modules = [
            ['label' => 'Portfolio', 'desc' => 'Project, link demo, repo GitHub, lengkap dengan galeri foto.', 'img' => 'https://picsum.photos/id/63/720/480'],
            ['label' => 'Skills', 'desc' => 'Level kemampuan kamu, dari Beginner sampai Expert.', 'img' => 'https://picsum.photos/id/39/720/480'],
            ['label' => 'Sertifikat', 'desc' => 'Bukti sertifikasi dengan link verifikasi.', 'img' => 'https://picsum.photos/id/23/720/480'],
            ['label' => 'Sosial', 'desc' => 'GitHub, LinkedIn, dan akun lain dalam satu tempat.', 'img' => 'https://picsum.photos/id/486/720/480'],
        ];
    @endphp
    @foreach ($modules as $m)
        <article class="module" data-animate>
            <div class="module-panel">
                @if ($loop->first)
                    <video autoplay muted loop playsinline preload="metadata"
                           poster="assets/hunt-and-peck-typing-monkeytype-benchmark-poster.jpg"
                           width="854" height="480" aria-hidden="true"
                           style="width:100%;height:100%;object-fit:cover;display:block;">
                        <source src="assets/hunt-and-peck-typing-monkeytype-benchmark.webm" type="video/webm">
                    </video>
                @else
                    <img src="{{ $m['img'] }}"
                         alt="Still life photograph from an open collection, used as atmosphere."
                         width="720" height="480" loading="lazy" class="drift">
                @endif
            </div>
            <div class="module-text">
                <h3>{{ $m['label'] }}</h3>
                <p>{{ $m['desc'] }}</p>
                <p>Halaman publik langsung menampilkan yang terbaru.</p>
            </div>
        </article>
    @endforeach
</section>

<!-- ─── Bukti nyata (bukan testimoni palsu) ─────── -->
<section class="section" style="padding-top:0;">
    <div style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:24px; max-width:640px;" data-animate>
        <div>
            <p style="font-family:var(--display); font-size:clamp(2.5rem,6vw,4rem); margin:0;">{{ $stats['users'] }}</p>
            <p style="color:var(--muted); margin:4px 0 0;">kreator aktif</p>
        </div>
        <div>
            <p style="font-family:var(--display); font-size:clamp(2.5rem,6vw,4rem); margin:0;">{{ $stats['portfolios'] }}</p>
            <p style="color:var(--muted); margin:4px 0 0;">karya terpublikasi</p>
        </div>
    </div>
</section>

<!-- ─── FAQ ─────────────────────────────────────── -->
<section class="section">
    <h2 class="section-head">Pertanyaan yang sering ditanyakan.</h2>
    <p style="color:var(--muted); max-width:60ch; margin:-40px 0 40px;">
        Masih ragu mulai? Ini beberapa hal yang paling sering ditanyakan sebelum bikin akun.
    </p>
    <div style="max-width:760px;">
        <details>
            <summary>Apakah Folio gratis dipakai?</summary>
            <p>Ya, bisa mulai gratis. Tidak ada kartu kredit, tidak ada masa trial. Daftar dan langsung isi profil.</p>
        </details>
        <details>
            <summary>Bisa custom username nanti jadi link folio.app/namaku?</summary>
            <p>Bisa. Cek ketersediaan username saat daftar. Link kamu akan jadi folio.app/username, unik dan mudah dihafal.</p>
        </details>
        <details>
            <summary>Link-nya cocok dibagikan ke dosen, HRD, atau klien?</summary>
            <p>Cocok. Halaman publik menampilkan portfolio, skill, sertifikat, dan sosial dalam satu tampilan rapi yang enak dibaca.</p>
        </details>
        <details>
            <summary>Bisa tambah karya langsung dari HP?</summary>
            <p>Bisa. Cukup buka browser, upload foto karya atau lempar link. Semuanya langsung tampil di halaman publik.</p>
        </details>
        <details>
            <summary>Bisa pilih bagian apa saja yang publik?</summary>
            <p>Ya. Atur visibilitas per section (portfolio, skill, sertifikat) dan urutannya sesuai selera.</p>
        </details>
        <details>
            <summary>Sertifikat bisa diverifikasi?</summary>
            <p>Bisa. Unggah bukti (PDF/JPG) dan cantumkan link kredensial. Sistem mengecek tautan secara otomatis.</p>
        </details>
        <details>
            <summary>Apakah data saya aman?</summary>
            <p>Akun dilindungi verifikasi email, rate limit login, dan audit aktivitas. Kamu juga bisa menghapus akun beserta data kapan saja.</p>
        </details>
    </div>
</section>

<!-- ─── CTA ──────────────────────────────────────── -->
<section class="cta">
    <div data-animate>
        <h2>Satu link. Kerja selesai.</h2>
    </div>
    <div class="cta-row" data-animate>
        <p>
            Bikin akun sekali, isi profil sekali, bagikan link itu ke siapapun: dosen, HRD, atau klien.
            Setiap karya, skill, dan sertifikat baru otomatis ikut tampil. Tanpa kirim PDF lagi.
        </p>
        <a href="{{ route('register') }}" class="btn">Mulai Sekarang</a>
        @if ($exampleProfile)
            <a href="{{ route('public.show', $exampleProfile) }}" target="_blank" rel="noopener" style="color:var(--bg); text-decoration:underline;">Lihat contoh profil nyata</a>
        @endif
    </div>
</section>

<!-- ─── Footer ───────────────────────────────────── -->
<footer class="site-footer" data-animate>
    <div class="footer-cols">
        <div>
            <h3>Folio</h3>
            <ul>
                <li>folio.app/username</li>
                <li>One URL per person</li>
            </ul>
        </div>
        <div>
            <h3>Navigasi</h3>
            <ul>
                <li><a href="{{ route('login') }}">Masuk</a></li>
                <li><a href="{{ route('register') }}">Daftar</a></li>
            </ul>
        </div>
        <div>
            <h3>Isi</h3>
            <ul>
                <li>Portfolio</li>
                <li>Skills</li>
                <li>Sertifikat</li>
                <li>Sosial</li>
            </ul>
        </div>
        <div>
            <h3>Kontak</h3>
            <ul>
                <li>hello@folio.app</li>
                <li>built by Sall</li>
            </ul>
        </div>
    </div>
    <div></div>
    <div>
        <p class="footer-wordmark">FOLIO</p>
        <div class="footer-legal">
            <span>© Folio</span>
            <span>built by Sall</span>
        </div>
        <p class="credits">
            Foto atmosfer dari Lorem Picsum (lisensi Unsplash): Justin Leibow, Luke Chesser, Alejandro Escamilla, dan Florian Klauer. Video: "Hunt and peck typing Monkeytype benchmark" oleh C40115, CC BY 4.0, via Wikimedia Commons.
        </p>
    </div>
</footer>

@endsection
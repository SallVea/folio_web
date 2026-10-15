# 🗂️ Folio — Personal Portfolio Showcase

Folio adalah platform portofolio personal: setiap pengguna punya **satu URL publik** (`folio.app/username`) yang merangkum portfolio, skill, sertifikat, dan tautan sosial. Tersedia versi web (dashboard) dan RESTful API (Laravel Sanctum) yang dapat dikonsumsi oleh aplikasi klien (mis. Android). Dilengkapi halaman publik yang bisa dibagikan (link/QR/vCard) plus mode ringkas/detail.

> Dibangun di atas **Laravel 11** + **PostgreSQL 15** + **Docker Compose**. Tanpa build frontend (Tailwind & Alpine via CDN).

---

## 🏗️ Dibangun di atas (Tech Stack)

**Backend**
- PHP 8.2 (container) / Laravel 11 (framework)
- PostgreSQL 15 (database utama)
- Laravel Sanctum (API token auth)
- Laravel Socialite (OAuth Google & GitHub)
- Laravel Mail + Notification (email + in-app notification)
- Intervention-free image compression via **GD** (custom `ImageOptimizer`)
- Queue: sync; Cache: file/array; Session: file

**Frontend (web)**
- Blade templates + Alpine.js (interaktivitas)
- Tailwind CSS (via CDN)
- Chart.js (dashboard analytics), Marked? tidak.
- QRCode.js (QR profil), vCard download

**Infrastruktur**
- Docker Compose: `app` (PHP-FPM), `db` (Postgres), `nginx`
- Nginx serve port host `8080`
- Postgres host port `5433`
- Volume persisten `pgdata`

**Utilities**
- Composer, PHPUnit 10.5
- OpenAPI/Swagger docs di `/api/docs`

---

## ✅ Fitur (checklist)

### Landing
- Hero + wordmark besar, tagline, pencarian username (mini preview profil dinamis via API)
- Statistik jujur (kreator aktif + karya terpublikasi, dihitung dari DB, bukan angka palsu)
- Section "Empat bagian" (Portfolio, Skill, Sertifikat, Sosial)
- FAQ (7 pertanyaan umum)
- CTA "Satu link. Kerja selesai."
- Tombol "Lihat contoh profil nyata" (memilih user dengan portfolio featured/terbanyak, auto-hide jika tidak ada)
- Social proof tanpa testimoni palsu (link ke profil nyata)
- Open Graph + canonical dinamis (`url()->current()`)
- Responsif (mobile-first)

### Auth
- Register, login, logout (audit logged)
- Email verification wajib sebelum akses dashboard (`verified` middleware)
- OAuth Google & GitHub (Socialite)
- Password reset (forgot + token)
- Show/hide password, strength meter
- Rate limit: login/register 5/menit, forgot 3/menit
- Inactive account block (web + API)
- Account deletion (cascade DB + session invalidation)

### Dashboard
- Profile completion, quick actions
- Certificate expiry reminders
- Chart.js: views 7 & 30 hari, top referrer, portfolio by category, skills by level
- Notification center (bell badge, mark read/all)
- Data export (JSON)
- Settings: privasi per section, urutan section, username validation real-time, foto profil, link sosial + preview, hapus akun

### Portfolio
- CRUD, drag-and-drop reorder, drag-move (up/down), duplicate
- Multi-foto galeri (upload/hapus)
- Upload thumbnail (optimasi via GD)
- Validasi URL HTTPS
- Search & filter kategori (dashboard)

### Skills
- CRUD, level (Beginner → Expert), kategori
- Drag reorder (button + drag)
- Level history (tercatat tiap perubahan, tampil di edit panel)
- Import/export CSV & JSON

### Certificates
- CRUD + inline edit
- Upload bukti (JPG/PNG/WEBP/PDF, PDF supported di dashboard & profil publik)
- Validasi credential URL (HTTPS + private-host guard, tanpa redirect following)
- Email & in-app reminder kadaluarsa (command `certificates:remind` + schedule harian, anti-spam 7 hari)
- Status verifikasi (valid/invalid/unknown)

### Public Profile
- URL `/{username}` (case-sensitive, hanya user aktif)
- Filter & search portfolio, group by category
- Mode tampilan Ringkas / Detail
- QR code & vCard download
- Referral tracking (`referrer` kolom)
- Privasi per section + custom section order (`section_order`)
- OG tags (title/description/image/canonical) + vCard
- Anti-clickjacking header

### Admin
- Dashboard (stats, trend registrasi 7 hari)
- Manajemen user: detail, search/filter, activate/deactivate, ubah role
- Audit log (paginated)
- Akses dibatasi middleware `active` + `admin`
- Token API dicabut saat deaktivasi

### API (Sanctum, `/api/v1`)
- Auth (register/login/logout/me) — rate limited
- Profile, portfolio (+thumbnail/galeri), skills, certificates, social links, stats
- Public profile `GET /u/{username}` + record view
- Endpoint terproteksi middleware `active` (akun nonaktif ditolak)

### Global/Backend
- OpenAPI docs + Swagger UI (`/api/docs`)
- Export data (JSON)
- Audit log infra
- Image optimization & storage cleanup (`storage:cleanup`, scheduled mingguan)
- Security headers (X-Frame-Options, nosniff, Referrer-Policy, X-XSS-Protection, Permissions-Policy, HSTS)
- Force HTTPS di produksi
- Pagination Tailwind

---

## 🔒 Keamanan

### Akses & autorisasi
- `verified` middleware (email wajib terverifikasi sebelum dashboard)
- `active` middleware (akun nonaktif tidak bisa login / di-logout otomatis; token API dicabut)
- `admin` middleware (panel admin)
- Semua resource diakses via relasi milik user (`auth()->user()->portfolios()->findOrFail($id)`) → **tidak ada IDOR** (web + API)

### Input & data
- Validasi Laravel + custom `HttpUrl` rule (hanya skema http/https → blokir `javascript:`, `data:`, `vbscript:`)
- Portfolio/project/github/credential URL wajib HTTPS (`starts_with:https://`)
- Credential verify: DNS resolve, blokir IP privat/loopback, port 443 saja, **tanpa follow redirect** (anti SSRF)
- Upload file: MIME nyata + limit ukuran (5MB), mimes jpg/jpeg/png/webp/pdf
- Password minimal 8 karakter + mengandung huruf & angka; update via reset/login konsisten
- CSRF pada semua route web (Laravel default)
- Blade `{{ }}` auto-escape → output aman; tidak ada `{!! !!}`

### Transport & headers
- SecurityHeaders: `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, `Referrer-Policy: strict-origin-when-cross-origin`, `X-XSS-Protection`, `Permissions-Policy`, `HSTS` saat HTTPS
- Force HTTPS di production (`AppServiceProvider`)
- `secure` cookie disarankan di prod

### Anti-abuse
- Rate limit throttle: login/register 5/menit, forgot 3/menit, public profile 60/menit, record view 30/menit
- Token limit (maks 5 per user, hapus ter-oldest saat melebihi)
- Audit log untuk event auth & admin

### Akun
- Hapus akun: cascade data + invalidate session + token dicabut
- Toggle nonaktif admin mencabut token API
- `is_active` default true, model attribute default

---

## 🗄️ Database

**Engine:** PostgreSQL 15 (`folio_db`, user `folio_user`, password `folio_pass`)

**Tabel utama:**
- `users` (auth, profil, privasi, section_order, is_active, role)
- `portfolios` (+`portfolio_images`)
- `skills` (+`skill_level_histories`)
- `certificates` (+`reminder_sent_at`)
- `social_links`
- `portfolio_views` (viewer_ip, referrer, viewed_at)
- `audit_logs`
- `personal_access_tokens` (Sanctum)
- `notifications`

FK: cascade on delete untuk portfolio/skill/certificate/social per user.

---

## ⚙️ Instalasi (ringkas)

```bash
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan storage:link
```

Akses:
- Web: http://localhost:8080
- API base: http://localhost:8080/api/v1
- API docs (Swagger UI): http://localhost:8080/api/docs

Port DB host: `5433`.

---

## 🧪 Testing

```bash
./vendor/bin/phpunit   # phpunit 10.5, PHP 8.2
```
Konfigurasi `phpunit.xml` memakai SQLite `:memory:` + `CACHE_STORE=array` + `SESSION_DRIVER=array` + `MAIL_MAILER=array`.

Suite saat ini: **25 tests, 65 assertions (OK)**.

---

## 🔧 Perintah Berguna

```bash
make setup    # instalasi penuh
make up       # nyalakan
make down     # matikan
make logs     # log
make bash     # shell container app
make migrate  # migrasi
make clear    # bersihkan cache
make reset    # ⚠️ hapus semua data DB
make ps / make routes
php artisan certificates:remind     # kirim reminder (daily via scheduler)
php artisan storage:cleanup --force # hapus file yatim
```

---

## 🌍 Environment (.env)

Variabel penting:
```
APP_ENV=local / production
APP_DEBUG=true / false   # wajib false di prod
APP_KEY=...
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=folio_db
DB_USERNAME=folio_user
DB_PASSWORD=folio_pass
SESSION_DRIVER=file
SESSION_SECURE_COOKIE=true   # prod
SESSION_ENCRYPT=true          # prod (opsional)
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public
MAIL_MAILER=log  # smtp di prod
# OAuth
GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET / GOOGLE_REDIRECT_URI
GITHUB_CLIENT_ID / GITHUB_CLIENT_SECRET / GITHUB_REDIRECT_URI
# SMS opsional
SMS_ENABLED=false
SMS_DRIVER=log
SMS_WEBHOOK=
SMS_TOKEN=
```

---

## 📁 Struktur Penting

```
app/
  Http/Controllers/ (web + api)
  Http/Middleware/ (IsAdminMiddleware, SecurityHeaders, EnsureAccountIsActive)
  Models/
  Notifications/ (CertificateExpiryReminder + channels/SmsChannel)
  Rules/ (HttpUrl)
  Support/ (ImageOptimizer)
  Console/Commands/ (SendCertificateReminders, CleanupStorage)
resources/views/
  layouts/ (landing, public, dashboard, guest)
  welcome.blade.php (landing)
  public-profile.blade.php
  dashboard/* (portfolio, skills, certificates, settings, notifications)
  admin/* (dashboard, users, user-detail, audit-logs)
routes/
  web.php, api.php, console.php
database/migrations/ (19 migration)
tests/Feature/ (Auth, Portfolio, Security, Notification, PublicProfile, SecurityHeaders)
docs: openapi.json
```

---

## 🎨 Desain Web

- Landing: editorial minimal (dark/light contrast via class `darkMode`), Tailwind CDN.
- Auth: layout publik dengan auth form.
- Dashboard: sidebar + Alpine.js, Chart.js.
- Profil publik: tema personal dengan variabel CSS.
- Tidak perlu `npm run build`.

### Catatan lisensi media
Foto atmosfer berasal dari Lorem Picsum (lisensi Unsplash): Justin Leibow, Luke Chesser, Alejandro Escamilla, Florian Klauer.
Video: "Hunt and peck typing Monkeytype benchmark" oleh C40115 (CC BY 4.0, Wikimedia Commons).

---

## 📌 Item Opsional (belum diimplementasikan)
- Content-Security-Policy ketat (Tailwind CDN + inline script)
- Image optimization lanjutan (srcset, webp/avif)
- 2FA, password strength library (zxcvbn)
- Admin tidak bisa menonaktifkan/dirikanan diri sendiri (lockout guard)
- API wajib verifikasi email
- Wipe storage file saat hapus akun (saat ini via `storage:cleanup`)
- Mobile E2E test

---

© Folio — built by Sall.
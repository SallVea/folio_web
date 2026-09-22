# 🗂️ Folio — Personal Portfolio Showcase

Project Laravel **lengkap dan berdiri sendiri**. Ini BUKAN patch — langsung **replace** seluruh folder project Laravel kamu yang lama dengan isi folder ini.

---

## ⚠️ PENTING Sebelum Mulai

1. **Backup dulu** project lama kamu kalau ada data penting yang belum di-export.
2. Folder ini **tidak menyertakan** `vendor/` — akan otomatis terisi saat `docker compose up --build` (proses `composer install` jalan otomatis di dalam Docker, butuh koneksi internet di komputer kamu).
3. Tidak perlu `npm install` / `npm run build` — Tailwind CSS & Alpine.js dimuat lewat CDN langsung di Blade layout, tidak ada proses build frontend sama sekali.

---

## 📁 Isi Project

```
folio/
├── app/
│   ├── Models/              (7 model: User, Portfolio, PortfolioImage, PortfolioView, Skill, Certificate, SocialLink)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/         (8 controller — endpoint untuk Android)
│   │   │   └── Web*.php     (7 controller — untuk tampilan web)
│   │   └── Resources/       (7 resource — format JSON response)
│   └── Providers/
├── bootstrap/                (app.php, providers.php)
├── config/                    (semua config Laravel: app, auth, database, sanctum, cors, dll)
├── database/
│   ├── migrations/           (7 migrasi bersih, urut dari nol)
│   └── seeders/
├── resources/views/           (13 file Blade: landing, auth, dashboard, halaman publik)
├── routes/
│   ├── api.php                (untuk Android)
│   ├── web.php                (untuk browser)
│   └── console.php
├── docker/                    (nginx config, php config)
├── public/index.php
├── artisan
├── composer.json
├── docker-compose.yml
├── Dockerfile
├── Makefile
└── .env
```

---

## 🚀 Cara Menjalankan (dari Nol)

### 1. Pastikan Docker Desktop sudah running

### 2. Masuk ke folder project ini, lalu jalankan:
```bash
make setup
```

Ini otomatis akan:
1. Build image Docker (termasuk `composer install` — Sanctum sudah otomatis ikut karena ada di `composer.json`)
2. Tunggu PostgreSQL siap
3. Generate `APP_KEY`
4. Jalankan semua migrasi (bikin semua tabel dari nol)
5. Buat storage link (biar foto bisa diakses lewat URL)
6. Bersihkan cache

Tunggu sampai muncul:
```
✅ Setup selesai!
🌐 Web    : http://localhost:8080
📱 Android: http://10.0.2.2:8080/api/v1
```

> Kalau tidak ada `make`, jalankan manual:
> ```bash
> docker compose up -d --build
> docker compose exec app php artisan key:generate
> docker compose exec app php artisan migrate
> docker compose exec app php artisan storage:link
> ```

---

## ✅ Verifikasi

```bash
make ps        # cek 3 container (db, app, nginx) statusnya "Up"
make routes     # lihat semua route terdaftar
```

**Buka di browser:**
| URL | Halaman |
|---|---|
| `http://localhost:8080/` | Landing page |
| `http://localhost:8080/register` | Daftar akun |
| `http://localhost:8080/login` | Masuk |
| `http://localhost:8080/dashboard` | Dashboard (setelah login) |
| `http://localhost:8080/{username}` | Halaman portofolio publik |

---

## 📱 Konfigurasi Android

Project Android (`folio_android.zip`) sudah diset ke:
```kotlin
// Emulator (default, tidak usah diubah)
const val BASE_URL = "http://10.0.2.2:8080/api/"

// HP Fisik — ganti dengan IP komputer kamu
const val BASE_URL = "http://192.168.x.x:8080/api/"
```
Cek IP komputer: `ipconfig` (Windows) / `ifconfig` (Mac/Linux). HP & komputer harus di WiFi yang sama.

---

## 🔑 Arsitektur Penting

- **Web** pakai session auth (`Auth::attempt`) — standar Laravel
- **Android** pakai token Sanctum (`Bearer token`) — lewat `/api/v1/*`
- Keduanya **independen**, tidak saling konflik, dan membaca/menulis ke **1 database PostgreSQL yang sama**
- Route `/{username}` di `web.php` ada di **baris paling bawah** agar tidak menangkap `/login`, `/register`, `/dashboard`

---

## 🛠️ Perintah Sehari-hari

```bash
make up        # nyalain Docker
make down      # matiin Docker
make logs      # lihat log (debugging)
make bash      # masuk ke dalam container Laravel
make migrate   # jalankan migrasi baru (kalau ada)
make clear     # bersihkan semua cache
make reset     # ⚠️ HAPUS SEMUA DATA & mulai dari nol lagi
```

---

## 🐛 Troubleshooting

| Masalah | Solusi |
|---|---|
| `composer install` gagal / timeout | Cek koneksi internet komputer, coba `docker compose build --no-cache app` |
| `Connection refused (pgsql)` | Database belum siap, tunggu ~20 detik setelah `docker compose up` |
| Halaman web blank/error 500 | `make logs` untuk lihat detail error |
| Foto tidak muncul setelah upload | `docker compose exec app php artisan storage:link` |
| Android tidak bisa connect | Pastikan Docker jalan (`make ps`) sebelum buka Android Studio |
| CSS/font tidak muncul di browser | Cek koneksi internet (Tailwind CDN & Google Fonts butuh internet) |
| Route 404 semua | `make clear`, cek `bootstrap/app.php` tidak tertimpa |

---

## 🎨 Desain

Dark (`#0B0B0F`) + violet (`#7C3AED`), font **Space Grotesk** (judul) + **Inter** (body) + **JetBrains Mono** (tag teknis/username) — konsisten antara Web dan Android.

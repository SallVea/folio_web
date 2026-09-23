# 🗂️ Folio — Personal Portfolio Showcase

Folio adalah sebuah sistem portofolio personal yang dibangun menggunakan framework Laravel. Sistem ini menyediakan antarmuka web untuk manajemen portofolio secara mandiri, sekaligus menyediakan RESTful API (menggunakan Laravel Sanctum) yang dapat dikonsumsi oleh aplikasi klien, seperti aplikasi Android.

---

## 🚀 Persyaratan Sistem

Sebelum memulai instalasi, pastikan sistem komputermu telah memasang:
- **Docker** dan **Docker Compose** (Sangat disarankan menggunakan Docker Desktop agar lebih mudah).
- **Make** (Opsional, sudah bawaan di Linux/Mac, untuk Windows bisa menggunakan WSL atau Git Bash).
- **Koneksi Internet** yang stabil (untuk mengunduh *image* Docker dan *dependency* Composer).

---

## 🛠️ Cara Menjalankan Project

Project ini sudah disiapkan agar sangat mudah dijalankan menggunakan kontainer Docker tanpa perlu menginstal PHP atau Composer di komputermu secara langsung.

### Langkah 1: Buka Terminal
Buka terminal kesukaanmu dan pastikan kamu sudah berada di dalam direktori project ini.

### Langkah 2: Proses Instalasi Otomatis
Jalankan perintah berikut untuk menyalakan dan mengatur project dari awal:

```bash
make setup
```

Perintah di atas sangat praktis karena akan mengotomatisasi hal-hal berikut:
1. Membangun (build) *image* Docker dan mengunduh seluruh *library* yang dibutuhkan (`composer install`).
2. Menyalakan *container* (Web, App, dan Database).
3. Menunggu hingga *database* PostgreSQL siap menerima koneksi.
4. Membuat *Application Key* (`APP_KEY`) untuk keamanan Laravel.
5. Menjalankan *database migrations* (membuat seluruh tabel dari nol).
6. Menghubungkan folder penyimpanan (storage link) agar foto/gambar bisa diakses.
7. Membersihkan *cache* aplikasi.

> **Catatan:** Jika komputermu tidak mendukung perintah `make`, jalankan baris perintah ini secara manual satu per satu:
> ```bash
> docker compose up -d --build
> docker compose exec app php artisan key:generate
> docker compose exec app php artisan migrate
> docker compose exec app php artisan storage:link
> ```

### Langkah 3: Akses Aplikasi
Jika proses sudah selesai dan terminal menunjukkan status sukses, kamu bisa membuka aplikasinya lewat peramban (browser):

- **Web App**: [http://localhost:8080](http://localhost:8080)
- **API Base URL**: `http://localhost:8080/api/v1`

---

## 🌐 Navigasi Halaman Web

Berikut adalah daftar halaman yang tersedia di dalam website:

| Halaman | URL | Keterangan |
|---|---|---|
| **Beranda (Landing)** | `http://localhost:8080/` | Halaman utama pengenalan aplikasi |
| **Daftar Akun** | `http://localhost:8080/register` | Untuk membuat akun baru |
| **Masuk** | `http://localhost:8080/login` | Login ke dalam sistem |
| **Dashboard** | `http://localhost:8080/dashboard` | Panel kontrol setelah kamu berhasil login |
| **Portofolio Publik** | `http://localhost:8080/{username}` | Halaman publik yang menampilkan karya pengguna |

---

## 📱 Konfigurasi API untuk Aplikasi Klien (Android)

Jika kamu ingin menyambungkan aplikasi Android ke sistem ini, pastikan untuk menggunakan konfigurasi Base URL berikut:

- Jika menggunakan **Emulator Android** (di komputer yang sama):
  ```kotlin
  const val BASE_URL = "http://10.0.2.2:8080/api/v1/"
  ```
- Jika menggunakan **Perangkat HP Fisik**:
  Ganti IP dengan IP Lokal komputer kamu. (Cek menggunakan perintah `ipconfig` di Windows atau `ifconfig` di Mac/Linux).
  ```kotlin
  const val BASE_URL = "http://192.168.x.x:8080/api/v1/"
  ```
  *(Pastikan HP dan Komputer terhubung dalam jaringan WiFi yang sama).*

---

## 💻 Panduan Perintah (Makefile)

Untuk mempermudah pekerjaan sehari-hari selama masa pengembangan, kamu bisa menggunakan perintah jalan pintas berikut:

```bash
make up        # Menyalakan project (berjalan di latar belakang)
make down      # Mematikan project
make logs      # Melihat log proses aplikasi (berguna jika ada error)
make bash      # Masuk ke dalam terminal container Laravel
make migrate   # Menjalankan migrasi database jika ada pembaruan
make clear     # Membersihkan semua cache sistem Laravel
make reset     # ⚠️ MENGHAPUS SEMUA DATA database dan memulai dari kondisi kosong
make ps        # Melihat status container Docker yang sedang berjalan
make routes    # Menampilkan daftar semua rute (routes) Laravel
```

---

## 🎨 Catatan Desain Web
Sistem web ini menggunakan gaya tema gelap. Tidak ada proses *build frontend* yang perlu dijalankan (`npm run build`) karena Tailwind CSS dan Alpine.js dimuat langsung melalui CDN. 
- Warna Utama: Dark (`#0B0B0F`) dan Violet (`#7C3AED`).
- Font: **Space Grotesk** (untuk Judul), **Inter** (untuk teks paragraf), dan **JetBrains Mono** (untuk teks nama pengguna/kode).

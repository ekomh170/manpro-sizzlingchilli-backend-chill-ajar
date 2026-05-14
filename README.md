# 📚 ChillAjar Backend API

<div align="center">
  <img src="./public/logo.png" alt="ChillAjar Logo" width="200" height="200">

  **REST API Platform pembelajaran online yang menghubungkan siswa dengan mentor berkualitas**

  Backend Laravel 12 modern dengan arsitektur scalable untuk mendukung ekosistem pembelajaran online yang lengkap!

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![Sanctum](https://img.shields.io/badge/Laravel%20Sanctum-Auth-FF2D20?style=flat-square)](https://laravel.com/docs/sanctum)

</div>

---

## ⭐ Tentang ChillAjar Backend

**ChillAjar Backend** adalah REST API yang powerful dan scalable, dibangun dengan Laravel 12 untuk mendukung platform pembelajaran online. Sistem ini menangani autentikasi, manajemen user, sistem paket pembelajaran, pembayaran, notifikasi WhatsApp, dan berbagai fitur kompleks lainnya dengan arsitektur yang clean dan maintainable.

---

## ✨ Fitur Utama

### 🔐 **Autentikasi & Otorisasi**
- **Sistem Multi-Role** - Admin, Mentor, dan Pelanggan dengan hak akses berbeda
- **Laravel Sanctum** - Autentikasi berbasis token yang aman
- **Password Hashing** - Enkripsi Bcrypt untuk keamanan maksimal
- **Role-based Access Control** - Perlindungan middleware untuk setiap endpoint

### 👥 **Manajemen User**
- **Operasi CRUD** - Manajemen lengkap untuk semua tipe user
- **Manajemen Profil** - Upload foto profil, update data personal
- **Statistik User** - Data dashboard untuk setiap role
- **Soft Delete** - Data historis tetap terjaga

### 📚 **Manajemen Kursus & Sesi**
- **Pembuatan Kursus Dinamis** - Mentor dapat membuat dan mengelola kursus
- **Penjadwalan Fleksibel** - Sistem jadwal dengan dukungan timezone
- **Gaya Mengajar** - Dukungan sesi online & offline
- **Pelacakan Sesi** - Status sesi (pending, started, end, reviewed)
- **Upload Foto** - Gambar kursus dengan validasi

### 💼 **Sistem Paket Pembelajaran (BARU)**
- **Bundle Items** - Modul PDF, Video, Quiz dalam satu paket
- **Auto-Kalkulasi** - Harga paket otomatis update saat item berubah
- **Visibilitas Paket** - Kontrol paket mana yang tersedia per kursus
- **Smart Pricing** - Formula: `harga_dasar = sum(max(item.harga - item.diskon, 0) * jumlah_item)`
- **Paket Opsional** - Pelanggan bisa pilih paket atau booking tanpa paket
- **Perlindungan Soft Delete** - Data historis tetap aman

### 💰 **Pembayaran & Transaksi**
- **Verifikasi Pembayaran** - Admin approve/reject pembayaran
- **Upload Bukti** - Bukti pembayaran (JPG, PNG, PDF max 10MB)
- **Sistem Re-upload** - Pelanggan bisa upload ulang jika ditolak
- **Status Transaksi** - Pelacakan status: menunggu, accepted, rejected
- **Download Bukti** - Endpoint untuk download bukti pembayaran

### 📱 **Notifikasi WhatsApp**
- **Notifikasi Otomatis** - Notifikasi otomatis saat event penting
- **Status Pembayaran** - Notif saat pembayaran diverifikasi/ditolak
- **Update Sesi** - Notif saat sesi dimulai/selesai
- **Integrasi Gateway** - Dukungan WhatsApp Web.js gateway
- **Format Pintar** - Auto-format nomor Indonesia (628xxx)

### ⭐ **Sistem Testimoni**
- **Sistem Rating** - Rating 1-5 bintang untuk mentor
- **Auto Update Status** - Sesi otomatis jadi 'reviewed' setelah testimoni
- **Analitik Mentor** - Dashboard dengan agregat rating
- **Dukungan Komentar** - Komentar detail dari pelanggan

---

## 🚀 **Teknologi yang Digunakan**

### Stack Backend
- **Laravel 12** - Framework PHP modern dan elegan
- **PHP 8.2+** - Fitur PHP terbaru untuk performa optimal
- **MySQL 8.0** - Database relasional yang powerful
- **Laravel Sanctum** - Autentikasi API yang secure
- **Guzzle HTTP** - Integrasi WhatsApp gateway
- **Intervention Image** - Pemrosesan & optimasi gambar

### Arsitektur & Pattern
- **RESTful API** - Metode HTTP standar (GET, POST, PUT, DELETE)
- **Pattern MVC** - Arsitektur Model-View-Controller
- **Repository Pattern** - Layer abstraksi untuk akses data
- **Service Layer** - Pemisahan logika bisnis
- **Eloquent ORM** - Query database yang elegan
- **Sistem Migrasi** - Version control untuk skema database

### Fitur Keamanan
- **Perlindungan CSRF** - Pencegahan Cross-Site Request Forgery
- **Pencegahan SQL Injection** - Prepared statements & query binding
- **Perlindungan XSS** - Sanitasi input
- **Rate Limiting** - API throttling untuk mencegah abuse
- **Validasi Upload File** - Validasi MIME type & ukuran
- **Enkripsi Password** - Bcrypt hashing

### WhatsApp Gateway (Terpisah)
- **Express.js Server** - Server Node.js terpisah untuk handle WhatsApp
- **WhatsApp Web.js** - Library untuk integrasi WhatsApp Web
- **HTTP Integration** - Laravel trigger notifikasi via Guzzle HTTP client
- **Auto Format** - Format nomor Indonesia otomatis (628xxx)

### Tools Developer
- **Artisan Commands** - Perintah CLI custom (hapus sesi expired, update rating)
- **Error Logging** - Monolog untuk pelacakan error
- **Git Version Control** - Workflow development kolaboratif

---

## 🐳 **Quick Start dengan Docker**

Setup development environment dalam hitungan menit menggunakan Docker!

### Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/Mac)
- [Docker & Docker Compose](https://docs.docker.com/) (Linux)

### Setup Otomatis (Recommended)
```powershell
# Windows PowerShell
.\docker-helper.ps1

# Linux/Mac
chmod +x docker-helper.sh
./docker-helper.sh
```
Pilih opsi **1) Setup awal** dan script akan otomatis:
- ✅ Setup environment file
- ✅ Build Docker containers (PHP, Nginx, MySQL, PHPMyAdmin)
- ✅ Install dependencies
- ✅ Generate application key
- ✅ Run migrations & seeders
- ✅ Setup storage link

### Setup Manual
```bash
# 1. Copy environment file
cp .env.docker .env

# 2. Build & start containers
docker-compose up -d --build

# 3. Install dependencies
docker-compose exec app composer install

# 4. Setup Laravel
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --seed
docker-compose exec app php artisan storage:link
docker-compose exec app php artisan config:cache
```

### Akses Services
- 🌐 **Laravel API**: http://localhost:9500
- 📊 **PHPMyAdmin**: http://localhost:9501
 - 🗄️ **MySQL**: `localhost:9502`

### Dokumentasi Lengkap Docker
📖 **[Baca dokumentasi lengkap Docker setup](./docker/README.md)**

---

## � **Deployment ke Production (Portainer)**

### Server Production
Backend sudah siap di-deploy ke server VPS menggunakan Portainer container management.

⚠️ **Informasi Credentials**: Untuk detail lengkap server, credentials, dan konfigurasi, lihat:
- 📄 **`docker/QUICK-REFERENCE.md`** (file lokal, tidak ter-commit untuk keamanan)
- 📄 **`docker/QUICK-REFERENCE.template.md`** (template yang bisa di-commit)

### Platform & Tools
- **Platform**: Portainer (Container Management)
- **VPN**: Twingate (Required for access)
- **Container Runtime**: Docker & Docker Compose

### Port Configuration
Port yang digunakan (aman dan tidak conflict):
- `9500` - Laravel API
- `9501` - PHPMyAdmin  
- `9502` - MySQL

### Quick Deploy Steps
1. **Install & Connect** - Twingate VPN
2. **Access Portainer** - Lihat URL di QUICK-REFERENCE.md
3. **Create Stack** - Upload `docker-compose.yml`
4. **Configure Environment** - Set production variables
5. **Deploy & Verify** - Check container status

### Dokumentasi Lengkap Deployment
📖 **[Panduan lengkap deployment ke Portainer](./docker/DEPLOYMENT-PORTAINER.md)**

Dokumentasi mencakup:
- ✅ Setup VPN & akses server
- ✅ Step-by-step deployment via Portainer
- ✅ Konfigurasi environment variables production
- ✅ Setup awal aplikasi (migration, seeder, admin)
- ✅ Troubleshooting common issues
- ✅ Monitoring & update aplikasi

---

## �📄 **Lisensi & Kepemilikan**

Backend API ini dikembangkan sebagai bagian dari project kolaboratif pendidikan. Arsitektur backend, desain database, dan logika bisnis dikembangkan dengan fokus pada skalabilitas, keamanan, dan best practices Laravel.

---

## 🤝 **Penghargaan**

- Lead Backend Developer : Eko Muchamad Haryono
- Lead Frontend Developer : Firenze Higa Putra
- Tim UI/UX yang luar biasa🎨
- QA tester yang detail & membantu 🐛
- Komunitas open source Laravel 💻
- Kontributor & beta tester 🙏
- Arsitektur Database : Skema dioptimalkan dengan relasi kompleks
- Desain API : Standar RESTful dengan dokumentasi komprehensif

---

## 📞 **Dukungan & Kontak**

Jika ada pertanyaan atau issue, silakan:
- 📧 Email team ekomh13@gmail.com
- 🐛 Buat issue di repository GitHub

---

<div align="center">

  **⭐ Jika API ini membantu development, jangan lupa kasih star ya! ⭐**

  Made with ❤️ by **Eko Muchamad Haryono**

</div>

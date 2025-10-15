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

## 📄 **Lisensi & Kepemilikan**

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

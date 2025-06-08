# Team Manpro SizzlingChili - Backend Laravel 12 Platform Menjadi Mentor Sebaya "Chill Ajar"

Sistem backend Laravel 12 dari platform Menjadi Mentor Sebaya "Chill Ajar" dari Tim Manpro SizzlingChili. Sistem ini mendukung manajemen user (admin, mentor, pelanggan), kursus, sesi pengajaran, pembayaran, testimoni, dan notifikasi sesuai kebutuhan bisnis.

---

## Fitur Utama

### 1. Autentikasi Pengguna
- Pelanggan & Mentor dapat mendaftar dan login.
- Admin dapat login untuk mengelola data.
- Logout untuk semua peran.

### 2. Pengelolaan Role & User
- Admin dapat mengubah role pengguna (admin, mentor, pelanggan).
- Admin dapat melihat, menambah, mengedit, dan menghapus data user, mentor, dan pelanggan.

### 3. Manajemen Kursus (Course)
- Admin dapat CRUD kursus.
- Pelanggan dapat melihat daftar kursus.
- Mentor dapat mengelola kursus yang diampu.

### 4. Pencarian Mentor & Detail
- Pelanggan dapat mencari mentor berdasarkan kursus.
- Pelanggan dapat melihat detail mentor (riwayat, rating, harga per sesi).

### 5. Manajemen Sesi Pengajaran
- Pelanggan dapat memesan sesi dengan mentor.
- Pelanggan memilih jadwal & gaya belajar (online/offline) yang tersedia.
- Mentor dapat mengelola jadwal & gaya mengajar.
- Mentor dapat konfirmasi & menyelesaikan sesi.

### 6. Pembayaran/Transaksi
- Pelanggan mengunggah bukti pembayaran.
- Admin dapat verifikasi atau menolak pembayaran.
- Sistem mengirim notifikasi ke pelanggan & mentor setelah pembayaran diverifikasi/ditolak.
- Notifikasi WhatsApp otomatis pada event tertentu (selesai sesi, pembayaran ditolak).

### 7. Testimoni
- Pelanggan dapat memberikan testimoni setelah sesi selesai.
- Mentor dapat melihat testimoni yang diterima.

---

## Catatan Penting Untuk Dev
- Gunakan Sanctum untuk autentikasi token.
- Notifikasi WhatsApp terintegrasi pada proses pembayaran & konfirmasi sesi.

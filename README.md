# Team Manpro SizzlingChili - Backend Laravel 12 Platform Menjadi Mentor Sebaya "Chill Ajar"

Sistem backend Laravel 12 dari platform Menjadi Mentor Sebaya "Chill Ajar" dari Tim Manpro SizzlingChili . Sistem ini mendukung manajemen user (admin, mentor, pelanggan), kursus, sesi pengajaran, pembayaran, testimoni, dan notifikasi sesuai kebutuhan bisnis.

---

## Fitur Utama

### 1. Autentikasi Pengguna
- Pelanggan & Mentor dapat mendaftar dan login.
- Admin dapat login untuk mengelola data.
- Logout untuk semua peran.

### 2. Pengelolaan Role & User
- Admin dapat mengubah role pengguna (admin, mentor, pelanggan).
- Admin dapat melihat, menambah, mengedit, dan menghapus data user, mentor, dan pelanggan.

### 3. Manajemen Mata Kuliah (Course)
- Admin dapat CRUD course.
- Pelanggan dapat melihat daftar course.
- Mentor dapat mengelola course yang diampu.

### 4. Pencarian Mentor & Detail
- Pelanggan dapat mencari mentor berdasarkan course.
- Pelanggan dapat melihat detail mentor (riwayat, rating, harga).

### 5. Manajemen Sesi Pengajaran
- Pelanggan dapat memesan sesi dengan mentor.
- Pelanggan memilih jadwal & gaya belajar (online/offline) yang tersedia.
- Mentor dapat mengelola jadwal & gaya mengajar.
- Mentor dapat konfirmasi & menyelesaikan sesi.

### 6. Pembayaran/Transaksi
- Pelanggan mengunggah bukti pembayaran (simulasi via Telegram).
- Admin dapat verifikasi atau menolak pembayaran.
- Sistem mengirim notifikasi ke pelanggan & mentor setelah pembayaran diverifikasi.

### 7. Testimoni
- Pelanggan dapat memberikan testimoni setelah sesi selesai.
- Mentor dapat melihat testimoni yang diterima.

## Catatan Penting Untuk Dev
- Gunakan Sanctum untuk autentikasi token.
- Notifikasi Telegram diintegrasikan pada proses pembayaran & konfirmasi sesi. (UnPrioritas)

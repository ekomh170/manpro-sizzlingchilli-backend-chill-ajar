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

---

## Struktur Controller

### AuthController
- `registrasiPelanggan`, `registrasiMentor`, `login`, `logout`

### AdminController
<!-- Create Pelanggan, Mentor, Pelanggan & User  -->
- `daftarPengguna`, `ubahRolePengguna`, `tambahPengguna`
<!-- Validasi Kondisi Ketika Mengakses Fungsi Ini di Fungsi Bikin Akun Pengguna  -->
- `tambahMentor`
- `tambahPelanggan`
<!-- Mentor -->
- `daftarMentor`, `perbaruiMentor`, `hapusMentor`, `detailMentor`
<!-- Pelanggan -->
- `daftarPelanggan`, `perbaruiPelanggan`, `hapusPelanggan`, `detailPelanggan`
<!-- Pembayaran -->
- `verifikasiPembayaran`, `tolakPembayaran`
<!-- Notif -->
- `notifikasiKeMentor`

> ⚠️ **Note**: Fungsi CRUD **Kursus**, **Sesi**, dan **Pembayaran** telah dipindahkan ke controller masing-masing (`CourseController`, `SessionController`, dan `PaymentController`) untuk menjaga modularitas dan mengikuti prinsip RESTful.

### MentorController
- `profilSaya`, 
- `aturJadwal`, `aturGayaMengajar`, `daftarKursusSaya`, `daftarSesiSaya`, `konfirmasiSesi`, `selesaikanSesi`, `daftarTestimoni`

### PelangganController
- `profilSaya`
- `daftarCourse`, `cariMentor`, `detailMentor`, `pesanSesi`, `daftarSesiMentor`, `unggahBuktiPembayaran`, `beriTestimoni`

### CourseController (resource)
- `index`, `store`, `show`, `update`, `destroy`

### SessionController (resource)
- `index`, `store`, `show`, `update`, `destroy`, `konfirmasiSesi`, `selesaiSesi`

### PaymentController (resource)
- `index`, `store`, `show`, `update`, `destroy`, `unggahBukti`, `verifikasiPembayaran`, `tolakPembayaran`

### TestimoniController (resource)
- `index`, `store`, `show`, `update`, `destroy`, `testimoniMentor`

---

## Struktur Database (Singkat)
- **User**: id, nama, email, password, nomorTelepon, peran, alamat
- **Admin, Mentor, Pelanggan**: turunan User
- **Course**: id, namaCourse, deskripsi, mentor_id
- **Session**: id, mentor_id, pelanggan_id, detailKursus, jadwal, statusSesi
- **Payment**: id, user_id, mentor_id, session_id, jumlah, statusPembayaran, metodePembayaran, tanggalPembayaran
- **Testimoni**: id, session_id, pelanggan_id, mentor_id, rating, komentar, tanggal

## Catatan Penting Untuk Dev
- Gunakan Sanctum untuk autentikasi token.
- Notifikasi Telegram diintegrasikan pada proses pembayaran & konfirmasi sesi. (UnPrioritas)

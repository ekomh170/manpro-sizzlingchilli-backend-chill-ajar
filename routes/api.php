<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// Import controllers - Auth
use App\Http\Controllers\Auth\AuthController;
// Import controllers - Mentor, Pelanggan, Admin
use App\Http\Controllers\Mentor\MentorController;
use App\Http\Controllers\Pelanggan\PelangganController;
use App\Http\Controllers\Admin\AdminController;
// Import controllers - Fitur
use App\Http\Controllers\Fitur\FiturController;
use App\Http\Controllers\Fitur\CourseController;

// ==================== AUTH ====================
// [POST] Login pengguna
Route::post('/login', [AuthController::class, 'login']);
// [POST] Registrasi pengguna baru
Route::post('/register', [AuthController::class, 'registrasi']);

// ==================== PROTECTED ROUTES (SANCTUM) ====================
Route::middleware(['auth:sanctum'])->group(function () {
    // [POST] Logout pengguna
    Route::post('/logout', [AuthController::class, 'logout']);

    // ==================== ADMIN ====================
    // [GET] Daftar semua pengguna
    Route::get('/admin/users', [AdminController::class, 'daftarPengguna']);
    // [PUT] Ubah role pengguna
    Route::put('/admin/users/{userId}/role', [AdminController::class, 'ubahRolePengguna']);
    // [POST] Tambah pengguna baru (admin/mentor/pelanggan)
    Route::post('/admin/users', [AdminController::class, 'tambahPengguna']);
    // [POST] Tambah mentor langsung
    Route::post('/admin/mentor', [AdminController::class, 'tambahMentor']);
    // [POST] Tambah pelanggan langsung
    Route::post('/admin/pelanggan', [AdminController::class, 'tambahPelanggan']);
    // [GET] Daftar mentor
    Route::get('/admin/mentor', [AdminController::class, 'daftarMentor']);
    // [GET] Detail mentor
    Route::get('/admin/mentor/{id}', [AdminController::class, 'detailMentor']);
    // [PUT] Update mentor
    Route::put('/admin/mentor/{id}', [AdminController::class, 'perbaruiMentor']);
    // [DELETE] Hapus mentor
    Route::delete('/admin/mentor/{id}', [AdminController::class, 'hapusMentor']);
    // [GET] Daftar pelanggan
    Route::get('/admin/pelanggan', [AdminController::class, 'daftarPelanggan']);
    // [GET] Detail pelanggan
    Route::get('/admin/pelanggan/{id}', [AdminController::class, 'detailPelanggan']);
    // [PUT] Update pelanggan
    Route::put('/admin/pelanggan/{id}', [AdminController::class, 'perbaruiPelanggan']);
    // [DELETE] Hapus pelanggan
    Route::delete('/admin/pelanggan/{id}', [AdminController::class, 'hapusPelanggan']);
    //  Pelanggan - Pembayaran Verifikasi - verifikasiPembayaran
    Route::post('/admin/verifikasi-pembayaran/{paymentId}', [AdminController::class, 'verifikasiPembayaran']);
    // Pelanggan - Pembayaran Tolak - tolakPembayaran
    Route::post('/admin/tolak-pembayaran/{paymentId}', [AdminController::class, 'tolakPembayaran']);
    // [POST] Kirim notifikasi ke mentor setelah pembayaran diverifikasi
    Route::post('/admin/notifikasi/mentor/{sessionId}', [AdminController::class, 'notifikasiKeMentor']);

    // ==================== MENTOR ====================
    // [GET] Profil mentor yang sedang login
    Route::get('/mentor/profil-saya', [MentorController::class, 'profilSaya']);
    // [POST] Atur jadwal pengajaran
    Route::post('/mentor/atur-jadwal', [MentorController::class, 'aturJadwal']);
    // [POST] Atur gaya mengajar
    Route::post('/mentor/atur-gaya', [MentorController::class, 'aturGayaMengajar']);
    // [GET] Daftar kursus yang diampu mentor
    Route::get('/mentor/daftar-course', [MentorController::class, 'daftarKursusSaya']);
    // [GET] Daftar sesi yang diampu mentor
    Route::get('/mentor/daftar-sesi', [MentorController::class, 'daftarSesiSaya']);
    // [POST] Konfirmasi sesi
    Route::post('/mentor/konfirmasi-sesi/{sessionId}', [MentorController::class, 'konfirmasiSesi']);
    // [POST] Selesaikan sesi
    Route::post('/mentor/selesai-sesi/{sessionId}', [MentorController::class, 'selesaiSesi']);
    // [GET] Daftar testimoni yang diterima mentor
    Route::get('/mentor/daftar-testimoni', [MentorController::class, 'daftarTestimoni']);

    // ==================== PELANGGAN ====================
    // [GET] Profil pelanggan yang sedang login
    Route::get('/pelanggan/profil-saya', [PelangganController::class, 'profilSaya']);
    // [GET] Daftar course
    Route::get('/pelanggan/daftar-course', [PelangganController::class, 'daftarCourse']);
    // [GET] Cari mentor berdasarkan mata kuliah
    Route::get('/pelanggan/cari-mentor', [PelangganController::class, 'cariMentor']);
    // [GET] Detail mentor
    Route::get('/pelanggan/detail-mentor/{mentorId}', [PelangganController::class, 'detailMentor']);
    // [POST] Pesan sesi pengajaran
    Route::post('/pelanggan/pesan-sesi', [PelangganController::class, 'pesanSesi']);
    // [GET] Daftar sesi yang pernah diikuti pelanggan
    Route::get('/pelanggan/daftar-sesi', [PelangganController::class, 'daftarSesiMentor']);
    // [POST] Unggah bukti pembayaran
    Route::post('/pelanggan/unggah-bukti/{paymentId}', [PelangganController::class, 'unggahBuktiPembayaran']);
    // [POST] Beri testimoni
    Route::post('/pelanggan/beri-testimoni/{sessionId}', [PelangganController::class, 'beriTestimoni']);

    // ==================== COURSE ====================
    // [GET] Daftar course
    Route::get('/courses', [CourseController::class, 'index']);
    // [POST] Tambah course
    Route::post('/courses', [CourseController::class, 'store']);
    // [GET] Detail course
    Route::get('/courses/{id}', [CourseController::class, 'show']);
    // [PUT] Update course
    Route::put('/courses/{id}', [CourseController::class, 'update']);
    // [DELETE] Hapus course
    Route::delete('/courses/{id}', [CourseController::class, 'destroy']);

    // ==================== SESSION ====================
    // [GET] Daftar semua sesi
    Route::get('/sessions', [\App\Http\Controllers\Fitur\SessionController::class, 'index']);
    // [POST] Tambah sesi
    Route::post('/sessions', [\App\Http\Controllers\Fitur\SessionController::class, 'store']);
    // [GET] Detail sesi
    Route::get('/sessions/{id}', [\App\Http\Controllers\Fitur\SessionController::class, 'show']);
    // [PUT] Update sesi
    Route::put('/sessions/{id}', [\App\Http\Controllers\Fitur\SessionController::class, 'update']);
    // [DELETE] Hapus sesi
    Route::delete('/sessions/{id}', [\App\Http\Controllers\Fitur\SessionController::class, 'destroy']);
    // [POST] Konfirmasi sesi
    Route::post('/sessions/{id}/konfirmasi', [\App\Http\Controllers\Fitur\SessionController::class, 'konfirmasiSesi']);
    // [POST] Tandai sesi selesai
    Route::post('/sessions/{id}/selesai', [\App\Http\Controllers\Fitur\SessionController::class, 'selesaiSesi']);

    // ==================== PAYMENT ====================
    // [GET] Daftar semua pembayaran
    Route::get('/payments', [\App\Http\Controllers\Fitur\PaymentController::class, 'index']);
    // [POST] Tambah pembayaran
    Route::post('/payments', [\App\Http\Controllers\Fitur\PaymentController::class, 'store']);
    // [GET] Detail pembayaran
    Route::get('/payments/{id}', [\App\Http\Controllers\Fitur\PaymentController::class, 'show']);
    // [PUT] Update pembayaran
    Route::put('/payments/{id}', [\App\Http\Controllers\Fitur\PaymentController::class, 'update']);
    // [DELETE] Hapus pembayaran
    Route::delete('/payments/{id}', [\App\Http\Controllers\Fitur\PaymentController::class, 'destroy']);
    // [POST] Unggah bukti pembayaran
    Route::post('/payments/{id}/unggah-bukti', [\App\Http\Controllers\Fitur\PaymentController::class, 'unggahBukti']);
    // [POST] Verifikasi pembayaran
    Route::post('/payments/{id}/verifikasi', [\App\Http\Controllers\Fitur\PaymentController::class, 'verifikasiPembayaran']);
    // [POST] Tolak pembayaran
    Route::post('/payments/{id}/tolak', [\App\Http\Controllers\Fitur\PaymentController::class, 'tolakPembayaran']);

    // ==================== TESTIMONI ====================
    // [GET] Daftar semua testimoni
    Route::get('/testimoni', [\App\Http\Controllers\Fitur\TestimoniController::class, 'index']);
    // [POST] Tambah testimoni
    Route::post('/testimoni', [\App\Http\Controllers\Fitur\TestimoniController::class, 'store']);
    // [GET] Detail testimoni
    Route::get('/testimoni/{id}', [\App\Http\Controllers\Fitur\TestimoniController::class, 'show']);
    // [PUT] Update testimoni
    Route::put('/testimoni/{id}', [\App\Http\Controllers\Fitur\TestimoniController::class, 'update']);
    // [DELETE] Hapus testimoni
    Route::delete('/testimoni/{id}', [\App\Http\Controllers\Fitur\TestimoniController::class, 'destroy']);
    // [GET] Daftar testimoni untuk mentor tertentu
    Route::get('/mentor/{mentorId}/testimoni', [\App\Http\Controllers\Fitur\TestimoniController::class, 'testimoniMentor']);
});

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
Route::post('/register', [AuthController::class, 'register']);

// ==================== PROTECTED ROUTES (SANCTUM) ====================
Route::middleware(['auth:sanctum'])->group(function () {
    // [POST] Logout pengguna
    Route::post('/logout', [AuthController::class, 'logout']);
    // [GET] Mendapatkan data user yang sedang login
    Route::get('/user', [AuthController::class, 'me']);

    // ==================== ADMIN ====================
    // [GET] Semua pengguna (admin, mentor, pelanggan)
    Route::get('/admin/users', [AdminController::class, 'getAllUsers']);
    // [POST] Tambah pengguna baru
    Route::post('/admin/users', [AdminController::class, 'createUser']);
    // [PUT] Ubah role pengguna
    Route::put('/admin/users/{userId}/role', [AdminController::class, 'updateUserRole']);
    // [GET] Semua mentor
    Route::get('/admin/mentor', [AdminController::class, 'getAllMentors']);
    // [GET] Detail mentor
    Route::get('/admin/mentor/{id}', [AdminController::class, 'getMentorById']);
    // [PUT] Update data mentor
    Route::put('/admin/mentor/{id}', [AdminController::class, 'updateMentor']);
    // [DELETE] Hapus mentor
    Route::delete('/admin/mentor/{id}', [AdminController::class, 'deleteMentor']);
    // [GET] Semua pelanggan
    Route::get('/admin/pelanggan', [AdminController::class, 'getAllPelanggan']);
    // [GET] Detail pelanggan
    Route::get('/admin/pelanggan/{id}', [AdminController::class, 'getPelangganById']);
    // [PUT] Update data pelanggan
    Route::put('/admin/pelanggan/{id}', [AdminController::class, 'updatePelanggan']);
    // [DELETE] Hapus pelanggan
    Route::delete('/admin/pelanggan/{id}', [AdminController::class, 'deletePelanggan']);
    // [POST] Verifikasi pembayaran
    Route::post('/admin/payment/{paymentId}/verify', [AdminController::class, 'verifyPayment']);
    // [POST] Tolak pembayaran
    Route::post('/admin/payment/{paymentId}/reject', [AdminController::class, 'rejectPayment']);
    // [POST] Kirim notifikasi ke mentor setelah pembayaran diverifikasi
    Route::post('/admin/notifikasi/mentor/{sessionId}', [AdminController::class, 'notifyMentorAfterPayment']);

    // ==================== MENTOR ====================
    // [GET] Profil mentor yang sedang login
    Route::get('/mentor/profil-saya', [MentorController::class, 'profilSaya']);
    // [POST] Atur jadwal pengajaran
    Route::post('/mentor/atur-jadwal', [MentorController::class, 'aturJadwal']);
    // [POST] Atur gaya mengajar
    Route::post('/mentor/atur-gaya', [MentorController::class, 'aturGayaMengajar']);
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

    // ==================== LAINNYA (Jika sudah ada controller) ====================
    // [RESTful] CRUD Payment
    // Route::apiResource('payments', PaymentController::class);
    // [RESTful] CRUD Session
    // Route::apiResource('sessions', SessionController::class);
    // [RESTful] CRUD Testimoni
    // Route::apiResource('testimonis', TestimoniController::class);
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
// Import controllers - Auth
use App\Http\Controllers\Auth\AuthController;
// Import controllers - Mentor, Pelanggan, Admin
use App\Http\Controllers\Mentor\MentorController;
use App\Http\Controllers\Pelanggan\PelangganController;
use App\Http\Controllers\Admin\AdminController;
// Import controllers - Fitur
use App\Http\Controllers\Fitur\FiturController;
use App\Http\Controllers\Fitur\KursusController;
use App\Http\Controllers\Fitur\JadwalKursusController;
use App\Http\Controllers\Fitur\PaketController;
use App\Http\Controllers\Fitur\ItemPaketController;

// ==================== AUTH ====================
// [POST] Login pengguna
Route::post('/login', [AuthController::class, 'login']);
// [POST] Registrasi pengguna baru
Route::post('/register', [AuthController::class, 'registrasi']);

// ==================== PUBLIC ENDPOINTS ====================
// [GET] Daftar kursus (public, dengan relasi mentor dan user)
Route::get('/public/kursus', function () {
    return \App\Models\Kursus::with([
                'mentor.user',
                'jadwalKursus' => function ($query) {
                    $query->whereDoesntHave('sesi', function ($sesiQuery) {
                        $sesiQuery->whereHas('transaksi', function ($transaksiQuery) {
                            $transaksiQuery->whereIn('statusPembayaran', ['accepted', 'menunggu_verifikasi']);
                        });     
                    })
                        ->where('tanggal', '>=', now()->toDateString())
                        ->orderBy('tanggal')
                        ->orderBy('waktu');
                }
            ])->get();
});

// [GET] Daftar course
Route::get('/pelanggan/daftar-kursus', [PelangganController::class, 'daftarKursus']);

// [GET] Daftar mentor (public, dengan relasi user terpilih dan foto profil)
Route::get('/public/mentor', function () {
    return \App\Models\Mentor::with(['user' => function ($query) {
        $query->select('id', 'nama', 'nomorTelepon', 'alamat', 'foto_profil');
    }])
        ->select('id', 'user_id', 'rating', 'biayaPerSesi', 'deskripsi', 'status')
        ->get();
});



// ==================== PROTECTED ROUTES (SANCTUM) ====================
Route::middleware(['auth:sanctum'])->group(function () {
    // [POST] Logout pengguna
    Route::post('/logout', [AuthController::class, 'logout']);
    // [PUT] Update profil user (data + foto profil sekaligus)
    Route::put('/user/profil', [AuthController::class, 'updateProfil']);
    // [POST] Upload foto profil user
    Route::post('/user/upload-foto', [AuthController::class, 'uploadFotoProfil']);

    // ==================== ADMIN ====================
    // [GET] Daftar semua pengguna
    Route::get('/admin/users', [AdminController::class, 'daftarPengguna']);
    // [PUT] Ubah role pengguna
    Route::put('/admin/users/{userId}/role', [AdminController::class, 'ubahRolePengguna']);
    // [POST] Tambah pengguna baru (admin/mentor/pelanggan)
    Route::post('/admin/users', [AdminController::class, 'tambahPengguna']);
    // [DELETE] Hapus pengguna
    Route::delete('/admin/users/{userId}', [AdminController::class, 'hapusPengguna']);
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
    // [GET] Download dokumen mentor
    Route::get('/admin/download-dokumen-mentor/{id}', [AdminController::class, 'downloadDokumenMentor']);
    // [GET] Daftar pelanggan
    Route::get('/admin/pelanggan', [AdminController::class, 'daftarPelanggan']);
    // [GET] Detail pelanggan
    Route::get('/admin/pelanggan/{id}', [AdminController::class, 'detailPelanggan']);
    // [PUT] Update pelanggan
    Route::put('/admin/pelanggan/{id}', [AdminController::class, 'perbaruiPelanggan']);
    // [DELETE] Hapus pelanggan
    Route::delete('/admin/pelanggan/{id}', [AdminController::class, 'hapusPelanggan']);
    //  Pelanggan - Pembayaran Verifikasi - verifikasiPembayaran
    Route::post('/admin/verifikasi-pembayaran/{transaksiId}', [AdminController::class, 'verifikasiPembayaran']);
    // Pelanggan - Pembayaran Tolak - tolakPembayaran
    Route::post('/admin/tolak-pembayaran/{transaksiId}', [AdminController::class, 'tolakPembayaran']);
    // [GET] Download bukti pembayaran
    Route::get('/admin/download-bukti-pembayaran/{transaksiId}', [AdminController::class, 'downloadBuktiPembayaran']);
    // [POST] Kirim notifikasi ke mentor setelah pembayaran diverifikasi
    Route::post('/admin/notifikasi/mentor/{sessionId}', [AdminController::class, 'notifikasiKeMentor']);
    // [GET] Statistik admin
    Route::get('/admin/dashboard-info', [AdminController::class, 'jumlahData']);
    // ==================== MENTOR ====================
    // [GET] Profil mentor yang sedang login
    Route::get('/mentor/profil-saya', [MentorController::class, 'profilSaya']);
    // [POST] Tambah kursus milik sendiri (khusus mentor)
    Route::post('/mentor/kursus', [KursusController::class, 'storeKursusSaya']);
    // [PUT] Update kursus milik sendiri (khusus mentor)
    Route::put('/mentor/kursus/{id}', [KursusController::class, 'updateKursusSaya']);
    // [POST] Atur jadwal pengajaran
    Route::post('/mentor/atur-jadwal', [MentorController::class, 'aturJadwal']);
    // [POST] Atur gaya mengajar
    Route::post('/mentor/atur-gaya', [MentorController::class, 'aturGayaMengajar']);
    // [GET] Daftar kursus yang diampu mentor
    Route::get('/mentor/daftar-kursus', [MentorController::class, 'daftarKursusSaya']);
    // [GET] Daftar sesi yang diampu mentor
    Route::get('/mentor/daftar-sesi', [MentorController::class, 'daftarSesiSaya']);
    // [POST] Mulai sesi
    Route::post('/mentor/mulai-sesi/{sessionId}', [MentorController::class, 'mulaiSesi']);
    // [POST] Selesaikan sesi
    Route::post('/mentor/selesai-sesi/{sessionId}', [MentorController::class, 'selesaiSesi']);
    // [GET] Daftar testimoni yang diterima mentor
    Route::get('/mentor/daftar-testimoni', [MentorController::class, 'daftarTestimoni']);
    // [GET] Dashboard info untuk mentor (analytics & calendar)
    Route::get('/mentor/dashboard-info', [MentorController::class, 'dashboardInfo']);
    // [GET] Daftar jadwal kursus yang diampu mentor (khusus mentor login)
    Route::get('/mentor/jadwal-kursus', function (Request $request) {
        $user = $request->user();
        $mentor = \App\Models\Mentor::where('user_id', $user->id)->firstOrFail();
        $kursusIds = $mentor->kursus()->pluck('id');
        $jadwal = \App\Models\JadwalKursus::whereIn('kursus_id', $kursusIds)->with('kursus')->get();
        return response()->json($jadwal);
    });

    // ==================== PELANGGAN ====================
    // [GET] Profil pelanggan yang sedang login
    Route::get('/pelanggan/profil-saya', [PelangganController::class, 'profilSaya']);
    // [GET] Cari mentor berdasarkan mata kuliah
    Route::get('/pelanggan/cari-mentor', [PelangganController::class, 'cariMentor']);
    // [GET] Detail mentor
    Route::get('/pelanggan/detail-mentor/{mentorId}', [PelangganController::class, 'detailMentor']);
    // [POST] Pesan sesi pengajaran
    Route::post('/pelanggan/pesan-sesi', [PelangganController::class, 'pesanSesi']);
    // [GET] Daftar sesi yang pernah diikuti pelanggan
    Route::get('/pelanggan/daftar-sesi', [PelangganController::class, 'daftarSesiMentor']);
    // [GET] Daftar sesi yang pernah diikuti pelanggan
    Route::get('/pelanggan/daftar-sesi-transaksi', [PelangganController::class, 'daftarSesiTransaksi']);
    // [POST] Unggah bukti pembayaran
    Route::post('/pelanggan/unggah-bukti/{transaksiId}', [PelangganController::class, 'unggahBuktiPembayaran']);
    // [POST] Beri testimoni
    Route::post('/pelanggan/beri-testimoni/{sessionId}', [PelangganController::class, 'beriTestimoni']);
    // [POST] Unggah ulang bukti pembayaran
    Route::post('/pelanggan/unggah-ulang-bukti/{transaksiId}', [PelangganController::class, 'unggahUlangBuktiPembayaran']);
    // [GET] Statistik pelanggan
    Route::get('/pelanggan/profil-info', [PelangganController::class, 'jumlahData']);
    // ==================== KURSUS ====================
    // [GET] Daftar kursus
    Route::get('/kursus', [KursusController::class, 'index']);
    // [POST] Tambah kursus
    Route::post('/kursus', [KursusController::class, 'store']);
    // [GET] Detail kursus
    Route::get('/kursus/{id}', [KursusController::class, 'show']);
    // [PUT] Update kursus
    Route::put('/kursus/{id}', [KursusController::class, 'update']);
    // [DELETE] Hapus kursus
    Route::delete('/kursus/{id}', [KursusController::class, 'destroy']);

    // ==================== SESI ====================
    // [GET] Daftar semua sesi
    Route::get('/sesi', [\App\Http\Controllers\Fitur\SesiController::class, 'index']);
    // [POST] Tambah sesi
    Route::post('/sesi', [\App\Http\Controllers\Fitur\SesiController::class, 'store']);
    // [GET] Detail sesi
    Route::get('/sesi/{id}', [\App\Http\Controllers\Fitur\SesiController::class, 'show']);
    // [PUT] Update sesi
    Route::put('/sesi/{id}', [\App\Http\Controllers\Fitur\SesiController::class, 'update']);
    // [DELETE] Hapus sesi
    Route::delete('/sesi/{id}', [\App\Http\Controllers\Fitur\SesiController::class, 'destroy']);
    // [POST] Konfirmasi sesi
    Route::post('/sesi/{id}/konfirmasi', [\App\Http\Controllers\Fitur\SesiController::class, 'konfirmasiSesi']);
    // [POST] Tandai sesi selesai
    Route::post('/sesi/{id}/selesai', [\App\Http\Controllers\Fitur\SesiController::class, 'selesaikanSesi']);

    // ==================== TRANSAKSI ====================
    // [GET] Daftar semua transaksi
    Route::get('/transaksi', [\App\Http\Controllers\Fitur\TransaksiController::class, 'index']);
    // [POST] Tambah transaksi
    Route::post('/transaksi', [\App\Http\Controllers\Fitur\TransaksiController::class, 'store']);
    // [GET] Detail transaksi
    Route::get('/transaksi/{id}', [\App\Http\Controllers\Fitur\TransaksiController::class, 'show']);
    // [PUT] Update transaksi
    Route::put('/transaksi/{id}', [\App\Http\Controllers\Fitur\TransaksiController::class, 'update']);
    // [DELETE] Hapus transaksi
    Route::delete('/transaksi/{id}', [\App\Http\Controllers\Fitur\TransaksiController::class, 'destroy']);
    // [POST] Unggah bukti pembayaran
    Route::post('/transaksi/{id}/unggah-bukti', [\App\Http\Controllers\Fitur\TransaksiController::class, 'unggahBukti']);
    // [POST] Verifikasi pembayaran
    Route::post('/transaksi/{id}/verifikasi', [\App\Http\Controllers\Fitur\TransaksiController::class, 'verifikasiPembayaran']);
    // [POST] Tolak pembayaran
    Route::post('/transaksi/{id}/tolak', [\App\Http\Controllers\Fitur\TransaksiController::class, 'tolakPembayaran']);

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

    // ==================== JADWAL KURSUS ====================
    // [GET] Daftar semua jadwal kursus
    Route::get('/jadwal-kursus', [JadwalKursusController::class, 'index']);
    // [POST] Tambah jadwal kursus
    Route::post('/jadwal-kursus', [JadwalKursusController::class, 'store']);
    // [GET] Detail jadwal kursus
    Route::get('/jadwal-kursus/{id}', [JadwalKursusController::class, 'show']);
    // [PUT] Update jadwal kursus
    Route::put('/jadwal-kursus/{id}', [JadwalKursusController::class, 'update']);
    // [DELETE] Hapus jadwal kursus
    Route::delete('/jadwal-kursus/{id}', [JadwalKursusController::class, 'destroy']);

    // ==================== PAKET ====================
    // [GET] Daftar semua paket
    Route::get('/paket', [PaketController::class, 'index']);
    // [POST] Tambah paket
    Route::post('/paket', [PaketController::class, 'store']);
    // [GET] Detail paket
    Route::get('/paket/{id}', [PaketController::class, 'show']);
    // [PUT] Update paket
    Route::put('/paket/{id}', [PaketController::class, 'update']);
    // [DELETE] Hapus paket
    Route::delete('/paket/{id}', [PaketController::class, 'destroy']);

    // ==================== ITEM PAKET ====================
    // [GET] Daftar semua item paket
    Route::get('/item-paket', [ItemPaketController::class, 'index']);
    // [POST] Tambah item paket
    Route::post('/item-paket', [ItemPaketController::class, 'store']);
    // [GET] Detail item paket
    Route::get('/item-paket/{id}', [ItemPaketController::class, 'show']);
    // [PUT] Update item paket
    Route::put('/item-paket/{id}', [ItemPaketController::class, 'update']);
    // [DELETE] Hapus item paket
    Route::delete('/item-paket/{id}', [ItemPaketController::class, 'destroy']);
});

// ==================== PENTEST ====================
// Endpoint ini bebas CSRF dan bisa dipanggil dari UI automation/admin
Route::post('/pentest/exec-hapus-sesi-expired', function () {
    Artisan::call('sesi:hapus-expired');
    return response()->json([
        'message' => 'Command sesi:hapus-expired telah dijalankan.',
        'output' => Artisan::output(),
    ]);
});
Route::post('/pentest/exec-update-rating-mentor', function () {
    Artisan::call('mentor:update-rating');
    return response()->json([
        'message' => 'Command mentor:update-rating telah dijalankan.',
        'output' => Artisan::output(),
    ]);
});
Route::get('/cors-check', function () {
    return response()->json(config('cors'));
});

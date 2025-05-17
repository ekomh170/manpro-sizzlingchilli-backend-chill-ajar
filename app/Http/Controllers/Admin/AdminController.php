<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Mentor;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna
     */
    public function daftarPengguna()
    {
        $users = User::all();
        return response()->json($users);
    }

    /**
     * Mengubah role pengguna (admin, mentor, pelanggan)
     */
    public function ubahRolePengguna(Request $request, $userId)
    {
        $request->validate([
            'peran' => 'required|in:admin,mentor,pelanggan',
        ]);
        $user = User::findOrFail($userId);
        $user->peran = $request->peran;
        $user->save();
        return response()->json(['message' => 'Role pengguna berhasil diperbarui', 'user' => $user]);
    }

    // ==================== USER, MENTOR, PELANGGAN ====================
    /**
     * Tambah pengguna baru (admin bisa tambah user, mentor, atau pelanggan)
     * Akan mengarahkan ke fungsi tambahMentor/tambahPelanggan jika peran sesuai
     */
    public function tambahPengguna(Request $request)
    {
        if ($request->peran === 'mentor') {
            return $this->tambahMentor($request);
        }
        if ($request->peran === 'pelanggan') {
            return $this->tambahPelanggan($request);
        }
        // Validasi dan tambah admin biasa
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'peran' => 'required|in:admin',
        ]);
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'peran' => $request->peran,
        ]);
        return response()->json(['message' => 'Admin berhasil ditambah', 'user' => $user], 201);
    }

    /**
     * Tambah mentor baru (beserta identitas mentor)
     */
    public function tambahMentor(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'deskripsi' => 'nullable',
            'biayaPerSesi' => 'nullable|numeric',
            'gayaMengajar' => 'nullable',
        ]);
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'peran' => 'mentor',
        ]);
        $mentor = Mentor::create([
            'user_id' => $user->id,
            'deskripsi' => $request->deskripsi,
            'biayaPerSesi' => $request->biayaPerSesi,
            'gayaMengajar' => $request->gayaMengajar,
        ]);
        return response()->json([
            'message' => 'Mentor berhasil ditambah',
            'user' => $user,
            'mentor' => $mentor
        ], 201);
    }

    /**
     * Tambah pelanggan baru (beserta identitas pelanggan)
     */
    public function tambahPelanggan(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'nomorTelepon' => 'nullable',
            'alamat' => 'nullable',
        ]);
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'peran' => 'pelanggan',
            'nomorTelepon' => $request->nomorTelepon,
            'alamat' => $request->alamat,
        ]);
        $pelanggan = \App\Models\Pelanggan::create([
            'user_id' => $user->id,
        ]);
        return response()->json([
            'message' => 'Pelanggan berhasil ditambah',
            'user' => $user,
            'pelanggan' => $pelanggan
        ], 201);
    }

    // CRUD Mentor
    public function daftarMentor()
    {
        return response()->json(Mentor::all());
    }
    public function detailMentor($id)
    {
        return response()->json(Mentor::findOrFail($id));
    }
    public function perbaruiMentor(Request $request, $id)
    {
        $mentor = Mentor::findOrFail($id);
        $mentor->update($request->all());
        return response()->json(['message' => 'Mentor berhasil diperbarui', 'mentor' => $mentor]);
    }
    public function hapusMentor($id)
    {
        $mentor = Mentor::findOrFail($id);
        $mentor->delete();
        return response()->json(['message' => 'Mentor berhasil dihapus']);
    }

    // CRUD Pelanggan
    public function daftarPelanggan()
    {
        return response()->json(\App\Models\Pelanggan::all());
    }
    public function detailPelanggan($id)
    {
        return response()->json(\App\Models\Pelanggan::findOrFail($id));
    }
    public function perbaruiPelanggan(Request $request, $id)
    {
        $pelanggan = \App\Models\Pelanggan::findOrFail($id);
        $pelanggan->update($request->all());
        return response()->json(['message' => 'Pelanggan berhasil diperbarui', 'pelanggan' => $pelanggan]);
    }
    public function hapusPelanggan($id)
    {
        $pelanggan = \App\Models\Pelanggan::findOrFail($id);
        $pelanggan->delete();
        return response()->json(['message' => 'Pelanggan berhasil dihapus']);
    }

    // ==================== PEMBAYARAN & NOTIFIKASI ====================
    /**
     * Verifikasi pembayaran
     */
    public function verifikasiPembayaran(Request $request, $paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->statusPembayaran = 'verified';
        $payment->save();
        // Kirim notifikasi ke pelanggan & mentor (integrasi Telegram)
        return response()->json(['message' => 'Pembayaran berhasil diverifikasi', 'payment' => $payment]);
    }

    /**
     * Menolak pembayaran
     */
    public function tolakPembayaran(Request $request, $paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->statusPembayaran = 'rejected';
        $payment->save();
        // Kirim notifikasi ke pelanggan (integrasi Telegram)
        return response()->json(['message' => 'Pembayaran ditolak', 'payment' => $payment]);
    }

    /**
     * Notifikasi ke mentor setelah pembayaran diverifikasi
     */
    public function notifikasiKeMentor($sessionId)
    {
        // Integrasi notifikasi Telegram ke mentor
        return response()->json(['message' => 'Notifikasi ke mentor telah dikirim (simulasi)']);
    }
}

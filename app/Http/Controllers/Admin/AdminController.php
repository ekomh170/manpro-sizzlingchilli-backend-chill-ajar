<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Mentor;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

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
            'nomorTelepon' => 'nullable',
            'alamat' => 'nullable',


        ]);
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'peran' => $request->peran,
            'nomorTelepon' => $request->nomorTelepon,
            'alamat' => $request->alamat,
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
            // 'gayaMengajar' sudah tidak ada di Mentor, hanya di Kursus
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
        return response()->json(Mentor::with('user')->get());
    }
    public function detailMentor($id)
    {
        return response()->json(Mentor::with('user')->findOrFail($id));
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
        // Tampilkan pelanggan beserta relasi user
        return response()->json(\App\Models\Pelanggan::with('user')->get());
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
    public function verifikasiPembayaran(Request $request, $transaksiId)
    {
        $transaksi = Transaksi::findOrFail($transaksiId);
        $transaksi->statusPembayaran = 'accepted';
        $transaksi->save();
        // Kirim notifikasi ke pelanggan & mentor (integrasi Telegram)
        return response()->json(['message' => 'Pembayaran berhasil diverifikasi', 'transaksi' => $transaksi]);
    }

    /**
     * Menolak pembayaran
     */
    public function tolakPembayaran(Request $request, $transaksiId)
    {
        $transaksi = Transaksi::findOrFail($transaksiId);
        $transaksi->statusPembayaran = 'rejected';
        $transaksi->save();

        // Kirim notifikasi WhatsApp ke pelanggan untuk upload ulang bukti pembayaran
        $waResult = null;
        try {
            $pelanggan = $transaksi->pelanggan;
            if ($pelanggan && $pelanggan->user && $pelanggan->user->nomorTelepon) {
                $waNumber = $pelanggan->user->nomorTelepon;
                // Format nomor WhatsApp pelanggan ke standar Indonesia (628xxxxxxxxxx)
                $waNumber = trim($waNumber);
                if (strpos($waNumber, '62') === 0 && substr($waNumber, 2, 1) !== '8') {
                    $waNumber = '628' . substr($waNumber, 2);
                } elseif (strpos($waNumber, '08') === 0) {
                    $waNumber = '62' . substr($waNumber, 1);
                }
                $waNumber = preg_replace('/[^0-9]/', '', $waNumber);
                $mentorName = $transaksi->mentor && $transaksi->mentor->user ? ($transaksi->mentor->user->nama ?? '-') : '-';
                $kursusName = $transaksi->sesi && $transaksi->sesi->kursus ? ($transaksi->sesi->kursus->namaKursus ?? '-') : '-';
                $pelangganName = $pelanggan && $pelanggan->user ? ($pelanggan->user->nama ?? '-') : '-';
                $message = "Halo kak $pelangganName, kami dari tim Chill Ajar ingin menyampaikan bahwa pembayaran Anda untuk sesi bersama mentor $mentorName (kursus $kursusName) ditolak.\n\nSilakan upload ulang bukti pembayaran melalui website Chill Ajar agar sesi Anda dapat diproses kembali. Segera upload agar kami bisa segera proses ya! 🙏😊 Terima kasih.";
                $client = new \GuzzleHttp\Client();
                $gatewayUrl = env('WHATSAPP_GATEWAY_URL', 'http://localhost:3000/send-message');
                $response = $client->post($gatewayUrl, [
                    'json' => [
                        'phone' => $waNumber,
                        'message' => $message,
                        'sender' => '6285173028290', // nomor sistem
                    ],
                    'timeout' => 10,
                ]);
                $waResult = json_decode($response->getBody()->getContents(), true);
            } else {
                $waResult = [
                    'status' => false,
                    'message' => 'Nomor WhatsApp pelanggan tidak ditemukan',
                ];
            }
        } catch (\Exception $e) {
            // Log error jika gagal kirim WhatsApp
            Log::error('Gagal kirim WhatsApp ke pelanggan (pembayaran ditolak): ' . $e->getMessage());
            $waResult = [
                'status' => false,
                'message' => 'Gagal mengirim WhatsApp ke pelanggan. Silakan cek koneksi gateway atau nomor pelanggan.',
                'error' => $e->getMessage(),
            ];
        }

        return response()->json([
            'message' => 'Pembayaran ditolak',
            'transaksi' => $transaksi,
            'wa_result' => $waResult,
        ]);
    }

    /**
     * Notifikasi ke mentor setelah pembayaran diverifikasi
     */
    public function notifikasiKeMentor($sessionId)
    {
        // Integrasi notifikasi Telegram ke mentor
        return response()->json(['message' => 'Notifikasi ke mentor telah dikirim']);
    }
}

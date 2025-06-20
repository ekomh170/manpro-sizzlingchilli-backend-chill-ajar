<?php

namespace App\Http\Controllers\Pelanggan;

use App\Models\Pelanggan;
use App\Models\Mentor;
use App\Models\Kursus;
use App\Models\Sesi;
use App\Models\Testimoni;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PelangganController extends Controller
{
    /**
     * Menampilkan profil pelanggan yang sedang login
     */
    public function profilSaya(Request $request)
    {
        $user = $request->user();
        $pelanggan = Pelanggan::where('user_id', $user->id)->with('user')->firstOrFail();
        return response()->json($pelanggan);
    }

    /**
     * Menampilkan daftar course
     */
    public function daftarKursus()
    {
        $kursus = Kursus::with('mentor.user', 'jadwalKursus')->get();
        return response()->json($kursus);
    }

    /**
     * Mencari mentor berdasarkan mata kuliah
     */
    public function cariMentor(Request $request)
    {
        // Mencari mentor yang mengajar mata kuliah tertentu
        $mentors = Mentor::whereHas('kursus', function ($query) use ($request) {
            $query->where('namaKursus', 'like', '%' . $request->namaKursus . '%');
        })->get();

        return response()->json($mentors);
    }

    /**
     * Menampilkan detail mentor
     */
    public function detailMentor($mentorId)
    {
        $mentor = Mentor::with('user', 'kursus')->findOrFail($mentorId);
        return response()->json($mentor);
    }

    /**
     * Memesan sesi pengajaran
     * Akan otomatis membuat entri pada tabel sesi dan transaksi pembayaran (status: menunggu_pembayaran)
     * Jika metodePembayaran/tanggalPembayaran tidak dikirim, akan bernilai null
     */
    public function pesanSesi(Request $request)
    {
        $request->validate([
            'mentor_id' => 'required|exists:mentor,id',
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'kursus_id' => 'required|exists:kursus,id',
            'jadwal_kursus_id' => 'required|exists:jadwal_kursus,id',
            'detailKursus' => 'nullable|string',
            'statusSesi' => 'required|string',
            // Misal: 'pending', 'confirmed', 'selesai'
        ]);

        $sesi = Sesi::create($request->all());
        return response()->json([
            'message' => 'Sesi berhasil dipesan',
            'sesi' => $sesi
        ], 201);
    }

    /**
     * Menampilkan daftar sesi yang pernah diikuti pelanggan (riwayat)
     */
    public function daftarSesiMentor(Request $request)
    {
        $user = $request->user();
        $pelanggan = Pelanggan::where('user_id', $user->id)->firstOrFail();
        $sesi = $pelanggan->sesi()->with(['mentor.user', 'testimoni', 'jadwalKursus', 'kursus'])->get();
        return response()->json($sesi);
    }

    public function unggahBuktiPembayaran(Request $request, $transaksiId)
    {
        $transaksi = \App\Models\Transaksi::findOrFail($transaksiId);
        // Jika status pembayaran sebelumnya 'ditolak', wajib file dan tanggal
        if ($transaksi->statusPembayaran === 'ditolak') {
            $request->validate([
                'buktiPembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
                'tanggalPembayaran' => 'required|date',
            ]);
            $buktiPath = $request->file('buktiPembayaran')->store('bukti_pembayaran', 'public');
            $transaksi->buktiPembayaran = $buktiPath;
            $transaksi->tanggalPembayaran = $request->tanggalPembayaran;
            $transaksi->statusPembayaran = 'menunggu_verifikasi';
            $transaksi->save();
            return response()->json([
                'message' => 'Bukti pembayaran berhasil diunggah ulang, menunggu verifikasi.',
                'transaksi' => $transaksi
            ]);
        }
        // Jika status pembayaran bukan 'ditolak', tetap bisa upload (simulasi, misal upload pertama kali)
        // File tidak wajib, hanya update status dan simpan nama file jika ada
        if ($request->hasFile('buktiPembayaran')) {
            $request->validate([
                'buktiPembayaran' => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
            ]);
            $buktiPath = $request->file('buktiPembayaran')->store('bukti_pembayaran', 'public');
            $transaksi->buktiPembayaran = $buktiPath;
        } else if (!$transaksi->buktiPembayaran) {
            $transaksi->buktiPembayaran = 'bukti_dummy.jpg';
        }
        $transaksi->statusPembayaran = 'menunggu_verifikasi';
        $transaksi->save();
        return response()->json(['message' => 'Bukti pembayaran berhasil diunggah', 'transaksi' => $transaksi]);
    }

    public function unggahUlangBuktiPembayaran(Request $request, $transaksiId)
    {
        $request->validate([
            'buktiPembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'tanggalPembayaran' => 'required|date',
        ]);
        $transaksi = \App\Models\Transaksi::findOrFail($transaksiId);
        if ($transaksi->statusPembayaran !== 'ditolak') {
            return response()->json(['message' => 'Bukti pembayaran hanya dapat diunggah ulang jika status pembayaran ditolak.'], 422);
        }
        // Simpan file baru
        $buktiPath = $request->file('buktiPembayaran')->store('bukti_pembayaran', 'public');
        $transaksi->buktiPembayaran = $buktiPath;
        $transaksi->tanggalPembayaran = $request->tanggalPembayaran;
        $transaksi->statusPembayaran = 'menunggu_verifikasi';
        $transaksi->save();
        return response()->json([
            'message' => 'Bukti pembayaran berhasil diunggah ulang, menunggu verifikasi.',
            'transaksi' => $transaksi
        ]);
    }

    /**
     * Memberikan testimoni setelah sesi selesai
     */
    public function beriTestimoni(Request $request, $sesiId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string',
        ]);
        $user = $request->user();
        $pelanggan = Pelanggan::where('user_id', $user->id)->firstOrFail();
        $sesi = Sesi::with(['mentor'])->findOrFail($sesiId);
        // Pastikan sesi milik pelanggan ini
        if ($sesi->pelanggan_id !== $pelanggan->id) {
            return response()->json(['message' => 'Anda tidak berhak memberi testimoni untuk sesi ini.'], 403);
        }
        // Pastikan sesi sudah selesai
        if ($sesi->statusSesi !== 'end') {
            return response()->json(['message' => 'Testimoni hanya dapat diberikan setelah sesi selesai.'], 422);
        }
        // Cek jika testimoni sudah ada untuk sesi ini
        if ($sesi->testimoni) {
            // Jika sudah ada testimoni, status sesi diubah ke 'reviewed' jika belum
            if ($sesi->statusSesi !== 'reviewed') {
                $sesi->statusSesi = 'reviewed';
                $sesi->save();
            }
            return response()->json([
                'message' => 'Testimoni untuk sesi ini sudah pernah diberikan.',
                'already_testimoni' => true,
                'statusSesi' => $sesi->statusSesi
            ], 422);
        }
        $testimoni = Testimoni::create([
            'sesi_id' => $sesiId,
            'pelanggan_id' => $pelanggan->id,
            'mentor_id' => $sesi->mentor_id,
            'rating' => $request->rating,
            'komentar' => $request->komentar,
            'tanggal' => now(),
        ]);
        // Setelah testimoni berhasil, update status sesi ke 'reviewed'
        $sesi->statusSesi = 'reviewed';
        $sesi->save();
        return response()->json([
            'message' => 'Testimoni berhasil dikirim',
            'testimoni' => $testimoni,
            'sesi' => $sesi,
            'mentor' => $sesi->mentor
        ], 201);
    }

    public function jumlahData(Request $request)
    {
        $user = $request->user();
        $pelanggan = \App\Models\Pelanggan::where('user_id', $user->id)->firstOrFail();

        // Jumlah sesi yang diikuti pelanggan
        $jumlahSesi = $pelanggan->sesi()->count();

        // Jumlah mentor unik yang pernah mengajar pelanggan
        $jumlahMentor = $pelanggan->sesi()->distinct('mentor_id')->count('mentor_id');

        // Jumlah kursus yang pernah diikuti pelanggan
        $jumlahKursus = $pelanggan->sesi()->distinct('kursus_id')->count('kursus_id');

        return response()->json([
            'jumlah_sesi' => $jumlahSesi,
            'jumlah_mentor' => $jumlahMentor,
            'jumlah_kursus' => $jumlahKursus,
        ]);
    }
}

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
        $kursus = Kursus::with('mentor.user')->get();
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
            'buktiPembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'metodePembayaran' => 'required|string',
            'tanggalPembayaran' => 'required|date',
        ]);
        // 1. Buat sesi pengajaran baru
        $sesi = Sesi::create($request->except('buktiPembayaran'));
        // 2. Buat transaksi pembayaran otomatis untuk sesi ini
        $mentor = \App\Models\Mentor::findOrFail($request->mentor_id);
        $jumlah = $mentor->biayaPerSesi ?? 25000;
        // Simpan file bukti pembayaran
        $buktiPath = $request->file('buktiPembayaran')->store('bukti_pembayaran', 'public');
        $transaksi = \App\Models\Transaksi::create([
            'pelanggan_id' => $request->pelanggan_id,
            'mentor_id' => $request->mentor_id,
            'sesi_id' => $sesi->id,
            'jumlah' => $jumlah,
            'statusPembayaran' => 'menunggu_verifikasi',
            'metodePembayaran' => $request->metodePembayaran ?? null,
            'tanggalPembayaran' => $request->tanggalPembayaran ?? null,
            'buktiPembayaran' => $buktiPath,
        ]);
        return response()->json([
            'sesi' => $sesi,
            'transaksi' => $transaksi
        ], 201);
    }

    /**
     * Menampilkan daftar sesi yang pernah diikuti pelanggan (riwayat)
     */
    public function daftarSesiMentor(Request $request)
    {
        $user = $request->user();
        $pelanggan = Pelanggan::where('user_id', $user->id)->firstOrFail();
        $sesi = $pelanggan->sesi()->with(['mentor.user', 'testimoni'])->get();
        return response()->json($sesi);
    }

    /**
     * Mengunggah bukti pembayaran (simulasi upload, update status pembayaran)
     */
    public function unggahBuktiPembayaran(Request $request, $transaksiId)
    {
        $transaksi = \App\Models\Transaksi::findOrFail($transaksiId);
        $transaksi->buktiPembayaran = $request->buktiPembayaran ?? 'bukti_dummy.jpg';
        $transaksi->statusPembayaran = 'menunggu_verifikasi'; // Update status setelah upload
        $transaksi->save();
        return response()->json(['message' => 'Bukti pembayaran berhasil diunggah', 'transaksi' => $transaksi]);
    }

    /**
     * Memberikan testimoni setelah sesi selesai
     */
    public function beriTestimoni(Request $request, $sesiId)
    {
        $testimoni = Testimoni::create([
            'sesi_id' => $sesiId,
            'pelanggan_id' => $request->pelanggan_id,
            'mentor_id' => $request->mentor_id,
            'rating' => $request->rating,
            'komentar' => $request->komentar,
            'tanggal' => now(),
        ]);
        return response()->json($testimoni, 201);
    }
}

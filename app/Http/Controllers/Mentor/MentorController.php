<?php

namespace App\Http\Controllers\Mentor;

use App\Models\Mentor;
use App\Models\Sesi;
use App\Models\Kursus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalKursus;

class MentorController extends Controller
{
    /**
     * Menambahkan jadwal pengajaran (membuat JadwalKursus baru)
     */
    public function aturJadwal(Request $request)
    {
        // Validasi input
        $request->validate([
            'kursus_id' => 'required|exists:kursus,id',
            'id' => 'nullable|exists:jadwal_kursus,id',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'keterangan' => 'nullable|string',
            'tempat' => 'nullable|string',
        ]);

        // Ambil data mentor dan kursus
        $mentor = Mentor::where('user_id', Auth::id())->firstOrFail();
        $kursus = Kursus::findOrFail($request->kursus_id);

        // Pastikan mentor memiliki akses ke kursus ini
        if ($kursus->mentor_id !== $mentor->id) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke kursus ini'], 403);
        }

        // Buat jadwal pengajaran baru
        $jadwalData = [
            'kursus_id' => $request->kursus_id,
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'keterangan' => $request->keterangan,
            'tempat' => $request->tempat,
        ];

        if ($request->has('id')) {
            // Jika ID jadwal sudah ada, update jadwal yang ada
            $jadwal = JadwalKursus::findOrFail($request->id);
            // Pastikan jadwal ini milik kursus yang benar
            if ($jadwal->kursus_id !== $kursus->id) {
                return response()->json(['message' => 'Jadwal tidak ditemukan untuk kursus ini'], 404);
            }
            // Perbarui jadwal dengan data baru
            $jadwal->update($jadwalData);
            $messege = 'Jadwal pengajaran berhasil diperbarui';
        } else {
            // Jika ID tidak ada, buat jadwal baru, pastikan kursus ada
            $jadwal = $kursus->jadwalKursus()->create($jadwalData);
            $messege = 'Jadwal pengajaran berhasil dibuat';
        }

        // Jika ada file yang diunggah, simpan file tersebut
        return response()->json([
            'message' => $messege ?? 'Jadwal pengajaran berhasil diperbarui',
            'jadwal' => $jadwal
        ]);
    }

    /**
     * Konfirmasi sesi pengajaran
     */
    public function konfirmasiSesi(Request $request, $sesiId)
    {
        $sesi = Sesi::findOrFail($sesiId);
        $sesi->statusSesi = 'confirmed';
        $sesi->save();

        return response()->json(['message' => 'Sesi pengajaran dikonfirmasi']);
    }

    /**
     * Menyelesaikan sesi pengajaran
     */
    public function selesaiSesi(Request $request, $sesiId)
    {
        $sesi = Sesi::findOrFail($sesiId);
        $sesi->statusSesi = 'selesai';
        $sesi->save();

        return response()->json(['message' => 'Sesi pengajaran selesai']);
    }

    /**
     * Menampilkan profil mentor yang sedang login
     */
    public function profilSaya(Request $request)
    {
        $user = $request->user();
        $mentor = Mentor::where('user_id', $user->id)->with('user')->firstOrFail();
        return response()->json($mentor);
    }

    /**
     * Menampilkan Kursus yang diampu oleh mentor
     */
    public function daftarKursusSaya(Request $request)
    {
        $user = $request->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();
        $kursus = $mentor->kursus()->with('jadwalKursus')->get();
        return response()->json($kursus);
    }

    /**
     * Menampilkan daftar sesi yang diampu oleh mentor
     */
    public function daftarSesiSaya(Request $request)
    {
        $user = $request->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();
        $sesi = $mentor->sesi()->with(['pelanggan.user', 'kursus'])->get();
        return response()->json($sesi);
    }

    /**
     * Menampilkan daftar testimoni yang diterima mentor
     */
    public function daftarTestimoni(Request $request)
    {
        $user = $request->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();
        $testimoni = $mentor->sesi()->with('testimoni')->get()->pluck('testimoni')->filter();
        return response()->json($testimoni->flatten());
    }
}

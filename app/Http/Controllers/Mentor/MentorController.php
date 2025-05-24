<?php

namespace App\Http\Controllers\Mentor;

use App\Models\Mentor;
use App\Models\Sesi;
use App\Models\Kursus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MentorController extends Controller
{
    /**
     * Menambahkan jadwal pengajaran (membuat JadwalKursus baru)
     */
    public function aturJadwal(Request $request)
    {
        $mentor = Mentor::findOrFail($request->mentor_id);
        $kursus = Kursus::where('mentor_id', $mentor->id)->firstOrFail();
        $jadwal = $kursus->jadwalKursus()->create([
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'keterangan' => $request->keterangan,
        ]);

        return response()->json(['message' => 'Jadwal pengajaran ditambahkan', 'jadwal' => $jadwal]);
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

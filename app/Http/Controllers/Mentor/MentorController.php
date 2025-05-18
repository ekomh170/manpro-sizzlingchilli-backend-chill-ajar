<?php

namespace App\Http\Controllers\Mentor;

use App\Models\Mentor;
use App\Models\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MentorController extends Controller
{
    /**
     * Menambahkan atau memperbarui jadwal pengajaran
     */
    public function aturJadwal(Request $request)
    {
        $mentor = Mentor::findOrFail($request->mentor_id);
        $mentor->jadwal = $request->jadwal; // Menyimpan jadwal pengajaran
        $mentor->save();

        return response()->json(['message' => 'Jadwal pengajaran diperbarui']);
    }

    /**
     * Menentukan gaya belajar (online/offline)
     */
    public function aturGayaMengajar(Request $request)
    {
        $mentor = Mentor::findOrFail($request->mentor_id);
        $mentor->gayaMengajar = $request->gayaMengajar; // Menyimpan gaya mengajar
        $mentor->save();

        return response()->json(['message' => 'Gaya mengajar diperbarui']);
    }

    /**
     * Konfirmasi sesi pengajaran
     */
    public function konfirmasiSesi(Request $request, $sessionId)
    {
        $session = Session::findOrFail($sessionId);
        $session->statusSesi = 'confirmed'; // Mengonfirmasi sesi pengajaran
        $session->save();

        return response()->json(['message' => 'Sesi pengajaran dikonfirmasi']);
    }

    /**
     * Menyelesaikan sesi pengajaran
     */
    public function selesaiSesi(Request $request, $sessionId)
    {
        $session = Session::findOrFail($sessionId);
        $session->statusSesi = 'completed'; // Menyelesaikan sesi pengajaran
        $session->save();

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
     * Menampilkan daftar sesi yang diampu oleh mentor
     */
    public function daftarSesiSaya(Request $request)
    {
        $user = $request->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();
        $sesi = $mentor->sessions()->with(['pelanggan.user'])->get();
        return response()->json($sesi);
    }

    /**
     * Menampilkan daftar testimoni yang diterima mentor
     */
    public function daftarTestimoni(Request $request)
    {
        $user = $request->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();
        $testimoni = $mentor->sessions()->with('testimoni')->get()->pluck('testimoni')->filter();
        return response()->json($testimoni);
    }
}

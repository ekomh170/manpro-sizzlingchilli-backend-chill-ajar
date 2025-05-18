<?php

namespace App\Http\Controllers\Pelanggan;

use App\Models\Mentor;
use App\Models\Session;
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
        $pelanggan = \App\Models\Pelanggan::where('user_id', $user->id)->with('user')->firstOrFail();
        return response()->json($pelanggan);
    }

    /**
     * Menampilkan daftar course
     */
    public function daftarCourse()
    {
        $courses = \App\Models\Course::with('mentor.user')->get();
        return response()->json($courses);
    }

    /**
     * Mencari mentor berdasarkan mata kuliah
     */
    public function cariMentor(Request $request)
    {
        // Mencari mentor yang mengajar mata kuliah tertentu
        $mentors = Mentor::whereHas('courses', function ($query) use ($request) {
            $query->where('namaCourse', 'like', '%' . $request->namaCourse . '%');
        })->get();

        return response()->json($mentors);
    }

    /**
     * Menampilkan detail mentor
     */
    public function detailMentor($mentorId)
    {
        $mentor = \App\Models\Mentor::with('user', 'courses')->findOrFail($mentorId);
        return response()->json($mentor);
    }

    /**
     * Memesan sesi pengajaran
     */
    public function pesanSesi(Request $request)
    {
        // Membuat sesi pengajaran baru untuk pelanggan
        $session = Session::create([
            'mentor_id' => $request->mentor_id,
            'pelanggan_id' => $request->pelanggan_id,
            'detailKursus' => $request->detailKursus,
            'jadwal' => $request->jadwal,
            'statusSesi' => 'booked', // Status sesi: booked
        ]);

        return response()->json($session, 201);
    }

    /**
     * Menampilkan daftar sesi yang pernah diikuti pelanggan
     */
    public function daftarSesiMentor(Request $request)
    {
        $user = $request->user();
        $pelanggan = \App\Models\Pelanggan::where('user_id', $user->id)->firstOrFail();
        $sesi = $pelanggan->sessions()->with(['mentor.user', 'testimoni'])->get();
        return response()->json($sesi);
    }

    /**
     * Mengunggah bukti pembayaran (simulasi)
     */
    public function unggahBuktiPembayaran(Request $request, $paymentId)
    {
        $payment = \App\Models\Payment::findOrFail($paymentId);
        // Simulasi unggah bukti pembayaran (misal: simpan nama file bukti)
        $payment->buktiPembayaran = $request->buktiPembayaran ?? 'bukti_dummy.jpg';
        $payment->statusPembayaran = 'menunggu_verifikasi';
        $payment->save();
        return response()->json(['message' => 'Bukti pembayaran berhasil diunggah', 'payment' => $payment]);
    }

    /**
     * Memberikan testimoni setelah sesi selesai
     */
    public function beriTestimoni(Request $request, $sessionId)
    {
        // Membuat testimoni setelah sesi selesai
        $testimoni = Testimoni::create([
            'session_id' => $sessionId,
            'pelanggan_id' => $request->pelanggan_id,
            'mentor_id' => $request->mentor_id,
            'rating' => $request->rating,
            'komentar' => $request->komentar,
        ]);

        return response()->json($testimoni, 201);
    }
}

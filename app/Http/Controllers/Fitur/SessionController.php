<?php

namespace App\Http\Controllers\Fitur;

use App\Models\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Fitur\FiturController;

class SessionController extends FiturController
{
    /**
     * Menampilkan daftar semua sesi
     */
    public function index()
    {
        return response()->json(Session::with(['mentor.user', 'pelanggan.user', 'testimoni'])->get());
    }

    /**
     * Menampilkan detail sesi tertentu
     */
    public function show($id)
    {
        $session = Session::with(['mentor.user', 'pelanggan.user', 'testimoni'])->findOrFail($id);
        return response()->json($session);
    }

    /**
     * Membuat sesi baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'mentor_id' => 'required|exists:mentor,id',
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'detailKursus' => 'required|string',
            'jadwal' => 'required|date',
        ]);
        $session = Session::create([
            'mentor_id' => $request->mentor_id,
            'pelanggan_id' => $request->pelanggan_id,
            'detailKursus' => $request->detailKursus,
            'jadwal' => $request->jadwal,
            'statusSesi' => 'booked',
        ]);
        return response()->json($session, 201);
    }

    /**
     * Memperbarui sesi
     */
    public function update(Request $request, $id)
    {
        $session = Session::findOrFail($id);
        $session->update($request->all());
        return response()->json($session);
    }

    /**
     * Menghapus sesi
     */
    public function destroy($id)
    {
        $session = Session::findOrFail($id);
        $session->delete();
        return response()->json(null, 204);
    }

    /**
     * Konfirmasi sesi (opsional, jika dibutuhkan)
     */
    public function konfirmasiSesi($id)
    {
        $session = Session::findOrFail($id);
        $session->statusSesi = 'confirmed';
        $session->save();
        return response()->json(['message' => 'Sesi dikonfirmasi', 'session' => $session]);
    }

    /**
     * Tandai sesi sebagai selesai (opsional, jika dibutuhkan)
     */
    public function selesaiSesi($id)
    {
        $session = Session::findOrFail($id);
        $session->statusSesi = 'completed';
        $session->save();
        return response()->json(['message' => 'Sesi selesai', 'session' => $session]);
    }
}

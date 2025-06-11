<?php

namespace App\Http\Controllers\Fitur;

use App\Models\JadwalKursus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Kursus;

class JadwalKursusController extends Controller
{
    public function index()
    {
        return response()->json(JadwalKursus::with('kursus')->get());
    }

    public function show($id)
    {
        $jadwal = JadwalKursus::with('kursus')->findOrFail($id);
        return response()->json($jadwal);
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'kursus_id' => 'required|exists:kursus,id',
            'id' => 'nullable|exists:jadwal_kursus,id',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'gayaMengajar' => 'required|in:online,offline', // WAJIB
            'keterangan' => 'nullable|string',
            'tempat' => 'nullable|string',
        ]);

        // Ambil data kursus
        $kursus = Kursus::findOrFail($request->kursus_id);

        // Buat atau perbarui jadwal pengajaran
        $jadwalData = [
            'kursus_id' => $request->kursus_id,
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'gayaMengajar' => $request->gayaMengajar, // WAJIB
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
            $jadwal->update($jadwalData);
            $message = 'Jadwal pengajaran berhasil diperbarui';
        } else {
            // Jika ID tidak ada, buat jadwal baru
            $jadwal = $kursus->jadwalKursus()->create($jadwalData);
            $message = 'Jadwal pengajaran berhasil dibuat';
        }

        // Kembalikan respons
        return response()->json([
            'message' => $message,
            'jadwal' => $jadwal,
        ]);
    }
    public function update(Request $request, $id)
    {
        $jadwal = JadwalKursus::findOrFail($id);
        $request->validate([
            'kursus_id' => 'sometimes|exists:kursus,id',
            'tanggal' => 'sometimes|date',
            'waktu' => 'sometimes',
            'gayaMengajar' => 'sometimes|required|in:online,offline', // WAJIB
            'keterangan' => 'nullable|string',
            'tempat' => 'nullable|string',
        ]);
        $jadwal->update($request->all());
        return response()->json($jadwal);
    }

    public function destroy($id)
    {
        $jadwal = JadwalKursus::findOrFail($id);
        $jadwal->delete();
        return response()->json(null, 204);
    }
}

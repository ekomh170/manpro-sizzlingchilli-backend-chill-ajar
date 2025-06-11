<?php

namespace App\Http\Controllers\Fitur;

use App\Models\Testimoni;
use App\Models\Sesi;
use App\Models\Pelanggan;
use App\Models\Mentor;
use Illuminate\Http\Request;
use App\Http\Controllers\Fitur\FiturController;

class TestimoniController extends FiturController
{
    /**
     * Menampilkan daftar semua testimoni
     */
    public function index()
    {
        // Pastikan relasi sesi.jadwal_kursus di-include agar field gayaMengajar tersedia di frontend
        return response()->json(Testimoni::with([
            'sesi.kursus',
            'sesi.jadwalKursus', // relasi jadwal_kursus pada sesi
            'pelanggan.user',
            'mentor.user'
        ])->get());
    }

    /**
     * Menampilkan detail testimoni tertentu
     */
    public function show($id)
    {
        $testimoni = Testimoni::with([
            'sesi.kursus',
            'sesi.jadwalKursus',
            'pelanggan.user',
            'mentor.user'
        ])->findOrFail($id);
        return response()->json($testimoni);
    }

    /**
     * Membuat testimoni baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'sesi_id' => 'required|exists:sesi,id',
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'mentor_id' => 'required|exists:mentor,id',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string',
            'tanggal' => 'required|date',
        ]);
        $testimoni = Testimoni::create($request->all());
        return response()->json($testimoni, 201);
    }

    /**
     * Memperbarui testimoni
     */
    public function update(Request $request, $id)
    {
        $testimoni = Testimoni::findOrFail($id);
        $testimoni->update($request->only(['rating', 'komentar', 'tanggal']));
        return response()->json($testimoni);
    }

    /**
     * Menghapus testimoni
     */
    public function destroy($id)
    {
        $testimoni = Testimoni::findOrFail($id);
        $testimoni->delete();
        return response()->json(null, 204);
    }

    /**
     * Menampilkan semua testimoni untuk mentor tertentu
     */
    public function testimoniMentor($mentorId)
    {
        $testimoni = Testimoni::where('mentor_id', $mentorId)->with(['sesi', 'pelanggan'])->get();
        return response()->json($testimoni);
    }
}

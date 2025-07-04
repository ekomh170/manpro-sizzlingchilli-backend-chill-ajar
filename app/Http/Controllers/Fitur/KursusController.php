<?php

namespace App\Http\Controllers\Fitur;

use App\Models\Kursus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Mentor;

class KursusController extends Controller
{
    public function index()
    {
        return response()->json(Kursus::with(['mentor.user', 'jadwalKursus'])->get());
    }

    public function show($id)
    {
        $kursus = Kursus::with(['mentor', 'jadwalKursus'])->findOrFail($id);
        return response()->json($kursus);
    }

    public function store(Request $request)
    {
        $request->validate([
            'namaKursus' => 'required|string',
            'deskripsi' => 'nullable|string',
            'mentor_id' => 'required|exists:mentor,id', // Validasi mentor_id dari input
            'fotoKursus' => 'nullable|image|max:10240', // Validasi gambar 10MB
        ]);

        $data = $request->all();

        if ($request->hasFile('fotoKursus')) {
            $file = $request->file('fotoKursus');
            if (!$file->isValid()) {
                return response()->json(['message' => 'Upload gambar gagal.'], 422);
            }
            $path = $file->store('foto_kursus', 'public');
            $data['fotoKursus'] = $path;
        }

        $kursus = Kursus::create($data);
        return response()->json($kursus, 201);
    }

    public function update(Request $request, $id)
    {
        // Cari kursus berdasarkan ID tanpa verifikasi mentor
        $kursus = Kursus::findOrFail($id);

        // Validasi input
        $request->validate([
            'namaKursus' => 'sometimes|required|string',
            'deskripsi' => 'nullable|string',
            'mentor_id' => 'required|exists:mentor,id', // Validasi mentor_id dari input
            'fotoKursus' => 'nullable|image|max:10240', // Validasi gambar 10MB
        ]);

        // Ambil data dari request
        $data = $request->all();

        // Tangani upload foto jika ada
        if ($request->hasFile('fotoKursus')) {
            $file = $request->file('fotoKursus');
            if (!$file->isValid()) {
                return response()->json(['message' => 'Upload gambar gagal.'], 422);
            }
            // Hapus file lama jika ada dan bukan default
            if ($kursus->fotoKursus && !str_contains($kursus->fotoKursus, 'default')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($kursus->fotoKursus);
            }
            $path = $file->store('foto_kursus', 'public');
            $data['fotoKursus'] = $path;
        }

        // Update kursus
        $kursus->update($data);

        return response()->json($kursus);
    }

    public function destroy($id)
    {
        $kursus = Kursus::findOrFail($id);
        $kursus->delete();
        return response()->json(null, 204);
    }

    /**
     * Endpoint khusus mentor: tambah kursus milik sendiri
     */
    public function storeKursusSaya(Request $request)
    {
        $user = $request->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();
        $request->validate([
            'namaKursus' => 'required|string',
            'deskripsi' => 'nullable|string',
            'fotoKursus' => 'nullable|image|max:10240', // Validasi gambar 10MB
        ]);
        $data = $request->all();
        $data['mentor_id'] = $mentor->id;
        if ($request->hasFile('fotoKursus')) {
            $file = $request->file('fotoKursus');
            if (!$file->isValid()) {
                return response()->json(['message' => 'Upload gambar gagal.'], 422);
            }
            $path = $file->store('foto_kursus', 'public');
            $data['fotoKursus'] = $path;
        }
        $kursus = Kursus::create($data);
        return response()->json($kursus, 201);
    }

    /**
     * Endpoint khusus mentor: update kursus milik sendiri
     */
    public function updateKursusSaya(Request $request, $id)
    {
        $user = $request->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();
        $kursus = Kursus::where('id', $id)->where('mentor_id', $mentor->id)->firstOrFail();
        $request->validate([
            'namaKursus' => 'sometimes|required|string',
            'deskripsi' => 'nullable|string',
            'fotoKursus' => 'nullable|image|max:10240', // Validasi gambar 10MB
        ]);
        $data = $request->all();
        if ($request->hasFile('fotoKursus')) {
            $file = $request->file('fotoKursus');
            if (!$file->isValid()) {
                return response()->json(['message' => 'Upload gambar gagal.'], 422);
            }
            // Hapus file lama jika ada dan bukan default
            if ($kursus->fotoKursus && !str_contains($kursus->fotoKursus, 'default')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($kursus->fotoKursus);
            }
            $path = $file->store('foto_kursus', 'public');
            $data['fotoKursus'] = $path;
        }
        $kursus->update($data);
        return response()->json($kursus);
    }
}

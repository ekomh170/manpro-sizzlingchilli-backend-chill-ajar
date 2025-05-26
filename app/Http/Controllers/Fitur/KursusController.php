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
        return response()->json(Kursus::with('mentor')->get());
    }

    public function show($id)
    {
        $kursus = Kursus::with(['mentor', 'jadwalKursus'])->findOrFail($id);
        return response()->json($kursus);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();
        $request->validate([
            'namaKursus' => 'required|string',
            'deskripsi' => 'nullable|string',
            'gayaMengajar' => 'required|in:online,offline',
            'fotoKursus' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['mentor_id'] = $mentor->id;

        // Proses upload gambar jika ada file
        if ($request->hasFile('fotoKursus')) {
            $file = $request->file('fotoKursus');
            $path = $file->store('foto_kursus', 'public');
            $data['fotoKursus'] = $path;
        }

        $kursus = Kursus::create($data);
        return response()->json($kursus, 201);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();
        $kursus = Kursus::where('id', $id)->where('mentor_id', $mentor->id)->firstOrFail();
        $data = $request->all();

        // Proses upload gambar jika ada file
        if ($request->hasFile('fotoKursus')) {
            $file = $request->file('fotoKursus');
            $path = $file->store('foto_kursus', 'public');
            $data['fotoKursus'] = $path;
        }

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
            'gayaMengajar' => 'required|in:online,offline',
            'fotoKursus' => 'nullable|string',
        ]);
        $data = $request->all();
        $data['mentor_id'] = $mentor->id;
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
        $data = $request->all();
        if ($request->hasFile('fotoKursus')) {
            $file = $request->file('fotoKursus');
            $path = $file->store('foto_kursus', 'public');
            $data['fotoKursus'] = $path;
        }
        $kursus->update($data);
        return response()->json($kursus);
    }
}

<?php

namespace App\Http\Controllers\Fitur;

use App\Models\Kursus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $request->validate([
            'namaKursus' => 'required|string',
            'deskripsi' => 'nullable|string',
            'mentor_id' => 'required|exists:mentor,id',
            'gayaMengajar' => 'required|in:online,offline',
            'fotoKursus' => 'nullable|string',
        ]);
        $kursus = Kursus::create($request->all());
        return response()->json($kursus, 201);
    }

    public function update(Request $request, $id)
    {
        $kursus = Kursus::findOrFail($id);
        $kursus->update($request->all());
        return response()->json($kursus);
    }

    public function destroy($id)
    {
        $kursus = Kursus::findOrFail($id);
        $kursus->delete();
        return response()->json(null, 204);
    }
}

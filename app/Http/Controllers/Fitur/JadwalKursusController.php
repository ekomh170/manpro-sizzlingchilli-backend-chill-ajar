<?php

namespace App\Http\Controllers\Fitur;

use App\Models\JadwalKursus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $request->validate([
            'kursus_id' => 'required|exists:kursus,id',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'keterangan' => 'nullable|string',
        ]);
        $jadwal = JadwalKursus::create($request->all());
        return response()->json($jadwal, 201);
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalKursus::findOrFail($id);
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

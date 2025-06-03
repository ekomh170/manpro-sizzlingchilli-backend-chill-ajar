<?php

namespace App\Http\Controllers\Fitur;

use App\Models\Sesi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SesiController extends Controller
{
    public function index()
    {
        return response()->json(Sesi::with(['mentor', 'pelanggan', 'kursus', 'jadwalKursus', 'transaksi', 'testimoni'])->get());
    }

    public function show($id)
    {
        $sesi = Sesi::with(['mentor', 'pelanggan', 'kursus', 'jadwalKursus', 'transaksi', 'testimoni'])->findOrFail($id);
        return response()->json($sesi);
    }

    public function store(Request $request)
    {
        $request->validate([
            'mentor_id' => 'required|exists:mentor,id',
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'kursus_id' => 'required|exists:kursus,id',
            'jadwal_kursus_id' => 'required|exists:jadwal_kursus,id',
            'detailKursus' => 'nullable|string',
            'statusSesi' => 'required|string',
        ]);
        $sesi = Sesi::create($request->all());
        return response()->json($sesi, 201);
    }

    public function update(Request $request, $id)
    {
        $sesi = Sesi::findOrFail($id);
        $sesi->update($request->all());
        return response()->json($sesi);
    }

    public function destroy($id)
    {
        $sesi = Sesi::findOrFail($id);
        $sesi->delete();
        return response()->json(null, 204);
    }

    public function konfirmasiSesi(Request $request, $id)
    {
        $sesi = Sesi::findOrFail($id);
        $sesi->statusSesi = 'started';
        $sesi->save();
        return response()->json(['message' => 'Sesi dimulai', 'sesi' => $sesi]);
    }

    public function selesaikanSesi(Request $request, $id)
    {
        $sesi = Sesi::findOrFail($id);
        $sesi->statusSesi = 'end';
        $sesi->save();
        return response()->json(['message' => 'Sesi selesai', 'sesi' => $sesi]);
    }
}

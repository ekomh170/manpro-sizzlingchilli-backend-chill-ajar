<?php

namespace App\Http\Controllers\Fitur;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransaksiController extends Controller
{
    public function index()
    {
        return response()->json(Transaksi::with(['pelanggan', 'mentor', 'sesi'])->get());
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'mentor', 'sesi'])->findOrFail($id);
        return response()->json($transaksi);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'mentor_id' => 'required|exists:mentor,id',
            'sesi_id' => 'required|exists:sesi,id',
            'jumlah' => 'required|numeric',
            'statusPembayaran' => 'required',
            'metodePembayaran' => 'required',
            'tanggalPembayaran' => 'required|date',
        ]);
        $transaksi = Transaksi::create($request->all());
        return response()->json($transaksi, 201);
    }

    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update($request->all());
        return response()->json($transaksi);
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();
        return response()->json(null, 204);
    }

    public function unggahBukti(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->buktiPembayaran = $request->buktiPembayaran ?? 'bukti_dummy.jpg';
        $transaksi->statusPembayaran = 'menunggu_verifikasi';
        $transaksi->save();
        return response()->json(['message' => 'Bukti pembayaran berhasil diunggah', 'transaksi' => $transaksi]);
    }

    public function verifikasiPembayaran(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->statusPembayaran = 'verified';
        $transaksi->save();
        return response()->json(['message' => 'Pembayaran berhasil diverifikasi', 'transaksi' => $transaksi]);
    }

    public function tolakPembayaran(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->statusPembayaran = 'rejected';
        $transaksi->save();
        return response()->json(['message' => 'Pembayaran ditolak', 'transaksi' => $transaksi]);
    }
}

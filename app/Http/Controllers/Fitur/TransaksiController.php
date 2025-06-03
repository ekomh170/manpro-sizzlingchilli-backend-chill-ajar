<?php

namespace App\Http\Controllers\Fitur;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::with([
            'pelanggan.user',
            'mentor.user',
            'sesi.kursus',
            'sesi.jadwalKursus'
        ])->get();
        return response()->json($transaksi);
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'mentor', 'sesi'])->findOrFail($id);
        return response()->json($transaksi);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'nullable|exists:transaksi,id', // Opsional, untuk update
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'mentor_id' => 'required|exists:mentor,id',
            'sesi_id' => 'required|exists:sesi,id',
            'jumlah' => 'required|numeric',
            'statusPembayaran' => 'required', // Bisa diatur ulang ke 'menunggu_verifikasi'
            'metodePembayaran' => 'required',
            'tanggalPembayaran' => 'required|date',
            'buktiPembayaran' => 'image|max:2048', // Opsional untuk update
        ]);

        $transaksiData = [
            'pelanggan_id' => $request->pelanggan_id,
            'mentor_id' => $request->mentor_id,
            'sesi_id' => $request->sesi_id,
            'jumlah' => $request->jumlah,
            'statusPembayaran' => $request->statusPembayaran,
            'metodePembayaran' => $request->metodePembayaran,
            'tanggalPembayaran' => $request->tanggalPembayaran,
        ];

        if ($request->hasFile('buktiPembayaran')) {
            $file = $request->file('buktiPembayaran');
            if (!$file->isValid()) {
                return response()->json(['message' => 'Upload gambar gagal.'], 422);
            }
            $path = $file->store('bukti_bayar', 'public');
            $transaksiData['buktiPembayaran'] = $path;
        }

        if ($request->has('id')) {
            // Update transaksi yang sudah ada
            $transaksi = Transaksi::findOrFail($request->id);
            $transaksi->update($transaksiData);
            // Set status kembali ke 'menunggu_verifikasi' saat update bukti
            $transaksi->statusPembayaran = 'menunggu_verifikasi';
            $transaksi->save();
            $message = 'Transaksi diperbarui dan menunggu verifikasi.';
        } else {
            // Create transaksi baru
            $transaksi = Transaksi::create($transaksiData);
            $message = 'Transaksi baru berhasil dibuat.';
        }

        return response()->json(['message' => $message, 'transaksi' => $transaksi], $request->has('id') ? 200 : 201);
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
        $transaksi->statusPembayaran = 'accepted';
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

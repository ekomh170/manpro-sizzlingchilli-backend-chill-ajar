<?php

namespace App\Http\Controllers\Fitur;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Fitur\FiturController;

class PaymentController extends FiturController
{
    /**
     * Menampilkan daftar semua pembayaran
     */
    public function index()
    {
        return response()->json(Payment::with(['user', 'mentor', 'session'])->get());
    }

    /**
     * Menampilkan detail pembayaran tertentu
     */
    public function show($id)
    {
        $payment = Payment::with(['user', 'mentor', 'session'])->findOrFail($id);
        return response()->json($payment);
    }

    /**
     * Membuat pembayaran baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'mentor_id' => 'required|exists:mentor,id',
            'session_id' => 'required|exists:session,id',
            'jumlah' => 'required|numeric',
            'statusPembayaran' => 'required|string',
            'metodePembayaran' => 'required|string',
            'tanggalPembayaran' => 'required|date',
        ]);
        $payment = Payment::create($request->all());
        return response()->json($payment, 201);
    }

    /**
     * Memperbarui pembayaran
     */
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->update($request->all());
        return response()->json($payment);
    }

    /**
     * Menghapus pembayaran
     */
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
        return response()->json(null, 204);
    }

    /**
     * Unggah bukti pembayaran (simulasi)
     */
    public function unggahBukti(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->buktiPembayaran = $request->buktiPembayaran ?? 'bukti_dummy.jpg';
        $payment->statusPembayaran = 'menunggu_verifikasi';
        $payment->save();
        return response()->json(['message' => 'Bukti pembayaran berhasil diunggah', 'payment' => $payment]);
    }

    /**
     * Verifikasi pembayaran
     */
    public function verifikasiPembayaran($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->statusPembayaran = 'terverifikasi';
        $payment->save();
        return response()->json(['message' => 'Pembayaran berhasil diverifikasi', 'payment' => $payment]);
    }

    /**
     * Tolak pembayaran
     */
    public function tolakPembayaran($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->statusPembayaran = 'ditolak';
        $payment->save();
        return response()->json(['message' => 'Pembayaran ditolak', 'payment' => $payment]);
    }
}

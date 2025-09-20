<?php

namespace App\Http\Controllers\Fitur;

use App\Models\Sesi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SesiController extends Controller
{
    public function index()
    {
        return response()->json(Sesi::whereHas('transaksi', function ($query) {
            $query->where('statusPembayaran', 'accepted');
        })->with(['mentor.user', 'pelanggan.user', 'kursus', 'jadwalKursus', 'paket'])->get());
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

        // Kirim notifikasi WhatsApp ke pelanggan
        $waResult = null;
        try {
            $pelanggan = $sesi->pelanggan;
            if ($pelanggan && $pelanggan->user && $pelanggan->user->nomorTelepon) {
                $waNumber = $pelanggan->user->nomorTelepon;
                $waNumber = trim($waNumber);
                if (strpos($waNumber, '62') === 0 && substr($waNumber, 2, 1) !== '8') {
                    $waNumber = '628' . substr($waNumber, 2);
                } elseif (strpos($waNumber, '08') === 0) {
                    $waNumber = '62' . substr($waNumber, 1);
                }
                $waNumber = preg_replace('/[^0-9]/', '', $waNumber);
                $mentorName = $sesi->mentor && $sesi->mentor->user ? ($sesi->mentor->user->nama ?? '-') : '-';
                $kursusName = $sesi->kursus ? ($sesi->kursus->namaKursus ?? '-') : '-';
                $pelangganName = $pelanggan && $pelanggan->user ? ($pelanggan->user->nama ?? '-') : '-';
                $message = "Halo kak $pelangganName, sesi pengajaran Anda bersama mentor $mentorName (kursus $kursusName) sudah dimulai. Silakan bersiap untuk mengikuti sesi. Terima kasih telah menggunakan Chill Ajar!";
                $client = new Client();
                $gatewayUrl = config('services.wa_gateway.url');
                $response = $client->post($gatewayUrl, [
                    'json' => [
                        'phone' => $waNumber,
                        'message' => $message,
                        'sender' => '6285173028290',
                    ],
                    'timeout' => 10,
                ]);
                $waResult = json_decode($response->getBody()->getContents(), true);
            } else {
                $waResult = [
                    'status' => false,
                    'message' => 'Nomor WhatsApp pelanggan tidak ditemukan',
                ];
            }
        } catch (\Exception $e) {
            Log::error('Gagal kirim WhatsApp ke pelanggan (sesi dimulai): ' . $e->getMessage());
            $waResult = [
                'status' => false,
                'message' => 'Gagal mengirim WhatsApp ke pelanggan. Silakan cek koneksi gateway atau nomor pelanggan.',
                'error' => $e->getMessage(),
            ];
        }

        return response()->json([
            'message' => 'Sesi dimulai',
            'sesi' => $sesi,
            'wa_result' => $waResult,
        ]);
    }

    public function selesaikanSesi(Request $request, $id)
    {
        $sesi = Sesi::findOrFail($id);
        $sesi->statusSesi = 'end';
        $sesi->save();
        return response()->json(['message' => 'Sesi selesai', 'sesi' => $sesi]);
    }
}

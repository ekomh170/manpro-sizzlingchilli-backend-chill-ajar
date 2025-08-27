<?php

namespace App\Http\Controllers\Fitur;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::with([
            'pelanggan.user',
            'mentor.user',
            'sesi.kursus',
            'sesi.jadwalKursus',
            'paket'
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
            'jumlah' => 'nullable|numeric', // Akan dihitung otomatis jika ada paket
            'paket_id' => 'nullable|exists:paket,id',
            'statusPembayaran' => 'required',
            'metodePembayaran' => 'required',
            'tanggalPembayaran' => 'required|date',
            'buktiPembayaran' => 'image|max:10240',
        ]);

        // Hitung jumlah transaksi
        $mentor = \App\Models\Mentor::find($request->mentor_id);
        $biayaPerSesi = $mentor ? ($mentor->biayaPerSesi ?? 0) : 0;
      if ($request->filled('paket_id')) {
    $paket = \App\Models\Paket::find($request->paket_id);
    // Hitung harga paket berdasarkan harga aktual items (setelah diskon item)
    $biayaPaket = 0;
    if ($paket && $paket->items) {
        foreach ($paket->items as $item) {
            $hargaItem = $item->harga ?? 0;
            $diskonItem = $item->diskon ?? 0;
            $biayaPaket += max($hargaItem - $diskonItem, 0);
        }
        // Kurangi diskon paket
        $biayaPaket = max($biayaPaket - ($paket->diskon ?? 0), 0);
    }
    $jumlah = $biayaPaket + $biayaPerSesi;
} else {
    $jumlah = $biayaPerSesi;
}


        
        $transaksiData = [
            'pelanggan_id' => $request->pelanggan_id,
            'mentor_id' => $request->mentor_id,
            'sesi_id' => $request->sesi_id,
            'paket_id' => $request->paket_id ?? null,
            'jumlah' => $jumlah,
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
            $transaksi = Transaksi::findOrFail($request->id);
            $transaksi->update($transaksiData);
            $transaksi->statusPembayaran = 'menunggu_verifikasi';
            $transaksi->save();
            $message = 'Transaksi diperbarui dan menunggu verifikasi.';
        } else {
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

        // Kirim notifikasi WhatsApp ke pelanggan
        $waResultPelanggan = null;
        try {
            $pelanggan = $transaksi->pelanggan;
            $mentor = $transaksi->mentor;
            if ($pelanggan && $pelanggan->user && $pelanggan->user->nomorTelepon) {
                $waNumber = $pelanggan->user->nomorTelepon;
                $waNumber = trim($waNumber);
                if (strpos($waNumber, '62') === 0 && substr($waNumber, 2, 1) !== '8') {
                    $waNumber = '628' . substr($waNumber, 2);
                } elseif (strpos($waNumber, '08') === 0) {
                    $waNumber = '62' . substr($waNumber, 1);
                }
                $waNumber = preg_replace('/[^0-9]/', '', $waNumber);
                $mentorName = $mentor && $mentor->user ? ($mentor->user->nama ?? '-') : '-';
                $kursusName = $transaksi->sesi && $transaksi->sesi->kursus ? ($transaksi->sesi->kursus->namaKursus ?? '-') : '-';
                $pelangganName = $pelanggan && $pelanggan->user ? ($pelanggan->user->nama ?? '-') : '-';
                $message = "Halo kak $pelangganName, pembayaran Anda untuk sesi bersama mentor $mentorName (kursus $kursusName) telah diverifikasi. Sesi Anda akan segera diproses. Terima kasih telah menggunakan Chill Ajar!";
                $client = new \GuzzleHttp\Client();
                $gatewayUrl = config('services.wa_gateway.url');
                $response = $client->post($gatewayUrl, [
                    'json' => [
                        'phone' => $waNumber,
                        'message' => $message,
                        'sender' => '6285173028290',
                    ],
                    'timeout' => 10,
                ]);
                $waResultPelanggan = json_decode($response->getBody()->getContents(), true);
            } else {
                $waResultPelanggan = [
                    'status' => false,
                    'message' => 'Nomor WhatsApp pelanggan tidak ditemukan',
                ];
            }
        } catch (\Exception $e) {
            Log::error('Gagal kirim WhatsApp ke pelanggan (pembayaran diverifikasi): ' . $e->getMessage());
            $waResultPelanggan = [
                'status' => false,
                'message' => 'Gagal mengirim WhatsApp ke pelanggan. Silakan cek koneksi gateway atau nomor pelanggan.',
                'error' => $e->getMessage(),
            ];
        }

        return response()->json([
            'message' => 'Pembayaran berhasil diverifikasi',
            'transaksi' => $transaksi,
            'wa_result_pelanggan' => $waResultPelanggan,
        ]);
    }

    public function tolakPembayaran(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->statusPembayaran = 'rejected';
        $transaksi->save();

        // Kirim notifikasi WhatsApp ke pelanggan untuk upload ulang bukti pembayaran
        $waResult = null;
        try {
            $pelanggan = $transaksi->pelanggan;
            if ($pelanggan && $pelanggan->user && $pelanggan->user->nomorTelepon) {
                $waNumber = $pelanggan->user->nomorTelepon;
                $waNumber = trim($waNumber);
                if (strpos($waNumber, '62') === 0 && substr($waNumber, 2, 1) !== '8') {
                    $waNumber = '628' . substr($waNumber, 2);
                } elseif (strpos($waNumber, '08') === 0) {
                    $waNumber = '62' . substr($waNumber, 1);
                }
                $waNumber = preg_replace('/[^0-9]/', '', $waNumber);
                $mentorName = $transaksi->mentor && $transaksi->mentor->user ? ($transaksi->mentor->user->nama ?? '-') : '-';
                $kursusName = $transaksi->sesi && $transaksi->sesi->kursus ? ($transaksi->sesi->kursus->namaKursus ?? '-') : '-';
                $pelangganName = $pelanggan && $pelanggan->user ? ($pelanggan->user->nama ?? '-') : '-';
                $message = "Halo kak $pelangganName, kami dari tim Chill Ajar ingin menyampaikan bahwa pembayaran Anda untuk sesi bersama mentor $mentorName (kursus $kursusName) ditolak.\n\nSilakan upload ulang bukti pembayaran melalui website Chill Ajar agar sesi Anda dapat diproses kembali. Segera upload agar kami bisa segera proses ya! 🙏😊 Terima kasih.";
                $client = new \GuzzleHttp\Client();
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
            Log::error('Gagal kirim WhatsApp ke pelanggan (pembayaran ditolak): ' . $e->getMessage());
            $waResult = [
                'status' => false,
                'message' => 'Gagal mengirim WhatsApp ke pelanggan. Silakan cek koneksi gateway atau nomor pelanggan.',
                'error' => $e->getMessage(),
            ];
        }

        return response()->json([
            'message' => 'Pembayaran ditolak',
            'transaksi' => $transaksi,
            'wa_result' => $waResult,
        ]);
    }
}

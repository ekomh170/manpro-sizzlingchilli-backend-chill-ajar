<?php

namespace App\Http\Controllers\Mentor;

use App\Models\Mentor;
use App\Models\Sesi;
use App\Models\Kursus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalKursus;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class MentorController extends Controller
{
    /**
     * Menambahkan jadwal pengajaran (membuat JadwalKursus baru)
     */
    public function aturJadwal(Request $request)
    {
        // Validasi input
        $request->validate([
            'kursus_id' => 'required|exists:kursus,id',
            'id' => 'nullable|exists:jadwal_kursus,id',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'gayaMengajar' => 'required|in:online,offline', // WAJIB
            'keterangan' => 'nullable|string',
            'tempat' => 'nullable|string',
        ]);

        // Ambil data mentor dan kursus
        $mentor = Mentor::where('user_id', Auth::id())->firstOrFail();
        $kursus = Kursus::findOrFail($request->kursus_id);

        // Pastikan mentor memiliki akses ke kursus ini
        if ($kursus->mentor_id !== $mentor->id) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke kursus ini'], 403);
        }

        // Buat jadwal pengajaran baru
        $jadwalData = [
            'kursus_id' => $request->kursus_id,
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'gayaMengajar' => $request->gayaMengajar, // WAJIB
            'keterangan' => $request->keterangan,
            'tempat' => $request->tempat,
        ];

        if ($request->has('id')) {
            // Jika ID jadwal sudah ada, update jadwal yang ada
            $jadwal = JadwalKursus::findOrFail($request->id);
            // Pastikan jadwal ini milik kursus yang benar
            if ($jadwal->kursus_id !== $kursus->id) {
                return response()->json(['message' => 'Jadwal tidak ditemukan untuk kursus ini'], 404);
            }
            // Perbarui jadwal dengan data baru
            $jadwal->update($jadwalData);
            $messege = 'Jadwal pengajaran berhasil diperbarui';
        } else {
            // Jika ID tidak ada, buat jadwal baru, pastikan kursus ada
            $jadwal = $kursus->jadwalKursus()->create($jadwalData);
            $messege = 'Jadwal pengajaran berhasil dibuat';
        }

        // Jika ada file yang diunggah, simpan file tersebut
        return response()->json([
            'message' => $messege ?? 'Jadwal pengajaran berhasil diperbarui',
            'jadwal' => $jadwal
        ]);
    }

    /**
     * Mulai sesi pengajaran
     */
    public function mulaiSesi(Request $request, $sesiId)
    {
        $sesi = Sesi::findOrFail($sesiId);
        $sesi->statusSesi = 'started';
        $sesi->save();

        return response()->json(['message' => 'Sesi pengajaran dikonfirmasi']);
    }


    /**
     * Menyelesaikan sesi pengajaran
     */
    public function selesaiSesi(Request $request, $sesiId)
    {
        $sesi = Sesi::findOrFail($sesiId);
        $sesi->statusSesi = 'end';
        $sesi->save();

        // Kirim notifikasi WhatsApp ke pelanggan via whatsapp-web.js gateway
        $waResult = null;
        try {
            // Ambil pelanggan dari relasi langsung field pelanggan_id
            $pelanggan = \App\Models\Pelanggan::with('user')->find($sesi->pelanggan_id);
            if ($pelanggan && $pelanggan->user && $pelanggan->user->nomorTelepon) {
                $waNumber = $pelanggan->user->nomorTelepon;
                // Format nomor WhatsApp pelanggan ke standar Indonesia (628xxxxxxxxxx)
                // Jika nomor diawali '62' tapi setelahnya bukan '8', tambahkan '8' setelah '62'
                if (strpos($waNumber, '62') === 0 && substr($waNumber, 2, 1) !== '8') {
                    $waNumber = '628' . substr($waNumber, 2);
                    // Jika nomor diawali '08', ubah menjadi '628'
                } elseif (strpos($waNumber, '08') === 0) {
                    $waNumber = '62' . substr($waNumber, 1);
                }
                // Hapus semua karakter selain angka
                $waNumber = preg_replace('/[^0-9]/', '', $waNumber);
                $mentorName = $sesi->mentor ? ($sesi->mentor->user->nama ?? '-') : '-';
                $kursusName = $sesi->kursus ? ($sesi->kursus->namaKursus ?? '-') : '-';
                $pelangganName = $pelanggan && $pelanggan->user ? ($pelanggan->user->nama ?? '-') : '-';
                $message = "Halo kak $pelangganName, kami dari tim Chill Ajar ingin menyampaikan bahwa sesi pengajaran Anda bersama mentor $mentorName tentang $kursusName telah selesai.\n\nJangan lupa untuk memberikan testimoni melalui aplikasi ya! Testimoni Anda membantu kami untuk berkembang 😊🙏 Terima kasih!";
                $client = new Client();
                $gatewayUrl = config('services.wa_gateway.url');
                $response = $client->post($gatewayUrl, [
                    'json' => [
                        'phone' => $waNumber,
                        'message' => $message,
                        'sender' => '6285173028290', // nomor sistem
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
            Log::error('Gagal kirim WhatsApp: ' . $e->getMessage());
            $waResult = [
                'status' => false,
                'message' => 'Gagal kirim WhatsApp',
                'error' => $e->getMessage(),
            ];
        }

        return response()->json([
            'message' => 'Sesi pengajaran selesai',
            'wa_result' => $waResult,
        ]);
    }

    /**
     * Menampilkan profil mentor yang sedang login
     */
    public function profilSaya(Request $request)
    {
        $user = $request->user();
        $mentor = Mentor::where('user_id', $user->id)->with('user')->firstOrFail();
        $jumlahKursus = $mentor->kursus()->count();
        $jumlahSesi = $mentor->sesi()->count();

        return response()->json([
            'mentor' => $mentor,
            'jumlah_kursus' => $jumlahKursus,
            'jumlah_sesi' => $jumlahSesi,
        ]);
    }

    /**
     * Menampilkan Kursus yang diampu oleh mentor
     */
    public function daftarKursusSaya(Request $request)
    {
        $user = $request->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();
        $kursus = $mentor->kursus()->with('jadwalKursus')->get();
        return response()->json($kursus);
    }

    /**
     * Menampilkan daftar sesi yang diampu oleh mentor
     */
    public function daftarSesiSaya(Request $request)
    {
        $user = $request->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();
        $sesi = $mentor->sesi()
    ->whereHas('transaksi', function ($query) {
        $query->where('statusPembayaran', 'accepted');
    })
    ->with(['pelanggan.user', 'kursus', 'jadwalKursus', 'paket.items'])
    ->get();
        return response()->json($sesi);

    }

    /**
     * Menampilkan daftar testimoni yang diterima mentor
     */
    public function daftarTestimoni(Request $request)
    {
        $user = $request->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();

        // Ambil sesi dengan relasi testimoni, pelanggan, kursus, dan jadwal_kursus
        $sesi = $mentor->sesi()
            ->with([
                'testimoni',
                'pelanggan.user', // Relasi pelanggan dan user dari pelanggan
                'kursus',        // Relasi kursus
                'jadwalKursus',  // Tambahkan relasi jadwalKursus agar gayaMengajar tersedia
            ])
            ->has('testimoni') // Hanya ambil sesi yang memiliki testimoni
            ->get();

        return response()->json($sesi);
    }

    /**
     * Dashboard info untuk mentor - analytics dan calendar
     */
    public function dashboardInfo(Request $request)
    {
        $user = $request->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();

        // Analytics Data
        $totalSesi = $mentor->sesi()->count();

        // Sesi bulan ini
        $sesiBuilnIni = $mentor->sesi()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Total jam mengajar (estimasi 2 jam per sesi)
        $totalJamMengajar = $totalSesi * 2;

        // Siswa aktif (pelanggan unik)
        $siswaAktif = $mentor->sesi()
            ->distinct('pelanggan_id')
            ->count('pelanggan_id');


        // Jumlah kursus
        $jumlahKursus = $mentor->kursus()->count();

        // Jumlah testimoni
        $jumlahTestimoni = $mentor->sesi()
            ->has('testimoni')
            ->count();

        // Calendar Data - Jadwal 30 hari ke depan dengan sesi status pending saja
        $calendar = JadwalKursus::whereHas('kursus', function ($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })
            ->whereBetween('tanggal', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
            ->with([

                'kursus',
                'sesi' => function ($query) {
                    $query->whereHas('transaksi', function ($subQuery) {
                        $subQuery->where('statusPembayaran', 'accepted');
                    })
                        ->where('statusSesi', 'pending') // Hanya sesi yang statusnya pending
                        ->with(['transaksi', 'pelanggan.user']); // Tambahkan relasi pelanggan.user
                }
            ])
            ->whereHas('sesi', function ($query) {
                $query->whereHas('transaksi', function ($subQuery) {
                    $subQuery->where('statusPembayaran', 'accepted');
                })
                    ->where('statusSesi', 'pending'); // Filter di level jadwal juga
            })
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->get()
            ->map(function ($jadwal) {
                $sesi = $jadwal->sesi->first();

                return [
                    'id' => $jadwal->id,
                    'tanggal' => $jadwal->tanggal,
                    'waktu' => $jadwal->waktu,
                    'gayaMengajar' => $jadwal->gayaMengajar,
                    'keterangan' => $jadwal->keterangan,
                    'tempat' => $jadwal->tempat,
                    'kursus' => $jadwal->kursus,
                    'sesi' => $sesi,
                    'siswaNama' => $sesi && $sesi->pelanggan && $sesi->pelanggan->user ? $sesi->pelanggan->user->nama : null,
                    'status' => 'pending' // Semua yang masuk calendar pasti pending
                ];
            });

        return response()->json([
            'mentor_status' => $mentor->status, // approved, pending, rejected untuk badge
            'analytics' => [
                'total_sesi' => $totalSesi,
                'sesi_bulan_ini' => $sesiBuilnIni,
                'total_jam_mengajar' => $totalJamMengajar,
                'siswa_aktif' => $siswaAktif,
                'rating' => $mentor->rating, // Method di model Mentor
                'jumlah_kursus' => $jumlahKursus,
                'jumlah_testimoni' => $jumlahTestimoni
            ],
            'calendar' => $calendar
        ]);
    }
}

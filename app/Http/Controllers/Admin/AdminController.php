<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Mentor;
use App\Models\Transaksi;
use App\Models\Sesi;
use App\Models\Kursus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\FileDownloadHelper;


class AdminController extends Controller
{

    /**
     * Menampilkan jumlah pengguna, mentor, dan kursus
     */
    public function jumlahData()
    {
        // Ambil 5 user terbaru
        $userTerbaru = User::orderBy('created_at', 'desc')->take(5)->get(['id', 'nama', 'email', 'peran', 'created_at']);

        return response()->json([
            'jumlah_user' => User::count(),
            'jumlah_pelanggan' => Pelanggan::count(),
            'jumlah_mentor' => Mentor::count(),
            'jumlah_kursus' => Kursus::count(),
            'jumlah_sesi' => Sesi::count(),
            'jumlah_transaksi' => Transaksi::count(),
            'user_terbaru' => $userTerbaru,

            // tambah data lain jika perlu
        ]);
    }

    /**
     * Menampilkan daftar semua pengguna
     */
  public function daftarPengguna()
{
    try {
        $users = User::with(['pelanggan', 'mentor'])->get()->map(function ($user) {
            $jumlahSesi = 0;
            $sesiMendatang = 0;
            $sesiSelesai = 0;
            $sesiBerlangsung = 0; 
            
            try {
                if ($user->peran === 'pelanggan' && $user->pelanggan) {
                    // Hitung sesi berdasarkan status untuk pelanggan
                    $reviewedCount = Sesi::whereHas('transaksi', function ($query) use ($user) {
                        $query->where('pelanggan_id', $user->pelanggan->id)
                              ->where('statusPembayaran', 'accepted');
                    })->where('statusSesi', 'reviewed')->count();
                    
                    $pendingCount = Sesi::whereHas('transaksi', function ($query) use ($user) {
                        $query->where('pelanggan_id', $user->pelanggan->id)
                              ->where('statusPembayaran', 'accepted');
                    })->where('statusSesi', 'pending')->count();
                    
                    $startedCount = Sesi::whereHas('transaksi', function ($query) use ($user) {
                        $query->where('pelanggan_id', $user->pelanggan->id)
                              ->where('statusPembayaran', 'accepted');
                    })->where('statusSesi', 'started')->count();
                    
                    $endCount = Sesi::whereHas('transaksi', function ($query) use ($user) {
                        $query->where('pelanggan_id', $user->pelanggan->id)
                              ->where('statusPembayaran', 'accepted');
                    })->where('statusSesi', 'end')->count();
                    
                    $jumlahSesi = $reviewedCount;
                    $sesiMendatang = $pendingCount;
                    $sesiBerlangsung = $startedCount;
                    $sesiSelesai = $endCount;

                } elseif ($user->peran === 'mentor' && $user->mentor) {
                    // Hitung sesi yang diajar mentor
                    $reviewedCount = Sesi::where('mentor_id', $user->mentor->id)
                                        ->whereHas('transaksi', function ($query) {
                                            $query->where('statusPembayaran', 'accepted');
                                        })
                                        ->where('statusSesi', 'reviewed')->count();

                    $pendingCount = Sesi::where('mentor_id', $user->mentor->id)
                                         ->whereHas('transaksi', function ($query) {
                                             $query->where('statusPembayaran', 'accepted');
                                         })
                                         ->where('statusSesi', 'pending')->count();

                    $startedCount = Sesi::where('mentor_id', $user->mentor->id)
                                       ->whereHas('transaksi', function ($query) {
                                           $query->where('statusPembayaran', 'accepted');
                                       })
                                       ->where('statusSesi', 'started')->count();

                    $endCount = Sesi::where('mentor_id', $user->mentor->id)
                                         ->whereHas('transaksi', function ($query) {
                                             $query->where('statusPembayaran', 'accepted');
                                         })
                                         ->where('statusSesi', 'end')->count();
                    
                    $jumlahSesi = $reviewedCount;
                    $sesiMendatang = $pendingCount;
                    $sesiBerlangsung = $startedCount;
                    $sesiSelesai = $endCount;
                }
            } catch (\Exception $e) {
                $jumlahSesi = 0;
                $sesiMendatang = 0;
                $sesiSelesai = 0;
                $sesiBerlangsung = 0;
                Log::warning('Error calculating sesi for user ' . $user->id . ': ' . $e->getMessage());
            }
            
            return [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'peran' => $user->peran,
                'created_at' => $user->created_at,
                'jumlah_sesi' => $jumlahSesi,
                'sesi_mendatang' => $sesiMendatang,
                'sesi_dimulai' => $sesiBerlangsung,
                'sesi_selesai' => $sesiSelesai,
                'nomorTelepon' => $user->nomorTelepon,
                'alamat' => $user->alamat,
            ];
        });

        return response()->json($users);
        
    } catch (\Exception $e) {
        Log::error('Error in daftarPengguna: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch users'], 500);
    }
}

    /**
     * Mengubah role pengguna (admin, mentor, pelanggan)
     */
    public function ubahRolePengguna(Request $request, $userId)
    {
        $request->validate([
            'peran' => 'required|in:admin,mentor,pelanggan',
        ]);
        $user = User::findOrFail($userId);
        $user->peran = $request->peran;
        $user->save();
        return response()->json(['message' => 'Role pengguna berhasil diperbarui', 'user' => $user]);
    }

    // ==================== USER, MENTOR, PELANGGAN ====================
    /**
     * Tambah pengguna baru (admin bisa tambah user, mentor, atau pelanggan)
     * Akan mengarahkan ke fungsi tambahMentor/tambahPelanggan jika peran sesuai
     */
    public function tambahPengguna(Request $request)
    {
        if ($request->peran === 'mentor') {
            return $this->tambahMentor($request);
        }
        if ($request->peran === 'pelanggan') {
            return $this->tambahPelanggan($request);
        }
        // Validasi dan tambah admin biasa
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'peran' => 'required|in:admin',
            'nomorTelepon' => 'nullable',
            'alamat' => 'nullable',


        ]);
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'peran' => $request->peran,
            'nomorTelepon' => $request->nomorTelepon,
            'alamat' => $request->alamat,
        ]);
        return response()->json(['message' => 'Admin berhasil ditambah', 'user' => $user], 201);
    }

    /**
     * Tambah mentor baru (beserta identitas mentor)
     */
    public function tambahMentor(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'deskripsi' => 'nullable',
            'biayaPerSesi' => 'nullable|numeric',
            'biayaPerSesiOffline' => 'nullable|numeric',
        ]);
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'peran' => 'mentor',
        ]);
        $mentor = Mentor::create([
            'user_id' => $user->id,
            'deskripsi' => $request->deskripsi,
            'biayaPerSesi' => $request->biayaPerSesi,
            'biayaPerSesiOffline' => $request->biayaPerSesiOffline,
            // 'gayaMengajar' sudah tidak ada di Mentor, hanya di Kursus
        ]);
        return response()->json([
            'message' => 'Mentor berhasil ditambah',
            'user' => $user,
            'mentor' => $mentor
        ], 201);
    }

    /**
     * Tambah pelanggan baru (beserta identitas pelanggan)
     */
    public function tambahPelanggan(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'nomorTelepon' => 'nullable',
            'alamat' => 'nullable',
        ]);
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'peran' => 'pelanggan',
            'nomorTelepon' => $request->nomorTelepon,
            'alamat' => $request->alamat,
        ]);
        $pelanggan = \App\Models\Pelanggan::create([
            'user_id' => $user->id,
        ]);
        return response()->json([
            'message' => 'Pelanggan berhasil ditambah',
            'user' => $user,
            'pelanggan' => $pelanggan
        ], 201);
    }

    // CRUD Mentor
    public function daftarMentor()
    {
        return response()->json(Mentor::with('user')->get());
    }
    public function detailMentor($id)
    {
        return response()->json(Mentor::with('user')->findOrFail($id));
    }
    public function perbaruiMentor(Request $request, $id)
    {
        $mentor = Mentor::findOrFail($id);

        // Ambil data dari request
        $data = $request->all();
    
        // Tangani upload dokumen jika ada
        if ($request->hasFile('dokumen_pendukung')) {
            $file = $request->file('dokumen_pendukung');
            if (!$file->isValid()) {
                return response()->json(['message' => 'Upload dokumen gagal.'], 422);
            }
            // Hapus file lama jika ada
            if ($mentor->dokumen_pendukung &&  \Illuminate\Support\Facades\Storage::disk('public')->exists($mentor->dokumen_pendukung)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($mentor->dokumen_pendukung);
            }
            $path = $file->store('dokumen_pendukung', 'public');
            $data['dokumen_pendukung'] = $path;
        }
    
        // Update mentor
        $mentor->update($data);
    
        return response()->json([
            'message' => 'Mentor berhasil diperbarui',
            'mentor' => $mentor
        ]);
    }
    public function hapusMentor($id)
    {
        $mentor = Mentor::findOrFail($id);
        $user = $mentor->user; // relasi ke user
        if ($user) {
            $user->delete(); // ini otomatis akan menghapus mentor karena onDelete('cascade')
        } else {
            $mentor->delete(); // fallback jika user tidak ditemukan
        }
        return response()->json(['message' => 'Mentor & user terkait berhasil dihapus']);
    }


    // CRUD Pelanggan
    public function daftarPelanggan()
    {
        // Tampilkan pelanggan beserta relasi user
        return response()->json(\App\Models\Pelanggan::with('user')->get());
    }
    public function detailPelanggan($id)
    {
        return response()->json(\App\Models\Pelanggan::findOrFail($id));
    }
    public function perbaruiPelanggan(Request $request, $id)
    {
        $pelanggan = \App\Models\Pelanggan::findOrFail($id);
        $pelanggan->update($request->all());
        return response()->json(['message' => 'Pelanggan berhasil diperbarui', 'pelanggan' => $pelanggan]);
    }
    public function hapusPelanggan($id)
    {
        $pelanggan = \App\Models\Pelanggan::findOrFail($id);
        $pelanggan->delete();
        return response()->json(['message' => 'Pelanggan berhasil dihapus']);
    }

    // ==================== PEMBAYARAN & NOTIFIKASI ====================
    /**
     * Verifikasi pembayaran
     */
    public function verifikasiPembayaran(Request $request, $transaksiId)
    {
        $transaksi = Transaksi::findOrFail($transaksiId);
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

        // Kirim notifikasi WhatsApp ke mentor
        $waResultMentor = null;
        try {
            $mentor = $transaksi->mentor;
            if ($mentor && $mentor->user && $mentor->user->nomorTelepon) {
                $waNumberMentor = $mentor->user->nomorTelepon;
                $waNumberMentor = trim($waNumberMentor);
                if (strpos($waNumberMentor, '62') === 0 && substr($waNumberMentor, 2, 1) !== '8') {
                    $waNumberMentor = '628' . substr($waNumberMentor, 2);
                } elseif (strpos($waNumberMentor, '08') === 0) {
                    $waNumberMentor = '62' . substr($waNumberMentor, 1);
                }
                $waNumberMentor = preg_replace('/[^0-9]/', '', $waNumberMentor);
                $pelangganName = $transaksi->pelanggan && $transaksi->pelanggan->user ? ($transaksi->pelanggan->user->nama ?? '-') : '-';
                $kursusName = $transaksi->sesi && $transaksi->sesi->kursus ? ($transaksi->sesi->kursus->namaKursus ?? '-') : '-';
                $messageMentor = "Halo kak $mentorName, kami dari tim Chill Ajar ingin menginformasikan bahwa pembayaran dari pelanggan $pelangganName untuk sesi kursus $kursusName sudah diverifikasi. Selamat mengajar dan semoga sesi berjalan lancar! Jangan lupa untuk mulai sesi di web Chill Ajar saat pengajaran dimulai. Terima kasih atas dedikasi dan semangatnya menjadi mentor di Chill Ajar! 😊🙏";
                $client = new \GuzzleHttp\Client();
                $gatewayUrl = config('services.wa_gateway.url');
                $response = $client->post($gatewayUrl, [
                    'json' => [
                        'phone' => $waNumberMentor,
                        'message' => $messageMentor,
                        'sender' => '6285173028290',
                    ],
                    'timeout' => 10,
                ]);
                $waResultMentor = json_decode($response->getBody()->getContents(), true);
            } else {
                $waResultMentor = [
                    'status' => false,
                    'message' => 'Nomor WhatsApp mentor tidak ditemukan',
                ];
            }
        } catch (\Exception $e) {
            Log::error('Gagal kirim WhatsApp ke mentor (pembayaran diverifikasi): ' . $e->getMessage());
            $waResultMentor = [
                'status' => false,
                'message' => 'Gagal mengirim WhatsApp ke mentor. Silakan cek koneksi gateway atau nomor mentor.',
                'error' => $e->getMessage(),
            ];
        }

        return response()->json([
            'message' => 'Pembayaran berhasil diverifikasi',
            'transaksi' => $transaksi,
            'wa_result_pelanggan' => $waResultPelanggan,
            'wa_result_mentor' => $waResultMentor,
        ]);
    }

    /**
     * Menolak pembayaran
     */
    public function tolakPembayaran(Request $request, $transaksiId)
    {
        $transaksi = Transaksi::findOrFail($transaksiId);
        $transaksi->statusPembayaran = 'rejected';
        $transaksi->save();

        // Kirim notifikasi WhatsApp ke pelanggan untuk upload ulang bukti pembayaran
        $waResult = null;
        try {
            $pelanggan = $transaksi->pelanggan;
            if ($pelanggan && $pelanggan->user && $pelanggan->user->nomorTelepon) {
                $waNumber = $pelanggan->user->nomorTelepon;
                // Format nomor WhatsApp pelanggan ke standar Indonesia (628xxxxxxxxxx)
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
            // Log error jika gagal kirim WhatsApp
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


    /**
     * Notifikasi ke mentor setelah pembayaran diverifikasi
     */
    public function notifikasiKeMentor($sessionId)
    {
        // Integrasi notifikasi Telegram ke mentor
        return response()->json(['message' => 'Notifikasi ke mentor telah dikirim']);
    }

    /**
     * Function Download
     */

 public function downloadBuktiPembayaran($transaksiId)
{
    try {
        $transaksi = Transaksi::findOrFail($transaksiId);

        if (!$transaksi->buktiPembayaran) {
            return response()->json(['message' => 'Bukti pembayaran tidak ditemukan'], 404);
        }

        $filePath = storage_path('app/public/' . $transaksi->buktiPembayaran);
        
        if (!file_exists($filePath)) {
            return response()->json(['message' => 'File tidak ditemukan di server'], 404);
        }

        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';


        return response()->file($filePath, [
            'Content-Type' => $mimeType,
        ]);

    } catch (ModelNotFoundException $e) {
        return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
    } catch (\Exception $e) {
        Log::error('Error downloading bukti pembayaran: ' . $e->getMessage());
        return response()->json(['message' => 'Terjadi kesalahan server'], 500);
    }
}

public function downloadDokumenMentor($mentorId)
{
    try {
        $mentor = Mentor::findOrFail($mentorId);

        if (!$mentor->dokumen_pendukung) {
            return response()->json(['message' => 'Dokumen pendukung tidak tersedia'], 404);
        }

        $filePath = storage_path('app/public/' . $mentor->dokumen_pendukung);
        
        if (!file_exists($filePath)) {
            return response()->json(['message' => 'File tidak ditemukan di server'], 404);
        }

        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
        ]);

    } catch (ModelNotFoundException $e) {
        return response()->json(['message' => 'Mentor tidak ditemukan'], 404);
    } catch (\Exception $e) {
        Log::error('Error downloading dokumen mentor: ' . $e->getMessage());
        return response()->json(['message' => 'Terjadi kesalahan server'], 500);
    }
}

}

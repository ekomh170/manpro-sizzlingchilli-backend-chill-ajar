<?php

use Illuminate\Support\Facades\Route;

// Halaman utama
Route::get('/', function () {
    return view('welcome');
});

// Pentest UI per controller
Route::get('/pentest', function () {
    $wa_gateway_url = env('WA_GATEWAY_URL');
    return view('pentest.index', compact('wa_gateway_url'));
});
Route::get('/pentest/auth', function () {
    return view('pentest.auth');
});
Route::get('/pentest/mentor', function () {
    return view('pentest.mentor');
});
Route::get('/pentest/pelanggan', function () {
    return view('pentest.pelanggan');
});
Route::get('/pentest/kursus', function () {
    return view('pentest.kursus');
});
Route::get('/pentest/sesi', function () {
    return view('pentest.sesi');
});
Route::get('/pentest/transaksi', function () {
    return view('pentest.transaksi');
});
Route::get('/pentest/testimoni', function () {
    return view('pentest.testimoni');
});
Route::get('/pentest/admin', function () {
    return view('pentest.admin');
});
// (endpoint automation hapus sesi expired dipindah ke api.php)

Route::get('/debug-log', function () {
    return response()->file(storage_path('logs/laravel.log'));
});

Route::get('/pentest/wa', function () {
    return view('pentest.wa');
})->name('pentest.wa');

Route::post('/pentest/wa', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'nomor' => 'required',
        'pesan' => 'required',
    ]);
    $wa_gateway_url = env('WA_GATEWAY_URL');
    // Debug jika env tidak terbaca
    if (!$wa_gateway_url) {
        dd([
            'WA_GATEWAY_URL' => env('WA_GATEWAY_URL'),
            'file_exists' => file_exists(base_path('.env')),
            'app_env' => env('APP_ENV'),
        ]);
    }
    // Normalisasi nomor WA: hapus non-digit, ubah 08... jadi 628..., pastikan prefix 628
    $nomor = preg_replace('/[^0-9]/', '', $request->nomor);
    if (strpos($nomor, '08') === 0) {
        $nomor = '62' . substr($nomor, 1);
    } elseif (strpos($nomor, '62') !== 0) {
        $nomor = '628' . ltrim($nomor, '0');
    }
    try {
        $client = new \GuzzleHttp\Client();
        $response = $client->post($wa_gateway_url, [
            'json' => [
                'phone' => $nomor,
                'message' => $request->pesan,
                'sender' => '6285173028290',
            ],
            'timeout' => 10,
        ]);
        if ($response->getStatusCode() === 200) {
            return redirect()->route('pentest.wa')->with('success', 'Pesan berhasil dikirim!');
        } else {
            return redirect()->route('pentest.wa')->with('error', 'Gagal mengirim pesan.');
        }
    } catch (\Exception $e) {
        return redirect()->route('pentest.wa')->with('error', 'Gagal mengirim pesan: ' . $e->getMessage());
    }
})->name('pentest.wa.send');

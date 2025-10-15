<?php

namespace App\Http\Controllers\Fitur;

use App\Models\Kursus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Mentor;

class KursusController extends Controller
{
    public function index()
    {
        // Ambil kursus beserta mentor, jadwal, dan paket aktif (beserta item_paket)
        $kursus = Kursus::with([
            'mentor.user',
            'jadwalKursus',
            'visibilitasPaket.paket.items'
        ])->get();
        return response()->json($kursus);
    }

    public function show($id)
    {
        // Ambil detail kursus beserta relasi lengkap
        $kursus = Kursus::with([
            'mentor',
            'jadwalKursus',
            'visibilitasPaket.paket.items'
        ])->findOrFail($id);
        return response()->json($kursus);
    }

    public function store(Request $request)
    {
        // Jika visibilitas_paket dikirim sebagai string (FormData), decode dulu
        if ($request->has('visibilitas_paket') && is_string($request->visibilitas_paket)) {
            $decoded = json_decode($request->visibilitas_paket, true);
            if (is_array($decoded)) {
                $request->merge(['visibilitas_paket' => $decoded]);
            }
        }
        $request->validate([
            'namaKursus' => 'required|string',
            'deskripsi' => 'nullable|string',
            'mentor_id' => 'required|exists:mentor,id', // Validasi mentor_id dari input
            'fotoKursus' => 'nullable|image|max:10240', // Validasi gambar 10MB
            'paket_ids' => 'required|array|min:1', // Wajib pilih minimal 1 paket
            'paket_ids.*' => 'exists:paket,id', // Pastikan id paket valid
            'visibilitas_paket' => 'nullable|array', // Visibilitas paket opsional
            'visibilitas_paket.*.paket_id' => 'exists:paket,id', // Validasi id paket
            'visibilitas_paket.*.visibilitas' => 'boolean', // Validasi status visibilitas
        ], [
            'paket_ids.required' => 'Minimal satu paket harus dipilih.',
            'paket_ids.min' => 'Minimal satu paket harus dipilih.',
        ]);

        $data = $request->except(['paket_ids', 'visibilitas_paket']);

        if ($request->hasFile('fotoKursus')) {
            $file = $request->file('fotoKursus');
            if (!$file->isValid()) {
                return response()->json(['message' => 'Upload gambar gagal.'], 422);
            }
            $path = $file->store('foto_kursus', 'public');
            $data['fotoKursus'] = $path;
        }

        $kursus = Kursus::create($data);
        // Simpan relasi paket aktif ke kursus (tabel visibilitas_paket)
        if ($request->has('paket_ids')) {
            foreach ($request->paket_ids as $paket_id) {
                \App\Models\VisibilitasPaket::updateOrCreate([
                    'kursus_id' => $kursus->id,
                    'paket_id' => $paket_id,
                ], [
                    'visibilitas' => true
                ]);
            }
        }
        // Kembalikan kursus beserta relasi visibilitas_paket, paket, dan item_paket
        return response()->json($kursus->load([
            'mentor',
            'jadwalKursus',
            'visibilitasPaket.paket.items'
        ]), 201);
    }

    public function update(Request $request, $id)
    {
        // Jika visibilitas_paket dikirim sebagai string (FormData), decode dulu
        if ($request->has('visibilitas_paket') && is_string($request->visibilitas_paket)) {
            $decoded = json_decode($request->visibilitas_paket, true);
            if (is_array($decoded)) {
                $request->merge(['visibilitas_paket' => $decoded]);
            }
        }
        // Cari kursus berdasarkan ID tanpa verifikasi mentor
        $kursus = Kursus::findOrFail($id);

        // Validasi input
        $request->validate([
            'namaKursus' => 'sometimes|required|string',
            'deskripsi' => 'nullable|string',
            'mentor_id' => 'required|exists:mentor,id', // Validasi mentor_id dari input
            'fotoKursus' => 'nullable|image|max:10240', // Validasi gambar 10MB
            'paket_ids' => 'nullable|array',
            'paket_ids.*' => 'exists:paket,id',
            'visibilitas_paket' => 'nullable|array',
            'visibilitas_paket.*.paket_id' => 'exists:paket,id',
            'visibilitas_paket.*.visibilitas' => 'boolean',
        ]);

        $data = $request->except(['paket_ids', 'visibilitas_paket']);

        // Tangani upload foto jika ada
        if ($request->hasFile('fotoKursus')) {
            $file = $request->file('fotoKursus');
            if (!$file->isValid()) {
                return response()->json(['message' => 'Upload gambar gagal.'], 422);
            }
            // Hapus file lama jika ada dan bukan default
            if ($kursus->fotoKursus && !str_contains($kursus->fotoKursus, 'default')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($kursus->fotoKursus);
            }
            $path = $file->store('foto_kursus', 'public');
            $data['fotoKursus'] = $path;
        }

        // Update kursus
        $kursus->update($data);

        // Update relasi paket di visibilitas_paket jika ada paket_ids
        if ($request->has('paket_ids')) {
            // Hapus semua relasi lama, lalu tambahkan yang baru
            \App\Models\VisibilitasPaket::where('kursus_id', $kursus->id)->delete();
            foreach ($request->paket_ids as $paket_id) {
                \App\Models\VisibilitasPaket::updateOrCreate([
                    'kursus_id' => $kursus->id,
                    'paket_id' => $paket_id,
                ], [
                    'visibilitas' => true
                ]);
            }
        }
        // Update status visibilitas jika ada visibilitas_paket
        if ($request->has('visibilitas_paket')) {
        foreach ($request->visibilitas_paket as $vp) {
            // Gunakan updateOrCreate untuk memastikan entry selalu ada
            \App\Models\VisibilitasPaket::updateOrCreate(
                [
                    'kursus_id' => $kursus->id,
                    'paket_id' => $vp['paket_id']
                ],
                [
                    'visibilitas' => $vp['visibilitas']
                ]
            );
        }
    }

        // Kembalikan kursus beserta relasi visibilitas_paket, paket, dan item_paket
        return response()->json($kursus->load([
            'mentor',
            'jadwalKursus',
            'visibilitasPaket.paket.items'
        ]));
    }

    public function destroy($id)
    {
        $kursus = Kursus::findOrFail($id);
        $kursus->delete();
        return response()->json(null, 204);
    }

    /**
     * Endpoint khusus mentor: tambah kursus milik sendiri
     */
    public function storeKursusSaya(Request $request)
    {
        $user = $request->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();
        $request->validate([
            'namaKursus' => 'required|string',
            'deskripsi' => 'nullable|string',
            'fotoKursus' => 'nullable|image|max:10240', // Validasi gambar 10MB
        ]);
        $data = $request->all();
        $data['mentor_id'] = $mentor->id;
        if ($request->hasFile('fotoKursus')) {
            $file = $request->file('fotoKursus');
            if (!$file->isValid()) {
                return response()->json(['message' => 'Upload gambar gagal.'], 422);
            }
            $path = $file->store('foto_kursus', 'public');
            $data['fotoKursus'] = $path;
        }
        $kursus = Kursus::create($data);
        return response()->json($kursus, 201);
    }

    /**
     * Endpoint khusus mentor: update kursus milik sendiri
     */
    public function updateKursusSaya(Request $request, $id)
    {
        $user = $request->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();
        $kursus = Kursus::where('id', $id)->where('mentor_id', $mentor->id)->firstOrFail();
        $request->validate([
            'namaKursus' => 'sometimes|required|string',
            'deskripsi' => 'nullable|string',
            'fotoKursus' => 'nullable|image|max:10240', // Validasi gambar 10MB
        ]);
        $data = $request->all();
        if ($request->hasFile('fotoKursus')) {
            $file = $request->file('fotoKursus');
            if (!$file->isValid()) {
                return response()->json(['message' => 'Upload gambar gagal.'], 422);
            }
            // Hapus file lama jika ada dan bukan default
            if ($kursus->fotoKursus && !str_contains($kursus->fotoKursus, 'default')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($kursus->fotoKursus);
            }
            $path = $file->store('foto_kursus', 'public');
            $data['fotoKursus'] = $path;
        }
        $kursus->update($data);
        return response()->json($kursus);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Mentor;

class AuthController extends Controller
{
    /**
     * Registrasi pengguna baru (memilih aktor: pelanggan/mentor)
     * Akan mengarahkan ke fungsi registrasiPelanggan/registrasiMentor sesuai peran
     */
    public function registrasi(Request $request)
    {
        $request->validate([
            'peran' => 'required|in:pelanggan,mentor',
        ]);
        if ($request->peran === 'pelanggan') {
            return $this->registrasiPelanggan($request);
        }
        if ($request->peran === 'mentor') {
            return $this->registrasiMentor($request);
        }
        return response()->json(['message' => 'Peran tidak valid'], 422);
    }

    /**
     * Registrasi pelanggan baru (insert ke users & pelanggan)
     */
    public function registrasiPelanggan(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'nomorTelepon' => 'nullable',
            'alamat' => 'nullable',
        ]);
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'peran' => 'pelanggan',
            'nomorTelepon' => $request->nomorTelepon ?? null,
            'alamat' => $request->alamat ?? null,
        ]);
        $pelanggan = \App\Models\Pelanggan::create([
            'user_id' => $user->id,
        ]);
        $token = $user->createToken('ChillAjarToken')->plainTextToken;
        return response()->json([
            'message' => 'Pelanggan berhasil terdaftar',
            'user' => $user,
            'pelanggan' => $pelanggan,
            'token' => $token
        ], 201);
    }

    /**
     * Registrasi mentor baru (insert ke users & mentor)
     */
    public function registrasiMentor(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'biayaPerSesi' => 'nullable',
            'deskripsi' => 'nullable',
            'nomorTelepon' => 'required|string',
            'alamat' => 'required|string',
            // 'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5048', // GAK DIPAKE KARENA SEKARANG MENTOR DI DAFTARIKAN OLEH ADMIN

        ]);
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'peran' => 'mentor',
            'nomorTelepon' => $request->nomorTelepon,
            'alamat' => $request->alamat,
        ]);

        // Proses upload dokumen pendukung GAK DIPAKE KARENA SEKARANG MENTOR DI DAFTARIKAN OLEH ADMIN
//         $dokumenPath = null;
//         if ($request->hasFile('dokumen_pendukung')) {
//             $file = $request->file('dokumen_pendukung');
//             if (!$file->isValid()) {
//                 return response()->json(['message' => 'Upload dokumen gagal.'], 422);
//             }
//             $dokumenPath = $file->store('dokumen_mentor', 'public');
// }
        $mentor = Mentor::create([
            'user_id' => $user->id,
            'biayaPerSesi' => 25000, // Default biaya per sesi
            'deskripsi' => $request->deskripsi ?? null,
            'rating' => 0,
            'status' => 'active', // Status awal mentor adalah aktif // KARENA SEKARANG MENTOR DI DAFTARIKAN OLEH ADMIN

            // 'dokumen_pendukung' => $dokumenPath, // Simpan path dokumen pendukung // GAK DIPAKE KARENA SEKARANG MENTOR DI DAFTARIKAN OLEH ADMIN

        ]);
        $token = $user->createToken('ChillAjarToken')->plainTextToken;
        return response()->json([
            'message' => 'Mentor berhasil terdaftar',
            'user' => $user,
            'mentor' => $mentor,
            'token' => $token
        ], 201);
    }

    /**
     * Login pengguna (Pelanggan / Mentor / Admin)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::where('email', $request->email)->with('pelanggan', 'mentor')->first();

              // Tambahkan pengecekan status mentor
            if ($user->peran === 'mentor') {
                $mentor = $user->mentor;
                if ($mentor && in_array($mentor->status, ['pending', 'rejected'])) {
                    return response()->json([
                        'message' => 'Akun mentor Anda belum diverifikasi admin. mohon tunggu atau hubungi admin untuk informasi lebih lanjut.',
                    ], 403);
                }
            }

            $token = $user->createToken('ChillAjarToken')->plainTextToken;
            return response()->json([
                'message' => 'Login berhasil',
                'token' => $token,
                'user' => $user
            ]);
        }
        return response()->json(['message' => 'Tidak sah'], 401);
    }

    /**
     * Logout pengguna
     */
    public function logout(Request $request)
    {
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });
        return response()->json(['message' => 'Logout berhasil']);
    }

    /**
     * Upload foto profil pengguna
     */
    public function uploadFotoProfil(Request $request)
    {
        try {
            $user = $request->user();

            Log::channel('storage')->info('Upload foto profil attempt', [
                'user_id' => $user->id,
                'user_name' => $user->nama,
                'has_file' => $request->hasFile('foto_profil')
            ]);

            $request->validate([
                'foto_profil' => 'required|image|max:10240', // Validasi gambar max 10MB
            ]);

            if ($request->hasFile('foto_profil')) {
                $file = $request->file('foto_profil');

                Log::channel('storage')->info('File foto profil received', [
                    'user_id' => $user->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ]);

                if (!$file->isValid()) {
                    Log::channel('storage')->error('Upload foto profil - invalid file', [
                        'user_id' => $user->id,
                        'file_name' => $file->getClientOriginalName()
                    ]);
                    return response()->json(['message' => 'Upload foto gagal.'], 422);
                }

                // Hapus foto lama jika bukan default
                if ($user->foto_profil && !str_contains($user->foto_profil, 'default')) {
                    Storage::disk('public')->delete($user->foto_profil);

                    Log::channel('storage')->info('Old foto profil deleted', [
                        'user_id' => $user->id,
                        'old_path' => $user->foto_profil
                    ]);
                }

                $path = $file->store('foto_profil', 'public');
                $user->foto_profil = $path;
                $user->save();

                Log::channel('storage')->info('Foto profil uploaded successfully', [
                    'user_id' => $user->id,
                    'file_path' => $path
                ]);

                return response()->json(['message' => 'Foto profil berhasil diunggah', 'foto_profil' => $path]);
            }

            Log::channel('storage')->warning('No file uploaded for foto profil', [
                'user_id' => $user->id
            ]);

            return response()->json(['message' => 'Tidak ada file yang diunggah'], 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::channel('storage')->error('Validation failed for upload foto profil', [
                'user_id' => $request->user()->id ?? null,
                'errors' => $e->errors()
            ]);
            throw $e;

        } catch (\Exception $e) {
            Log::channel('storage')->error('Upload foto profil failed', [
                'user_id' => $request->user()->id ?? null,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Gagal mengunggah foto profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update profil user (data + foto profil sekaligus)
     */
    public function updateProfil(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'nomorTelepon' => 'sometimes|nullable|string',
            'alamat' => 'sometimes|nullable|string',
            'foto_profil' => 'sometimes|image|max:10240', // Validasi gambar max 10MB
            'deskripsi' => 'sometimes|nullable|string', // tambahkan validasi deskripsi untuk mentor

        ]);

        // Update data user
        foreach (['nama', 'email', 'nomorTelepon', 'alamat'] as $field) {
            if ($request->has($field)) {
                $user->$field = $request->$field;
            }
        }

        // Proses upload foto profil jika ada file
        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');

            Log::channel('storage')->info('Update profil - foto profil received', [
                'user_id' => $user->id,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ]);

            if (!$file->isValid()) {
                Log::channel('storage')->error('Update profil - invalid foto file', [
                    'user_id' => $user->id,
                    'file_name' => $file->getClientOriginalName()
                ]);
                return response()->json(['message' => 'Upload foto gagal.'], 422);
            }

            // Hapus foto lama jika bukan default
            if ($user->foto_profil && !str_contains($user->foto_profil, 'default')) {
                Storage::disk('public')->delete($user->foto_profil);

                Log::channel('storage')->info('Update profil - old foto deleted', [
                    'user_id' => $user->id,
                    'old_path' => $user->foto_profil
                ]);
            }

            $path = $file->store('foto_profil', 'public');
            $user->foto_profil = $path;

            Log::channel('storage')->info('Update profil - foto uploaded successfully', [
                'user_id' => $user->id,
                'file_path' => $path
            ]);
        }
        $user->save();

        // Jika user adalah mentor, update juga deskripsi di tabel mentor
        if ($user->peran === 'mentor' && $request->has('deskripsi')) {
            $mentor = $user->mentor; // pastikan relasi mentor() ada di model User
            if ($mentor) {
                $mentor->deskripsi = $request->deskripsi;
                $mentor->save();
            }
        }

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'user' => $user
        ]);
    }
}

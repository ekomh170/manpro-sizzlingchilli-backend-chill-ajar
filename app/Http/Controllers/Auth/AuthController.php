<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Registrasi pengguna baru (Pelanggan / Mentor)
     */
    public function registrasi(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        // Membuat pengguna baru
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json(['message' => 'Pengguna berhasil terdaftar', 'user' => $user], 201);
    }

    /**
     * Registrasi pelanggan baru
     */
    public function registrasiPelanggan(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'peran' => 'pelanggan',
        ]);
        return response()->json(['message' => 'Pelanggan berhasil terdaftar', 'user' => $user], 201);
    }

    /**
     * Registrasi mentor baru
     */
    public function registrasiMentor(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'peran' => 'mentor',
        ]);
        return response()->json(['message' => 'Mentor berhasil terdaftar', 'user' => $user], 201);
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
            $user = User::where('email', $request->email)->first();
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
}

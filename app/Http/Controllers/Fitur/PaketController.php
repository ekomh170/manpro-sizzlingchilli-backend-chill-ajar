<?php

namespace App\Http\Controllers\Fitur;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use Illuminate\Http\Request;

class PaketController extends Controller
{
    // [GET] Daftar semua paket
    public function index()
    {
        return response()->json(Paket::with('items')->get());
    }

    // [GET] Detail satu paket beserta item yang dipilih
    public function show($id)
    {
        $paket = Paket::with('items')->findOrFail($id);
        $data = $paket->toArray();
        $data['harga_setelah_diskon'] = max(0, $paket->harga_dasar - ($paket->diskon ?? 0));
        return response()->json($data);
    }

    // [POST] Tambah paket baru beserta item yang dipilih
    public function store(Request $request)
    {
        // Validasi input paket dan item yang dipilih
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'harga_dasar' => 'required|integer',
            'diskon' => 'nullable|integer',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',
            'max_pembelian_per_user' => 'nullable|integer',
            'items' => 'array', // array of item_paket
            'items.*.id' => 'required|integer|exists:item_paket,id',
            'items.*.jumlah_item' => 'nullable|integer|min:1',
        ]);
        $paket = Paket::create($validated);
        // Simpan relasi item yang dipilih ke tabel relasi_item_paket
        if (!empty($validated['items'])) {
            $syncData = [];
            foreach ($validated['items'] as $item) {
                $syncData[$item['id']] = [
                    'jumlah_item' => $item['jumlah_item'] ?? 1
                ];
            }
            $paket->items()->sync($syncData);
        }
        $paket->refresh();
        return response()->json($paket->load('items'), 201);
    }

    // Update paket
    public function update(Request $request, $id)
    {
        $paket = Paket::findOrFail($id);
        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:100',
            'harga_dasar' => 'sometimes|required|integer',
            'diskon' => 'nullable|integer',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',
            'max_pembelian_per_user' => 'nullable|integer',
            'items' => 'array',
            'items.*.id' => 'required_with:items|integer|exists:item_paket,id',
            'items.*.jumlah_item' => 'nullable|integer|min:1',
        ]);
        $paket->update($validated);
        if (isset($validated['items'])) {
            $syncData = [];
            foreach ($validated['items'] as $item) {
                $syncData[$item['id']] = [
                    'jumlah_item' => $item['jumlah_item'] ?? 1
                ];
            }
            $paket->items()->sync($syncData);
        }
        $paket->refresh();
        return response()->json($paket->load('items'));
    }

    // [DELETE] Hapus paket (soft delete)
    public function destroy($id)
    {
        $paket = Paket::findOrFail($id);
        $paket->delete();
        return response()->json(['message' => 'Paket deleted']);
    }
}

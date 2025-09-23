<?php

namespace App\Http\Controllers\Fitur;

use App\Http\Controllers\Controller;
use App\Models\ItemPaket;
use App\Models\Paket;
use Illuminate\Http\Request;

class ItemPaketController extends Controller
{
    // List all item_paket
    public function index()
    {
        return response()->json(ItemPaket::all());
    }

    // Show single item_paket
    public function show($id)
    {
        $item = ItemPaket::findOrFail($id);
        return response()->json($item);
    }

    // Create new item_paket
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'harga' => 'required|integer',
            'diskon' => 'nullable|integer',
            'deskripsi' => 'nullable|string',
        ]);
        $item = ItemPaket::create($validated);
        return response()->json($item, 201);
    }

    // Update item_paket
    public function update(Request $request, $id)
    {
        $item = ItemPaket::findOrFail($id);
        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:100',
            'harga' => 'sometimes|required|integer',
            'diskon' => 'nullable|integer',
            'deskripsi' => 'nullable|string',
        ]);
        $item->update($validated);

        // jika harga/diskon berubah -> rekalkulasi paket terkait
        if ($item->wasChanged(['harga', 'diskon'])) {
            $item->paket()->get()->each(function ($paket) {
                $this->recalculateHargaDasarForPaket($paket);
            });
        }

        return response()->json($item);
    }

    // Delete item_paket (soft delete)
    public function destroy($id)
    {
        $item = ItemPaket::findOrFail($id);

        // ambil paket terkait sebelum delete
        $affectedPakets = $item->paket()->get();

        $item->delete();

        // rekalkulasi tiap paket
        foreach ($affectedPakets as $paket) {
            $this->recalculateHargaDasarForPaket($paket);
        }

        return response()->json(['message' => 'Item paket deleted']);
    }

    /**
     * Hitung ulang harga_dasar paket:
     * harga_dasar = sum( max(item.harga - item.diskon, 0) * jumlah_pivot )
     */
    protected function recalculateHargaDasarForPaket(Paket $paket)
    {
        $paket->load(['items' => function($q) {
            $q->withTrashed(false);
        }]);

        $total = $paket->items->reduce(function ($carry, $it) {
            // skip jika item di-soft-delete
            if (method_exists($it, 'trashed') && $it->trashed()) {
                return $carry;
            }

            $itemPrice = (int) ($it->harga ?? 0);
            $itemDiskon = (int) ($it->diskon ?? 0);
            $jumlah = (int) ($it->pivot->jumlah_item ?? 1);

            return $carry + max($itemPrice - $itemDiskon, 0) * $jumlah;
        }, 0);

        if ((int) $paket->harga_dasar !== $total) {
            $paket->harga_dasar = $total;
            $paket->save();
        }
    }

}

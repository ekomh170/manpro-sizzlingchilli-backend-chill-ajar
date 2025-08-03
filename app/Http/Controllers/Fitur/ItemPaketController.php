<?php

namespace App\Http\Controllers\Fitur;

use App\Http\Controllers\Controller;
use App\Models\ItemPaket;
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
        return response()->json($item);
    }

    // Delete item_paket (soft delete)
    public function destroy($id)
    {
        $item = ItemPaket::findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'Item paket deleted']);
    }
}

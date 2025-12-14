<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // READ (List Items)
    public function index()
    {
        // Only fetch products belonging to the logged-in user
        $products = Produk::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($products);
    }

    // CREATE (Add Item)
    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string',
            'harga_modal' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|integer',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        $path = null;
        if ($request->hasFile('foto')) {
            // Save to storage/app/public/products
            $path = $request->file('foto')->store('products', 'public');
        }

        $product = Produk::create([
            'user_id' => Auth::id(), // Auto-assign current user
            'nama_produk' => $request->nama_produk,
            'harga_modal' => $request->harga_modal,
            'harga_jual' => $request->harga_jual,
            'stok' => $request->stok,
            'foto' => $path,
            'deskripsi' => $request->deskripsi
        ]);

        return response()->json(['message' => 'Produk berhasil ditambahkan', 'data' => $product], 201);
    }

    // UPDATE (Edit Item)
    public function update(Request $request, $id)
    {
        $product = Produk::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'nama_produk' => 'required|string',
            'harga_modal' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|integer',
        ]);

        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($product->foto) {
                Storage::disk('public')->delete($product->foto);
            }
            $product->foto = $request->file('foto')->store('products', 'public');
        }

        $product->update([
            'nama_produk' => $request->nama_produk,
            'harga_modal' => $request->harga_modal,
            'harga_jual' => $request->harga_jual,
            'stok' => $request->stok,
            'deskripsi' => $request->deskripsi
        ]);

        return response()->json(['message' => 'Produk diupdate', 'data' => $product]);
    }

    // DELETE
    public function destroy($id)
    {
        $product = Produk::where('user_id', Auth::id())->findOrFail($id);
        
        if ($product->foto) {
            Storage::disk('public')->delete($product->foto);
        }

        $product->delete();
        return response()->json(['message' => 'Produk dihapus']);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // Read
    public function index()
    {
        $products = Produk::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($products);
    }

    // Create
    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string',
            'harga_modal' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|integer',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('products', 'public');
        }

        $product = Produk::create([
            'user_id' => Auth::id(),
            'nama_produk' => $request->nama_produk,
            'harga_modal' => $request->harga_modal,
            'harga_jual' => $request->harga_jual,
            'stok' => $request->stok,
            'foto' => $path,
            'deskripsi' => $request->deskripsi
        ]);

        return response()->json(['message' => 'Produk berhasil ditambahkan', 'data' => $product], 201);
    }

    // Update
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
            // Hapus foto lama
            if ($product->foto && Storage::disk('public')->exists($product->foto)) {
                Storage::disk('public')->delete($product->foto);
            }
            
            // Insert foto
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

    // Delete
    public function destroy($id)
    {
        $product = Produk::where('user_id', Auth::id())->findOrFail($id);
        
        // Hapus foto dari storage
        if ($product->foto && Storage::disk('public')->exists($product->foto)) {
            Storage::disk('public')->delete($product->foto);
        }

        $product->delete();
        return response()->json(['message' => 'Produk dihapus']);
    }
}
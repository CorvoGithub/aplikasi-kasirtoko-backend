<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TransactionController extends Controller
{

    // GET: List History
    public function index()
    {
        // 1. Get transactions for current user
        // 2. 'with' loads the relationships (Eager Loading) to avoid N+1 query issues
        $transactions = Transaksi::where('user_id', Auth::id())
            ->with(['transaksiDetails.produk']) // Load details AND the product info inside details
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($transactions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:produk,id',
            'items.*.qty' => 'required|integer|min:1',
            'uang_diberikan' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $totalHarga = 0;
            $itemsToInsert = [];

            // Calculate Logic...
            foreach ($request->items as $item) {
                $product = Produk::where('user_id', $user->id)->lockForUpdate()->find($item['id']);

                if (!$product) throw new \Exception("Produk tidak ditemukan.");
                if ($product->stok < $item['qty']) throw new \Exception("Stok {$product->nama_produk} kurang.");

                $subtotal = $product->harga_jual * $item['qty'];
                $totalHarga += $subtotal;

                $itemsToInsert[] = [
                    'product_obj' => $product,
                    'qty' => $item['qty'],
                    'harga_satuan' => $product->harga_jual,
                    'subtotal' => $subtotal
                ];
            }

            if ($request->uang_diberikan < $totalHarga) throw new \Exception("Uang kurang.");

            // Create Transaction
            $transaksi = Transaksi::create([
                'user_id' => $user->id,
                // CHANGE: Use your model's static method
                'kode_transaksi' => Transaksi::generateKodeTransaksi(), 
                'total_harga' => $totalHarga,
                'uang_diberikan' => $request->uang_diberikan,
                'kembalian' => $request->uang_diberikan - $totalHarga,
            ]);

            // Create Details
            foreach ($itemsToInsert as $item) {
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $item['product_obj']->id,
                    'qty' => $item['qty'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['subtotal']
                ]);

                $item['product_obj']->decrement('stok', $item['qty']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Transaksi berhasil!',
                'transaksi' => $transaksi
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
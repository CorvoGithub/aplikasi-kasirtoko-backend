<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * GET: List History with Specific Minute Filtering
     */
    public function index(Request $request)
    {
        $query = Transaksi::where('user_id', Auth::id())
            ->with(['transaksiDetails.produk']);

        if ($request->filled('date')) {
            $date = $request->date; // YYYY-MM-DD

            // 1. Determine Start Time (WIB)
            // Example: 19 becomes "19:00:00"
            $startHour = $request->filled('start_hour') ? $request->start_hour : '00';
            $startTimeString = "$date $startHour:00:00";

            // 2. Determine End Time (WIB)
            // Example: 20 becomes "20:00:59"
            // This captures the whole "20:00" minute, but stops before "20:01"
            if ($request->filled('end_hour')) {
                $endHour = $request->end_hour;
                $endTimeString = "$date $endHour:00:59"; 
            } else {
                // If no end hour selected, cover the whole day
                $endTimeString = "$date 23:59:59";
            }

            try {
                // 3. Convert WIB to UTC for Database Query
                $startUtc = Carbon::createFromFormat('Y-m-d H:i:s', $startTimeString, 'Asia/Jakarta')
                                  ->setTimezone('UTC');

                $endUtc   = Carbon::createFromFormat('Y-m-d H:i:s', $endTimeString, 'Asia/Jakarta')
                                  ->setTimezone('UTC');

                // 4. Apply Filter
                $query->whereBetween('created_at', [$startUtc, $endUtc]);

            } catch (\Exception $e) {
                // Ignore invalid date/time parsing
            }
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        return response()->json($transactions);
    }

    /**
     * POST: Create New Transaction
     */
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

            $transaksi = Transaksi::create([
                'user_id' => $user->id,
                'kode_transaksi' => Transaksi::generateKodeTransaksi(), 
                'total_harga' => $totalHarga,
                'uang_diberikan' => $request->uang_diberikan,
                'kembalian' => $request->uang_diberikan - $totalHarga,
            ]);

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
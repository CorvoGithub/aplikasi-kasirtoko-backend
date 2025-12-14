<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. Total Revenue (Total Pendapatan)
        $totalRevenue = Transaksi::where('user_id', $userId)->sum('total_harga');

        // 2. Total Products (Jumlah Barang di Toko)
        $totalProducts = Produk::where('user_id', $userId)->count();

        // 3. Total Transactions (Jumlah Transaksi Berhasil)
        $totalTransactions = Transaksi::where('user_id', $userId)->count();

        // 4. Today's Revenue (Pendapatan Hari Ini)
        $todayRevenue = Transaksi::where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->sum('total_harga');

        // 5. Recent Activity (5 Transaksi Terakhir)
        $recentActivity = Transaksi::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($transaksi) {
                return [
                    'id' => $transaksi->id,
                    'invoice' => $transaksi->kode_transaksi,
                    'amount' => $transaksi->total_harga,
                    'date' => $transaksi->created_at->diffForHumans(), // e.g., "2 minutes ago"
                ];
            });

        return response()->json([
            'total_revenue' => $totalRevenue,
            'total_products' => $totalProducts,
            'total_transactions' => $totalTransactions,
            'today_revenue' => $todayRevenue,
            'recent_activity' => $recentActivity
        ]);
    }

    public function notifications()
    {
        // Find products belonging to user that have less than 5 items in stock
        $lowStockItems = \App\Models\Produk::where('user_id', Auth::id())
            ->where('stok', '<=', 5)
            ->select('id', 'nama_produk', 'stok')
            ->take(5) // Limit to 5 notifications to not clutter UI
            ->get();

        return response()->json($lowStockItems);
    }
}
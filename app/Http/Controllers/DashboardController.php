<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //Dashboard Overview
    public function index()
    {
        $userId = Auth::id();

        // Total Revenue
        $totalRevenue = Transaksi::where('user_id', $userId)->sum('total_harga');

        // Total Products
        $totalProducts = Produk::where('user_id', $userId)->count();

        // Total Transactions
        $totalTransactions = Transaksi::where('user_id', $userId)->count();

        // Today's Revenue
        $todayRevenue = Transaksi::where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->sum('total_harga');

        // Recent Activity
        $recentActivity = Transaksi::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($transaksi) {
                return [
                    'id' => $transaksi->id,
                    'invoice' => $transaksi->kode_transaksi,
                    'amount' => $transaksi->total_harga,
                    'date' => $transaksi->created_at->diffForHumans(),
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

    // Low Stock Notifications
    public function notifications()
    {
        $lowStockItems = \App\Models\Produk::where('user_id', Auth::id())
            ->where('stok', '<=', 5)
            ->select('id', 'nama_produk', 'stok')
            ->take(5) // Limit 5 
            ->get();

        return response()->json($lowStockItems);
    }
}
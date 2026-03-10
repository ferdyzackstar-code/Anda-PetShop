<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $outletId = $request->get('outlet_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Query dasar
        $query = Transaction::when($outletId, function ($q) use ($outletId) {
            return $q->where('outlet_id', $outletId);
        })->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
            return $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        });

        // Gunakan query ini untuk statistik
        $totalSales = (clone $query)->sum('total_price');
        $transactionCount = (clone $query)->count();
        $recentTransactions = (clone $query)
            ->with(['outlet', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Untuk grafik tetap tampilkan 7 hari terakhir (atau sesuaikan jika perlu)
        $salesData = Transaction::when($outletId, function ($q) use ($outletId) {
            return $q->where('outlet_id', $outletId);
        })
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $productCount = Product::count(); // Produk biasanya global

        return view('dashboard.index', compact('totalSales', 'transactionCount', 'productCount', 'salesData', 'recentTransactions'));
    }
}

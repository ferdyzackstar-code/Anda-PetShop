<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Kosongkan dulu isinya agar tidak error
        $productCount = \App\Models\Product::count();
        
        // Kirim data kosong ke view agar tidak error 'undefined variable' di Blade
        return view('dashboard.index', [
            'totalSales' => 0,
            'transactionCount' => 0,
            'productCount' => $productCount,
            'salesData' => collect([]),
            'recentTransactions' => collect([])
        ]);
    }
}

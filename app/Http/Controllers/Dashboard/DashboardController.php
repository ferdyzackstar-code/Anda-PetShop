<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $totalUsers = User::count();
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();

        $totalProducts = Product::count();
        $totalSpecies = Category::whereNull('parent_id')->count(); 
        $totalCategories = Category::whereNotNull('parent_id')->count();    
        $totalSuppliers = Supplier::count();

        $latestOrders = Order::with(['user', 'payment'])
            ->latest()
            ->take(5)
            ->get();

        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
            ->select('products.id', 'products.name', 'products.image', DB::raw('SUM(order_items.qty) as total_qty'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('products.id', 'products.name', 'products.image')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $topKasirs = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
            ->select('users.id', 'users.name', 'users.image', DB::raw('COUNT(orders.id) as total_transactions'), DB::raw('SUM(orders.total_amount) as total_revenue'))
            ->groupBy('users.id', 'users.name', 'users.image')
            ->orderByDesc('total_transactions')
            ->take(5)
            ->get();

        $latestPurchases = Purchase::with('supplier')->latest()->take(5)->get();

        $lowStockProducts = Product::with('category')->where('status', 'active')->where('stock', '<=', 10)->orderBy('stock')->take(8)->get();

        $topSuppliers = DB::table('purchases')
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->where('purchases.status', 'received')
            ->whereBetween('purchases.purchase_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->select('suppliers.id', 'suppliers.name', DB::raw('COUNT(purchases.id) as total_purchases'), DB::raw('SUM(purchases.total_amount) as total_value'))
            ->groupBy('suppliers.id', 'suppliers.name')
            ->orderByDesc('total_purchases')
            ->take(5)
            ->get();

        $salesTrend = DB::table('orders')
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(id) as total_orders'), DB::raw('SUM(total_amount) as total_revenue'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $salesChartLabels = [];
        $salesChartOrders = [];
        $salesChartRevenue = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $found = $salesTrend->firstWhere('date', $date);

            $salesChartLabels[] = now()->subDays($i)->format('d M');
            $salesChartOrders[] = $found ? (int) $found->total_orders : 0;
            $salesChartRevenue[] = $found ? (float) $found->total_revenue : 0;
        }

        $orderStatusData = DB::table('orders')->select('status', DB::raw('COUNT(id) as total'))->groupBy('status')->pluck('total', 'status');

        $purchaseBySupplier = DB::table('purchases')->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')->where('purchases.status', 'received')->select('suppliers.name', DB::raw('SUM(purchases.total_amount) as total_value'))->groupBy('suppliers.name')->orderByDesc('total_value')->take(6)->get();

        $stockByCategory = DB::table('products')->join('categories', 'products.category_id', '=', 'categories.id')->join('categories as parent', 'categories.parent_id', '=', 'parent.id')->where('products.status', 'active')->select('parent.name as category_name', DB::raw('SUM(products.stock) as total_stock'))->groupBy('parent.name')->orderByDesc('total_stock')->get();

        return view(
            'dashboard.index',
            compact(
                'totalUsers',
                'totalRoles',
                'totalPermissions',
                'totalProducts',
                'totalSpecies',
                'totalCategories',
                'totalSuppliers',
                'latestOrders',
                'topProducts',
                'topKasirs',
                'latestPurchases',
                'lowStockProducts',
                'topSuppliers',
                'salesChartLabels',
                'salesChartOrders',
                'salesChartRevenue',
                'orderStatusData',
                'purchaseBySupplier',
                'stockByCategory',
            ),
        );
    }
}

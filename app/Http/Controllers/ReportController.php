<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. SIAPKAN SARINGAN UTAMA (Query Builder)
        $query = Order::with(['user', 'payment']);

        // 2. PROSES PENYARINGAN BERDASARKAN REQUEST (Filter)
        // Jika ada filter tanggal...
        $query->when($request->start_date && $request->end_date, function ($q) use ($request) {
            return $q->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        });

        // Jika ada filter Kasir...
        $query->when($request->kasir_id, function ($q) use ($request) {
            return $q->where('user_id', $request->kasir_id);
        });

        // Jika ada filter Status (Completed, Pending, Cancelled)...
        $query->when($request->status, function ($q) use ($request) {
            return $q->where('status', $request->status);
        });

        // Jika ada filter Metode Pembayaran (Cash/Transfer)...
        $query->when($request->payment_method, function ($q) use ($request) {
            // Karena payment_method ada di tabel payments, kita gunakan whereHas
            return $q->whereHas('payment', function ($p) use ($request) {
                $p->where('payment_method', $request->payment_method);
            });
        });

        // AMBIL DATA YANG SUDAH DISARING
        $orders = $query->latest()->get();

        // 3. HITUNG-HITUNGAN UNTUK GRAFIK (CHART)

        // A. Trend Metode Pembayaran (Berapa Cash, Berapa Transfer)
        $paymentTrend = $orders
            ->groupBy(function ($order) {
                return $order->payment ? $order->payment->payment_method : 'unknown';
            })
            ->map->count();

        // B. Kasir Paling Aktif (Hitung transaksi per kasir)
        $kasirTrend = $orders
            ->groupBy(function ($order) {
                return $order->user ? $order->user->name : 'Unknown';
            })
            ->map->count();

        // C. Top 5 Produk Terlaris (Berdasarkan filter tanggal yang sama)
        $topProducts = OrderItem::with('product')
            ->whereHas('order', function ($q) use ($request) {
                // Terapkan filter tanggal agar akurat dengan laporan
                if ($request->start_date && $request->end_date) {
                    $q->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
                }
            })
            ->select('product_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(qty * price) as omset'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Siapkan data kasir untuk dropdown filter di Blade
        $kasirs = User::all(); // Sesuaikan jika kamu punya role khusus kasir

        return view('dashboard.reports.index', compact('orders', 'paymentTrend', 'kasirTrend', 'topProducts', 'kasirs'));
    }

    public function exportPdf(Request $request)
    {
        // Panggil ulang saringan yang sama persis seperti di atas
        // (Bisa dipisahkan ke function private agar tidak mengulang kode, tapi begini lebih mudah dipahami dulu)
        $query = Order::with(['user', 'payment']);
        // ... (Kopi paste logika $query->when() dari atas ke sini) ...

        $orders = $query->latest()->get();

        // Kirim ke view khusus PDF
        $pdf = Pdf::loadView('dashboard.reports.pdf', compact('orders'));

        // Download file PDF
        return $pdf->download('Laporan_Transaksi.pdf');
    }
}

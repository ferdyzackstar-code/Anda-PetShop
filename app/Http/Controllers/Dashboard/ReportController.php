<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Transaction;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    public function exportPdf(Request $request)
    {
        $type = $request->get('type', 'index'); // Default ke index jika tidak ada parameter type
        $date = now()->format('d F Y');

        // Ambil input filter dari request (sama dengan yang ada di index dashboard)
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $outletId = $request->get('outlet_id');

        // Logic pemilihan data
        if ($type == 'index') {
            // Query Dasar
            $query = Transaction::with(['outlet', 'user']);

            // Filter Tanggal
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }

            // Filter Outlet
            if ($outletId) {
                $query->where('outlet_id', $outletId);
            }

            $transactions = $query->latest()->get();
            $totalRevenue = $transactions->sum('total_price');
            $view = 'dashboard.reports.pdf_index';
        } elseif ($type == 'summary') {
            $query = Transaction::query();
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }

            $transactions = $query->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total_transactions'), DB::raw('SUM(total_price) as total_revenue'))->groupBy('date')->orderBy('date', 'desc')->get();

            $totalRevenue = $transactions->sum('total_revenue');
            $view = 'dashboard.reports.pdf_summary';
        } elseif ($type == 'outlet') {
            // Untuk laporan outlet, kita hitung transaksi dalam range tanggal tersebut
            $transactions = Outlet::withCount([
                'transactions' => function ($q) use ($startDate, $endDate) {
                    if ($startDate && $endDate) {
                        $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                    }
                },
            ])
                ->withSum(
                    [
                        'transactions' => function ($q) use ($startDate, $endDate) {
                            if ($startDate && $endDate) {
                                $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                            }
                        },
                    ],
                    'total_price',
                )
                ->get();

            $totalRevenue = $transactions->sum('transactions_sum_total_price');
            $view = 'dashboard.reports.pdf_outlet';
        } elseif ($type == 'employee') {
            $transactions = User::withCount([
                'transactions' => function ($q) use ($startDate, $endDate) {
                    if ($startDate && $endDate) {
                        $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                    }
                },
            ])
                ->withSum(
                    [
                        'transactions' => function ($q) use ($startDate, $endDate) {
                            if ($startDate && $endDate) {
                                $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                            }
                        },
                    ],
                    'total_price',
                )
                ->get();

            $totalRevenue = $transactions->sum('transactions_sum_total_price');
            $view = 'dashboard.reports.pdf_employee';
        }

        // Generate PDF
        $pdf = Pdf::loadView($view, compact('transactions', 'date', 'totalRevenue', 'startDate', 'endDate'));

        return $pdf->download("laporan-{$type}-" . now()->format('Ymd') . '.pdf');
    }

    // Method untuk Ringkasan Penjualan
    public function summary(Request $request)
    {
        if ($request->ajax()) {
            // Mengelompokkan transaksi berdasarkan tanggal
            $data = Transaction::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total_transactions'), DB::raw('SUM(total_price) as total_revenue'))->groupBy('date')->orderBy('date', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('formatted_date', function ($row) {
                    return date('d M Y', strtotime($row->date));
                })
                ->addColumn('revenue', function ($row) {
                    return 'Rp ' . number_format($row->total_revenue, 0, ',', '.');
                })
                ->make(true);
        }

        // Hitung total keseluruhan untuk widget di atas tabel
        $totalAllTime = Transaction::sum('total_price');
        $transactionCount = Transaction::count();

        return view('dashboard.reports.summary', compact('totalAllTime', 'transactionCount'));
    }

    // Untuk Laporan Per Outlet
    public function outlet(Request $request)
    {
        if ($request->ajax()) {
            $data = Outlet::withCount('transactions')->withSum('transactions', 'total_price')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('total_omzet', function ($row) {
                    return 'Rp ' . number_format($row->transactions_sum_total_price ?? 0, 0, ',', '.');
                })
                ->make(true);
        }

        return view('dashboard.reports.outlet');
    }

    // Untuk Laporan Karyawan
    public function employee(Request $request)
    {
        if ($request->ajax()) {
            $data = User::withCount('transactions')->withSum('transactions', 'total_price')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('total_uang', function ($row) {
                    return 'Rp ' . number_format($row->transactions_sum_total_price ?? 0, 0, ',', '.');
                })
                ->addColumn('status', function ($row) {
                    $badge = $row->transactions_count > 0 ? 'badge-success' : 'badge-secondary';
                    $text = $row->transactions_count > 0 ? 'Aktif' : 'Pasif';
                    return '<span class="badge ' . $badge . '">' . $text . '</span>';
                })
                ->rawColumns(['status'])
                ->make(true);
        }

        return view('dashboard.reports.employee');
    }
}

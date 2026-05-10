<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    // =========================================================
    //  HELPER: Bangun query Order berdasarkan filter
    // =========================================================
    private function buildOrderQuery(array $filters)
    {
        $query = Order::with(['user', 'payment'])->whereBetween('created_at', [$filters['start_date'] . ' 00:00:00', $filters['end_date'] . ' 23:59:59']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['kasir_id'])) {
            $query->where('user_id', $filters['kasir_id']);
        }
        if (!empty($filters['payment_method'])) {
            $query->whereHas('payment', fn($q) => $q->where('payment_method', $filters['payment_method']));
        }

        return $query->oldest();
    }

    // =========================================================
    //  HELPER: Hitung totals dari koleksi order
    // =========================================================
    private function calcTotals($orders): array
    {
        return [
            'completed' => $orders->where('status', 'completed')->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
            'cash' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
            'transfer' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
            'total_trx' => $orders->count(),
            'revenue' => $orders->sum('total_amount'),
        ];
    }

    // =========================================================
    //  HELPER: Hitung cashierData dari koleksi order
    // =========================================================
    private function calcCashierData($orders)
    {
        return $orders
            ->groupBy('user_id')
            ->map(
                fn($group) => [
                    'name' => $group->first()->user->name ?? 'Unknown',
                    'count' => $group->count(),
                ],
            )
            ->sortByDesc('count')
            ->values();
    }

    // =========================================================
    //  HELPER: Hitung pieData dari koleksi order
    // =========================================================
    private function calcPieData($orders): array
    {
        return [
            'cash' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
            'transfer' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
        ];
    }

    // =========================================================
    //  PER-JAM
    // =========================================================
    public function hourlyReport(Request $request)
    {
        // PRG: jika POST → simpan filter ke session lalu redirect ke GET
        if ($request->isMethod('POST')) {
            $request->validate(
                [
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after_or_equal:start_date',
                ],
                [
                    'start_date.required' => 'Mulai tanggal harus diisi!',
                    'start_date.date' => 'Mulai tanggal tidak valid!',
                    'end_date.required' => 'Sampai tanggal harus diisi!',
                    'end_date.date' => 'Sampai tanggal tidak valid!',
                    'end_date.after_or_equal' => 'Sampai tanggal harus sama atau setelah mulai tanggal!',
                ],
            );

            session()->flash('report_filter', [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
                'payment_method' => $request->payment_method,
                'kasir_id' => $request->kasir_id,
            ]);

            return redirect()->route('dashboard.reports.hourly');
        }

        // GET: baca filter dari session flash (jika ada) atau default hari ini
        $filter = session('report_filter', []);

        $startDate = $filter['start_date'] ?? date('Y-m-d');
        $endDate = $filter['end_date'] ?? date('Y-m-d');
        $statusFilter = $filter['status'] ?? null;
        $methodFilter = $filter['payment_method'] ?? null;
        $kasirFilter = $filter['kasir_id'] ?? null;

        $kasirs = User::role('kasir')->get();
        $orders = $this->buildOrderQuery([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $statusFilter,
            'kasir_id' => $kasirFilter,
            'payment_method' => $methodFilter,
        ])->get();

        $hourlyData = $orders->groupBy(fn($o) => Carbon::parse($o->created_at)->format('H:00'));

        $tableData = [];
        foreach ($hourlyData as $hour => $hourOrders) {
            $tableData[] = [
                'hour_raw' => $hour,
                'hour_formatted' => $hour,
                'completed' => $hourOrders->where('status', 'completed')->count(),
                'pending' => $hourOrders->where('status', 'pending')->count(),
                'cancelled' => $hourOrders->where('status', 'cancelled')->count(),
                'cash' => $hourOrders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
                'transfer' => $hourOrders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
                'total_trx' => $hourOrders->count(),
                'revenue' => $hourOrders->sum('total_amount'),
            ];
        }

        $tableData = collect($tableData)->sortBy('hour_raw')->values();
        $totals = $this->calcTotals($orders);

        $totalTransaksiKeseluruhan = $orders->count();
        $totalKeuntunganKeseluruhan = $orders->sum('total_amount');

        $peakHourRow = collect($tableData)->sortByDesc('total_trx')->first();
        $peakHourName = $peakHourRow ? $peakHourRow['hour_formatted'] : '-';
        $peakHourTrxCount = $peakHourRow ? $peakHourRow['total_trx'] : 0;

        $chartHours = $tableData->pluck('hour_raw');
        $chartVolume = $tableData->pluck('total_trx');

        $chartStatusCompleted = [];
        $chartStatusPending = [];
        $chartStatusCancelled = [];
        foreach ($tableData as $row) {
            $g = $hourlyData[$row['hour_raw']];
            $chartStatusCompleted[] = $g->where('status', 'completed')->count();
            $chartStatusPending[] = $g->where('status', 'pending')->count();
            $chartStatusCancelled[] = $g->where('status', 'cancelled')->count();
        }

        $pieData = $this->calcPieData($orders);
        $cashierData = $this->calcCashierData($orders);

        return view('dashboard.reports.hourly', compact('tableData', 'startDate', 'endDate', 'statusFilter', 'methodFilter', 'kasirFilter', 'kasirs', 'totalTransaksiKeseluruhan', 'totalKeuntunganKeseluruhan', 'peakHourName', 'peakHourTrxCount', 'chartHours', 'chartVolume', 'chartStatusCompleted', 'chartStatusPending', 'chartStatusCancelled', 'pieData', 'cashierData', 'totals'));
    }

    // =========================================================
    //  HARIAN
    // =========================================================
    public function dailyReport(Request $request)
    {
        if ($request->isMethod('POST')) {
            $request->validate(
                [
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after_or_equal:start_date',
                ],
                [
                    'start_date.required' => 'Mulai tanggal harus diisi!',
                    'start_date.date' => 'Mulai tanggal tidak valid!',
                    'end_date.required' => 'Sampai tanggal harus diisi!',
                    'end_date.date' => 'Sampai tanggal tidak valid!',
                    'end_date.after_or_equal' => 'Sampai tanggal harus sama atau setelah mulai tanggal!',
                ],
            );

            session()->flash('report_filter', [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
                'payment_method' => $request->payment_method,
                'kasir_id' => $request->kasir_id,
            ]);

            return redirect()->route('dashboard.reports.daily');
        }

        $filter = session('report_filter', []);

        $startDate = $filter['start_date'] ?? date('Y-m-01');
        $endDate = $filter['end_date'] ?? date('Y-m-t');
        $statusFilter = $filter['status'] ?? null;
        $methodFilter = $filter['payment_method'] ?? null;
        $kasirFilter = $filter['kasir_id'] ?? null;

        $kasirs = User::role('kasir')->get();
        $orders = $this->buildOrderQuery([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $statusFilter,
            'kasir_id' => $kasirFilter,
            'payment_method' => $methodFilter,
        ])->get();

        $dailyData = $orders->groupBy(fn($o) => Carbon::parse($o->created_at)->format('Y-m-d'));

        $tableData = [];
        foreach ($dailyData as $date => $dayOrders) {
            $tableData[] = [
                'date_raw' => $date,
                'date_formatted' => Carbon::parse($date)->translatedFormat('l, d F Y'),
                'completed' => $dayOrders->where('status', 'completed')->count(),
                'pending' => $dayOrders->where('status', 'pending')->count(),
                'cancelled' => $dayOrders->where('status', 'cancelled')->count(),
                'cash' => $dayOrders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
                'transfer' => $dayOrders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
                'total_trx' => $dayOrders->count(),
                'revenue' => $dayOrders->sum('total_amount'),
            ];
        }

        $tableData = collect($tableData)->sortBy('date_raw')->values();
        $totals = $this->calcTotals($orders);

        $totalTransaksiKeseluruhan = $orders->count();
        $totalKeuntunganKeseluruhan = $orders->sum('total_amount');

        $peakDateRow = collect($tableData)->sortByDesc('total_trx')->first();
        $peakDateName = $peakDateRow ? $peakDateRow['date_formatted'] : '-';
        $peakDateTrxCount = $peakDateRow ? $peakDateRow['total_trx'] : 0;

        $chartDates = $tableData->pluck('date_raw')->map(fn($d) => Carbon::parse($d)->translatedFormat('d F'));
        $chartVolume = $tableData->pluck('total_trx');

        $chartStatusCompleted = [];
        $chartStatusPending = [];
        $chartStatusCancelled = [];
        foreach ($tableData as $row) {
            $g = $dailyData[$row['date_raw']];
            $chartStatusCompleted[] = $g->where('status', 'completed')->count();
            $chartStatusPending[] = $g->where('status', 'pending')->count();
            $chartStatusCancelled[] = $g->where('status', 'cancelled')->count();
        }

        $pieData = $this->calcPieData($orders);
        $cashierData = $this->calcCashierData($orders);

        return view('dashboard.reports.daily', compact('tableData', 'startDate', 'endDate', 'statusFilter', 'methodFilter', 'kasirFilter', 'kasirs', 'totalTransaksiKeseluruhan', 'totalKeuntunganKeseluruhan', 'peakDateName', 'peakDateTrxCount', 'chartDates', 'chartVolume', 'chartStatusCompleted', 'chartStatusPending', 'chartStatusCancelled', 'pieData', 'cashierData', 'totals'));
    }

    // =========================================================
    //  BULANAN
    // =========================================================
    public function monthlyReport(Request $request)
    {
        if ($request->isMethod('POST')) {
            $request->validate(
                [
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after_or_equal:start_date',
                ],
                [
                    'start_date.required' => 'Mulai tanggal harus diisi!',
                    'start_date.date' => 'Mulai tanggal tidak valid!',
                    'end_date.required' => 'Sampai tanggal harus diisi!',
                    'end_date.date' => 'Sampai tanggal tidak valid!',
                    'end_date.after_or_equal' => 'Sampai tanggal harus sama atau setelah mulai tanggal!',
                ],
            );

            session()->flash('report_filter', [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
                'payment_method' => $request->payment_method,
                'kasir_id' => $request->kasir_id,
            ]);

            return redirect()->route('dashboard.reports.monthly');
        }

        $filter = session('report_filter', []);

        $startDate = $filter['start_date'] ?? date('Y-01-01');
        $endDate = $filter['end_date'] ?? date('Y-12-31');
        $statusFilter = $filter['status'] ?? null;
        $methodFilter = $filter['payment_method'] ?? null;
        $kasirFilter = $filter['kasir_id'] ?? null;

        $kasirs = User::role('kasir')->get();
        $orders = $this->buildOrderQuery([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $statusFilter,
            'kasir_id' => $kasirFilter,
            'payment_method' => $methodFilter,
        ])->get();

        $monthlyData = $orders->groupBy(fn($o) => Carbon::parse($o->created_at)->format('Y-m'));

        $tableData = [];
        foreach ($monthlyData as $month => $monthOrders) {
            $tableData[] = [
                'month_raw' => $month,
                'month_formatted' => Carbon::parse($month)->translatedFormat('F Y'),
                'completed' => $monthOrders->where('status', 'completed')->count(),
                'pending' => $monthOrders->where('status', 'pending')->count(),
                'cancelled' => $monthOrders->where('status', 'cancelled')->count(),
                'cash' => $monthOrders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
                'transfer' => $monthOrders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
                'total_trx' => $monthOrders->count(),
                'revenue' => $monthOrders->sum('total_amount'),
            ];
        }

        $tableData = collect($tableData)->sortBy('month_raw')->values();
        $totals = $this->calcTotals($orders);

        $totalTransaksiKeseluruhan = $orders->count();
        $totalKeuntunganKeseluruhan = $orders->sum('total_amount');

        $peakMonthRow = collect($tableData)->sortByDesc('total_trx')->first();
        $peakMonthName = $peakMonthRow ? $peakMonthRow['month_formatted'] : '-';
        $peakMonthTrxCount = $peakMonthRow ? $peakMonthRow['total_trx'] : 0;

        $chartMonths = $tableData->pluck('month_raw')->map(fn($m) => Carbon::parse($m)->translatedFormat('F Y'));
        $chartVolume = $tableData->pluck('total_trx');

        $chartStatusCompleted = [];
        $chartStatusPending = [];
        $chartStatusCancelled = [];
        foreach ($tableData as $row) {
            $g = $monthlyData[$row['month_raw']];
            $chartStatusCompleted[] = $g->where('status', 'completed')->count();
            $chartStatusPending[] = $g->where('status', 'pending')->count();
            $chartStatusCancelled[] = $g->where('status', 'cancelled')->count();
        }

        $pieData = $this->calcPieData($orders);
        $cashierData = $this->calcCashierData($orders);

        return view('dashboard.reports.monthly', compact('tableData', 'startDate', 'endDate', 'statusFilter', 'methodFilter', 'kasirFilter', 'kasirs', 'totalTransaksiKeseluruhan', 'totalKeuntunganKeseluruhan', 'peakMonthName', 'peakMonthTrxCount', 'chartMonths', 'chartVolume', 'chartStatusCompleted', 'chartStatusPending', 'chartStatusCancelled', 'pieData', 'cashierData', 'totals'));
    }

    // =========================================================
    //  EXPORT PDF (tetap GET, tidak berubah)
    // =========================================================
    public function exportHourlyPdf(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-d');
        $endDate = $request->end_date ?? date('Y-m-d');
        $statusFilter = $request->status;
        $methodFilter = $request->payment_method;
        $kasirFilter = $request->kasir_id;
        $kasirs = User::role('kasir')->get();

        $orders = $this->buildOrderQuery([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $statusFilter,
            'kasir_id' => $kasirFilter,
            'payment_method' => $methodFilter,
        ])->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal Export: Tidak ada data transaksi pada filter yang dipilih.');
        }

        $hourlyData = $orders->groupBy(fn($o) => Carbon::parse($o->created_at)->format('H:00'));
        $tableData = [];
        foreach ($hourlyData as $hour => $hourOrders) {
            $tableData[] = [
                'hour_raw' => $hour,
                'hour_formatted' => $hour,
                'completed' => $hourOrders->where('status', 'completed')->count(),
                'pending' => $hourOrders->where('status', 'pending')->count(),
                'cancelled' => $hourOrders->where('status', 'cancelled')->count(),
                'cash' => $hourOrders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
                'transfer' => $hourOrders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
                'total_trx' => $hourOrders->count(),
                'revenue' => $hourOrders->sum('total_amount'),
            ];
        }

        $tableData = collect($tableData)->sortBy('hour_raw')->values();
        $totals = $this->calcTotals($orders);
        $totalKeuntunganKeseluruhan = $orders->sum('total_amount');

        $pdf = Pdf::loadView('dashboard.reports.pdf_hourly', compact('tableData', 'startDate', 'endDate', 'statusFilter', 'methodFilter', 'kasirFilter', 'kasirs', 'totalKeuntunganKeseluruhan', 'totals'))->setPaper('a4', 'portrait');

        return $pdf->download("Laporan_PerJam_{$startDate}_sampai_{$endDate}.pdf");
    }

    public function exportDailyPdf(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-t');
        $statusFilter = $request->status;
        $methodFilter = $request->payment_method;
        $kasirFilter = $request->kasir_id;
        $kasirs = User::role('kasir')->get();

        $orders = $this->buildOrderQuery([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $statusFilter,
            'kasir_id' => $kasirFilter,
            'payment_method' => $methodFilter,
        ])->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal Export: Tidak ada data transaksi pada filter yang dipilih.');
        }

        $dailyData = $orders->groupBy(fn($o) => Carbon::parse($o->created_at)->format('Y-m-d'));
        $tableData = [];
        foreach ($dailyData as $date => $dayOrders) {
            $tableData[] = [
                'date_raw' => $date,
                'date_formatted' => Carbon::parse($date)->translatedFormat('l, d F Y'),
                'completed' => $dayOrders->where('status', 'completed')->count(),
                'pending' => $dayOrders->where('status', 'pending')->count(),
                'cancelled' => $dayOrders->where('status', 'cancelled')->count(),
                'cash' => $dayOrders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
                'transfer' => $dayOrders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
                'total_trx' => $dayOrders->count(),
                'revenue' => $dayOrders->sum('total_amount'),
            ];
        }

        $tableData = collect($tableData)->sortBy('date_raw')->values();
        $totals = $this->calcTotals($orders);
        $totalKeuntunganKeseluruhan = $orders->sum('total_amount');

        $pdf = Pdf::loadView('dashboard.reports.pdf_daily', compact('tableData', 'startDate', 'endDate', 'statusFilter', 'methodFilter', 'kasirFilter', 'kasirs', 'totalKeuntunganKeseluruhan', 'totals'))->setPaper('a4', 'portrait');

        return $pdf->download("Laporan_Harian_{$startDate}_sampai_{$endDate}.pdf");
    }

    public function exportMonthlyPdf(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-01-01');
        $endDate = $request->end_date ?? date('Y-12-31');
        $statusFilter = $request->status;
        $methodFilter = $request->payment_method;
        $kasirFilter = $request->kasir_id;
        $kasirs = User::role('kasir')->get();

        $orders = $this->buildOrderQuery([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $statusFilter,
            'kasir_id' => $kasirFilter,
            'payment_method' => $methodFilter,
        ])->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal Export: Tidak ada data transaksi pada filter yang dipilih.');
        }

        $monthlyData = $orders->groupBy(fn($o) => Carbon::parse($o->created_at)->format('Y-m'));
        $tableData = [];
        foreach ($monthlyData as $month => $monthOrders) {
            $tableData[] = [
                'month_raw' => $month,
                'month_formatted' => Carbon::parse($month)->translatedFormat('F Y'),
                'completed' => $monthOrders->where('status', 'completed')->count(),
                'pending' => $monthOrders->where('status', 'pending')->count(),
                'cancelled' => $monthOrders->where('status', 'cancelled')->count(),
                'cash' => $monthOrders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
                'transfer' => $monthOrders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
                'total_trx' => $monthOrders->count(),
                'revenue' => $monthOrders->sum('total_amount'),
            ];
        }

        $tableData = collect($tableData)->sortBy('month_raw')->values();
        $totals = $this->calcTotals($orders);
        $totalKeuntunganKeseluruhan = $orders->sum('total_amount');

        $pdf = Pdf::loadView('dashboard.reports.pdf_monthly', compact('tableData', 'startDate', 'endDate', 'totalKeuntunganKeseluruhan', 'totals'))->setPaper('a4', 'portrait');

        return $pdf->download("Laporan_Bulanan_{$startDate}_sampai_{$endDate}.pdf");
    }
}

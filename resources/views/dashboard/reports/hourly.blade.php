@extends('dashboard.layouts.admin')

@section('content')
    <div class="container-fluid py-4">

        <div class="row align-items-center mb-4">
            <div class="col">
                <h4 class="font-weight-bold mb-0">
                    <i class="fas fa-list-check text-primary mr-2"></i> Laporan Transaksi Per-Jam
                </h4>
            </div>
            <div class="col-auto">
                <a href="{{ route('dashboard.reports.hourly.export', request()->all()) }}" class="btn btn-danger shadow-sm">
                    <i class="fas fa-file-pdf fa-sm mr-1"></i> Export PDF
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form action="{{ route('dashboard.reports.hourly') }}" method="GET">
                    <div class="row align-items-end">

                        <div class="col-md-2 form-group mb-md-0">
                            <label class="text-muted small font-weight-bold mb-1">Mulai Tanggal</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>

                        <div class="col-md-2 form-group mb-md-0">
                            <label class="text-muted small font-weight-bold mb-1">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>

                        <div class="col-md-2 form-group mb-md-0">
                            <label class="text-muted small font-weight-bold mb-1">Status</label>
                            <select name="status" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="completed" {{ $statusFilter == 'completed' ? 'selected' : '' }}>Completed
                                </option>
                                <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="cancelled" {{ $statusFilter == 'cancelled' ? 'selected' : '' }}>Cancelled
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2 form-group mb-md-0">
                            <label class="text-muted small font-weight-bold mb-1">Metode</label>
                            <select name="payment_method" class="form-control">
                                <option value="">Semua Metode</option>
                                <option value="cash" {{ $methodFilter == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="transfer" {{ $methodFilter == 'transfer' ? 'selected' : '' }}>Transfer
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2 form-group mb-md-0">
                            <label class="text-muted small font-weight-bold mb-1">Kasir</label>
                            <select name="kasir_id" class="form-control">
                                <option value="">Semua Kasir</option>
                                @foreach ($kasirs as $ksr)
                                    <option value="{{ $ksr->id }}" {{ $kasirFilter == $ksr->id ? 'selected' : '' }}>
                                        {{ $ksr->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm">
                                <i class="fas fa-filter mr-1"></i> Terapkan
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <p class="small font-weight-bold mb-1">JAM PALING RAMAI</p>
                                <h3 class="mb-0 font-weight-bold">{{ $peakHour }}</h3>
                                <small class="text-white-50">{{ $peakTrxCount }} Transaksi terjadi</small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-fire fa-3x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-info text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <p class="small font-weight-bold mb-1">TOTAL TRANSAKSI</p>
                                <h3 class="mb-0 font-weight-bold">{{ $orders->count() }}</h3>
                                <small class="text-white-50">Dari semua filter dipilih</small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-basket fa-3x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <p class="small font-weight-bold mb-1">TOTAL REVENUE</p>
                                <h3 class="mb-0 font-weight-bold">
                                    Rp{{ number_format($orders->sum('total_amount'), 0, ',', '.') }}</h3>
                                <small class="text-white-50">Periode terpilih</small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-wallet fa-3x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered mb-0 align-middle text-center">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="py-3 border-0">No</th>
                                <th class="py-3 border-0">Waktu</th>
                                <th class="py-3 border-0 text-left">Kasir</th>
                                <th class="py-3 border-0">Status</th>
                                <th class="py-3 border-0">Metode</th>
                                <th class="py-3 border-0 text-right pr-4">Estimasi Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalKeseluruhan = 0; @endphp
                            @forelse($orders as $index => $order)
                                @php $totalKeseluruhan += $order->total_amount; @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><span
                                            class="badge badge-light border">{{ $order->created_at->format('H:i a') }}</span>
                                    </td>
                                    <td class="text-left font-weight-bold">{{ $order->user->name ?? 'N/A' }}</td>
                                    <td>
                                        @if ($order->status == 'completed')
                                            <span class="badge badge-success px-3 py-2">Completed</span>
                                        @elseif($order->status == 'pending')
                                            <span class="badge badge-warning px-3 py-2">Pending</span>
                                        @else
                                            <span class="badge badge-danger px-3 py-2">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (optional($order->payment)->payment_method == 'cash')
                                            <span class="text-success font-weight-bold"><i
                                                    class="fa-solid fa-money-bill-wave"></i> Cash</span>
                                        @elseif(optional($order->payment)->payment_method == 'transfer')
                                            <span class="text-primary font-weight-bold"><i
                                                    class="fa-solid fa-credit-card"></i> Transfer</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-right pr-4 font-weight-bold">Rp
                                        {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-muted py-5">Tidak ada transaksi ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="5" class="text-right py-3 pr-2">ESTIMASI GRAND TOTAL:</td>
                                <td class="text-right pr-4 text-success py-3" style="font-size: 1.1rem;">
                                    Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

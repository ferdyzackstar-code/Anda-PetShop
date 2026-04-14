@extends('dashboard.layouts.admin')

@section('content')
    <div class="container-fluid">

        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('dashboard.reports.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label>Mulai Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Metode</label>
                        <select name="payment_method" class="form-control">
                            <option value="">Semua Metode</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>
                                Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 me-1"><i class="fa fa-filter"></i>
                            Filter</button>
                        <a href="{{ route('dashboard.reports.pdf', request()->all()) }}" class="btn btn-danger w-100"><i
                                class="fa fa-file-pdf"></i> PDF</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6>Metode Pembayaran</h6>
                        <canvas id="paymentChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6>Kasir Teraktif</h6>
                        <canvas id="kasirChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6>Top 5 Produk</h6>
                        <ul class="list-group list-group-flush mt-3">
                            @foreach ($topProducts as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    {{ $item->product->name }}
                                    <span class="badge bg-success rounded-pill text-white">{{ $item->total_qty }} Terjual (Rp
                                        {{ number_format($item->omset, 0, ',', '.') }})</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 1. CHART METODE PEMBAYARAN (Pie Chart)
        const paymentCtx = document.getElementById('paymentChart').getContext('2d');
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                // Ambil nama kunci dari controller (Cash, Transfer)
                labels: {!! json_encode($paymentTrend->keys()) !!},
                datasets: [{
                    // Ambil jumlah datanya
                    data: {!! json_encode($paymentTrend->values()) !!},
                    backgroundColor: ['#28a745', '#17a2b8', '#ffc107']
                }]
            }
        });

        // 2. CHART KASIR TERAKTIF (Bar Chart)
        const kasirCtx = document.getElementById('kasirChart').getContext('2d');
        new Chart(kasirCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($kasirTrend->keys()) !!},
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: {!! json_encode($kasirTrend->values()) !!},
                    backgroundColor: '#0d6efd'
                }]
            }
        });
    </script>
@endpush

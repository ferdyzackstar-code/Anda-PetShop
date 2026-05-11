@extends('dashboard.layouts.admin')

@section('title', 'Laporan Per-Jam — Anda Petshop')

@section('content')

    <div class="card w-100 border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-4 bg-primary rounded d-flex flex-wrap align-items-center justify-content-between">
            <h5 class="mb-0 text-white font-weight-bold ">
                <i class="fas fa-calendar-day mr-2"></i> Laporan Transaksi Per-Jam
            </h5>
            @php
                $exportUrl = route(
                    'dashboard.reports.hourly.export',
                    array_filter([
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'status' => $statusFilter,
                        'payment_method' => $methodFilter,
                        'kasir_id' => $kasirFilter,
                    ]),
                );
            @endphp
            <a href="{{ $exportUrl }}" class="btn btn-danger btn-sm mt-1 mt-sm-0">
                <i class="fas fa-file-pdf mr-1"></i> Export PDF
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('dashboard.reports.hourly') }}" method="POST">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-2 form-group mb-2 mb-md-0">
                        <label class="font-weight-bold text-gray-700 small">Mulai Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-2 form-group mb-2 mb-md-0">
                        <label class="font-weight-bold text-gray-700 small">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-2 form-group mb-2 mb-md-0">
                        <label class="font-weight-bold text-gray-700 small">Status Pembayaran</label>
                        <select name="status" class="form-control">
                            <option value="">Semua</option>
                            <option value="completed" {{ $statusFilter == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="cancelled" {{ $statusFilter == 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group mb-2 mb-md-0">
                        <label class="font-weight-bold text-gray-700 small">Metode</label>
                        <select name="payment_method" class="form-control">
                            <option value="">Semua</option>
                            <option value="cash" {{ $methodFilter == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ $methodFilter == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group mb-2 mb-md-0">
                        <label class="font-weight-bold text-gray-700 small">Kasir</label>
                        <select name="kasir_id" class="form-control">
                            <option value="">Semua Kasir</option>
                            @foreach ($kasirs as $ksr)
                                <option value="{{ $ksr->id }}" {{ $kasirFilter == $ksr->id ? 'selected' : '' }}>
                                    {{ $ksr->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card shadow-sm bg-primary text-white h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <p class="small font-weight-bold mb-1 text-uppercase text-white-50">Jam Paling Ramai</p>
                            <h4 class="mb-0 font-weight-bold">{{ $peakHourName ?? '-' }}</h4>
                            <div class="small mt-1">{{ $peakHourTrxCount ?? 0 }} Transaksi</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-fire fa-3x text-white-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card shadow-sm bg-info text-white h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <p class="small font-weight-bold mb-1 text-uppercase text-white-50">Total Transaksi</p>
                            <h3 class="mb-0 font-weight-bold">{{ $totalTransaksiKeseluruhan ?? 0 }}</h3>
                            <div class="small mt-1">Berdasarkan Filter</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-shopping-basket fa-3x text-white-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm bg-success text-white h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <p class="small font-weight-bold mb-1 text-uppercase text-white-50">Estimasi Keuntungan</p>
                            <h3 class="mb-0 font-weight-bold">
                                Rp {{ number_format($totalKeuntunganKeseluruhan, 0, ',', '.') }}
                            </h3>
                            <div class="small mt-1">Berdasarkan Filter</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-hand-holding-usd fa-3x text-white-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line mr-1"></i> Tren Volume Transaksi
                    </h6>
                </div>
                <div class="card-body"><canvas id="volumeChart"></canvas></div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar mr-1"></i> Performa Kasir
                    </h6>
                </div>
                <div class="card-body"><canvas id="cashierChart"></canvas></div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area mr-1"></i> Perbandingan Status Transaksi
                    </h6>
                </div>
                <div class="card-body" style="position:relative; height:250px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie mr-1"></i> Perbandingan Metode Pembayaran
                    </h6>
                </div>
                <div class="card-body" style="position:relative; height:250px;">
                    <canvas id="methodChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table mr-1"></i> Rincian Laporan Per-Jam
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0 text-center">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th rowspan="2" class="align-middle">No</th>
                            <th rowspan="2" class="align-middle">Waktu</th>
                            <th colspan="3">Status</th>
                            <th colspan="2">Metode Pembayaran</th>
                            <th rowspan="2" class="align-middle">Total Transaksi</th>
                            <th rowspan="2" class="align-middle">Estimasi Keuntungan</th>
                        </tr>
                        <tr>
                            <th class="bg-success">Completed</th>
                            <th class="bg-warning">Pending</th>
                            <th class="bg-danger">Cancelled</th>
                            <th class="bg-success">Cash</th>
                            <th class="bg-info">Transfer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tableData as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="badge badge-secondary">{{ $row['hour_formatted'] }}</span></td>
                                <td>{{ $row['completed'] }}</td>
                                <td>{{ $row['pending'] }}</td>
                                <td>{{ $row['cancelled'] }}</td>
                                <td>{{ $row['cash'] }}</td>
                                <td>{{ $row['transfer'] }}</td>
                                <td class="font-weight-bold">{{ $row['total_trx'] }}</td>
                                <td class="font-weight-bold text-success">
                                    Rp {{ number_format($row['revenue'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-muted py-4">Tidak ada transaksi ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-warning text-white font-weight-bold">
                        <tr>
                            <td colspan="2" class="text-center">TOTAL</td>
                            <td>{{ $totals['completed'] }}</td>
                            <td>{{ $totals['pending'] }}</td>
                            <td>{{ $totals['cancelled'] }}</td>
                            <td>{{ $totals['cash'] }}</td>
                            <td>{{ $totals['transfer'] }}</td>
                            <td>{{ $totals['total_trx'] }}</td>
                            <td>Rp {{ number_format($totals['revenue'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('volumeChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($chartHours) !!},
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: {!! json_encode($chartVolume) !!},
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78,115,223,0.1)',
                    fill: true,
                    tension: 0.3
                }]
            }
        });
        new Chart(document.getElementById('cashierChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($cashierData->pluck('name')) !!},
                datasets: [{
                    label: 'Total Transaksi',
                    data: {!! json_encode($cashierData->pluck('count')) !!},
                    backgroundColor: '#36b9cc',
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y'
            }
        });
        new Chart(document.getElementById('statusChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($chartHours) !!},
                datasets: [{
                        label: 'Completed',
                        data: {!! json_encode($chartStatusCompleted) !!},
                        borderColor: '#1cc88a',
                        backgroundColor: 'rgba(28,200,138,0.05)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Pending',
                        data: {!! json_encode($chartStatusPending) !!},
                        borderColor: '#f6c23e',
                        backgroundColor: 'rgba(246,194,62,0.05)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Cancelled',
                        data: {!! json_encode($chartStatusCancelled) !!},
                        borderColor: '#e74a3b',
                        backgroundColor: 'rgba(231,74,59,0.05)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        new Chart(document.getElementById('methodChart'), {
            type: 'pie',
            data: {
                labels: ['Cash', 'Transfer'],
                datasets: [{
                    data: [{{ $pieData['cash'] }}, {{ $pieData['transfer'] }}],
                    backgroundColor: ['#1cc88a', '#4e73df']
                }]
            },
            options: {
                maintainAspectRatio: false
            }
        });
    </script>
@endpush

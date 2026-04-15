@extends('dashboard.layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="fa-solid fa-chart-line fa-fw me-2"></i>Monthly Report (Laporan Bulanan)</h4>
            <a href="{{ route('dashboard.reports.monthly.export', request()->all()) }}" class="btn btn-danger">
                <i class="fa-solid fa-file-pdf fa-fw me-1"></i> Export PDF
            </a>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('dashboard.reports.monthly') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Pilih Tahun</label>
                        <select name="year" class="form-control">
                            @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i>
                            Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold">Grafik Pendapatan Tahun {{ $year }}</div>
                    <div class="card-body">
                        <canvas id="monthlyChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <table class="table table-hover table-bordered mb-0 text-center align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Bulan</th>
                                    <th>Total Transaksi</th>
                                    <th>Total Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalKeseluruhan = 0;
                                    $no = 1;
                                @endphp
                                @foreach ($reportData as $data)
                                    @php $totalKeseluruhan += $data['revenue']; @endphp
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td class="text-start ps-4 fw-bold">{{ $data['month_name'] }}</td>
                                        <td>{{ $data['total_transaksi'] }} Transaksi</td>
                                        <td class="text-success font-weight-bold text-right pe-4">Rp
                                            {{ number_format($data['revenue'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="3" class="text-right text-center">TOTAL REVENUE TAHUN INI:</td>
                                    <td class="text-success fs-5">Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(ctx, {
            type: 'line', // Pakai tipe garis agar terlihat trennya
            data: {
                labels: {!! json_encode(array_column($reportData, 'month_name')) !!},
                datasets: [{
                    label: 'Revenue (Rp)',
                    data: {!! json_encode(array_column($reportData, 'revenue')) !!},
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3 // Bikin garisnya melengkung halus
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endpush

@extends('dashboard.layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="fa-solid fa-calendar-day fa-fw me-2"></i>Daily Report (Laporan Harian)</h4>
            <a href="{{ route('dashboard.reports.daily.export', request()->all()) }}" class="btn btn-danger" target="_blank">
                <i class="fa-solid fa-file-pdf fa-fw me-1"></i> Export PDF
            </a>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('dashboard.reports.daily') }}" method="GET" class="row d-flex align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Pilih Bulan</label>
                        <select name="month" class="form-control">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}"
                                    {{ $month == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Pilih Tahun</label>
                        <select name="year" class="form-control">
                            @for ($y = date('Y'); $y >= date('Y') - 3; $y--)
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

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">
                Data Penjualan Bulan: {{ date('F', mktime(0, 0, 0, $month, 10)) }} {{ $year }}
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0 text-center align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th width="10%">No</th>
                                <th>Tanggal</th>
                                <th>Total Transaksi</th>
                                <th>Revenue (Omset)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalKeseluruhan = 0;
                                $no = 1;
                            @endphp

                            @forelse($dailyData as $date => $data)
                                @php $totalKeseluruhan += $data['revenue']; @endphp
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td class="fw-bold">
                                        {{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') }}</td>
                                    <td><span class="badge bg-info text-white">{{ $data['total_transaksi'] }}
                                            Transaksi</span></td>
                                    <td class="text-success fw-bold">Rp {{ number_format($data['revenue'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-muted py-4">Belum ada transaksi di bulan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-light fw-bold">
                            <tr>
                                <td colspan="3" class="text-end">TOTAL REVENUE BULAN INI:</td>
                                <td class="text-success fs-5">Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

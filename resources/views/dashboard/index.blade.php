@extends('dashboard.layouts.admin')

@section('title', 'Dashboard Utama')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" rel="preload" as="script">
@endpush

@section('content')

<div class="card w-100 border-0 shadow-sm mb-4">
    <div class="card-body py-4 px-4 bg-primary rounded">
        <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:.5rem;">
            <div>
                <h4 class="mb-2 text-white font-weight-bold">
                    <i class="fas fa-paw mr-2"></i> Selamat Datang, {{ Auth::user()->name }}! 
                </h4>
                <p class="mb-2 text-white" style="opacity:.85; font-size:.9rem;">
                    Berikut ringkasan aktivitas <strong>Anda Petshop</strong> hari ini.
                </p>
            </div>
            <span class="badge badge-light text-primary px-3 py-2" style="font-size:.82rem;">
                <i class="fas fa-clock mr-1"></i>
                {{ now()->translatedFormat('l, d F Y — H:i') }} WIB
            </span>
        </div>
    </div>
</div>

    <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('dashboard.index')]]" />

    <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-3">
        <i class="fas fa-users-cog mr-1"></i> Pengguna & Kontrol Akses
    </h6>
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-4 mb-3">
            <div class="card border-left-primary shadow-sm h-100">
                <div class="card-body py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pengguna</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                        <a href="{{ route('dashboard.users.index') }}" class="small text-primary">
                            <i class="fas fa-arrow-right mr-1"></i> Kelola Pengguna
                        </a>
                    </div>
                    <i class="fas fa-users fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 mb-3">
            <div class="card border-left-info shadow-sm h-100">
                <div class="card-body py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Role</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $totalRoles }}</div>
                        <a href="{{ route('dashboard.roles.index') }}" class="small text-info">
                            <i class="fas fa-arrow-right mr-1"></i> Kelola Peran
                        </a>
                    </div>
                    <i class="fas fa-shield-alt fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 mb-3">
            <div class="card border-left-success shadow-sm h-100">
                <div class="card-body py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Hak Akses</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $totalPermissions }}</div>
                        <a href="{{ route('dashboard.permissions.index') }}" class="small text-success">
                            <i class="fas fa-arrow-right mr-1"></i> Kelola Hak Akses
                        </a>
                    </div>
                    <i class="fas fa-key fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>

    <h6 class="font-weight-bold text-success border-bottom pb-2 mb-3">
        <i class="fas fa-boxes mr-1"></i> Informasi Inventori
    </h6>
    <div class="row mb-4">
        <div class="col-6 col-lg-3 mb-3">
            <div class="card border-left-success shadow-sm h-100">
                <div class="card-body py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Produk</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $totalProducts }}</div>
                        <a href="{{ route('dashboard.products.index') }}" class="small text-success">
                            <i class="fas fa-arrow-right mr-1"></i> Kelola
                        </a>
                    </div>
                    <i class="fas fa-box-open fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="card border-left-warning shadow-sm h-100">
                <div class="card-body py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Spesies</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $totalSpecies }}</div>
                        <a href="{{ route('dashboard.categories.index') }}" class="small text-warning">
                            <i class="fas fa-arrow-right mr-1"></i> Kelola
                        </a>
                    </div>
                    <i class="fas fa-paw fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="card border-left-info shadow-sm h-100">
                <div class="card-body py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Kategori</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $totalCategories }}</div>
                        <a href="{{ route('dashboard.categories.index') }}" class="small text-info">
                            <i class="fas fa-arrow-right mr-1"></i> Kelola
                        </a>
                    </div>
                    <i class="fas fa-tags fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="card border-left-danger shadow-sm h-100">
                <div class="card-body py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Supplier</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $totalSuppliers }}</div>
                        <a href="{{ route('dashboard.suppliers.index') }}" class="small text-danger">
                            <i class="fas fa-arrow-right mr-1"></i> Kelola
                        </a>
                    </div>
                    <i class="fas fa-truck fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-chart-bar mr-1"></i> Total Stok per Spesies
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartStockByCategory" style="max-height:240px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-3">
        <i class="fas fa-cash-register mr-1"></i> Informasi Penjualan
    </h6>

    <div class="row mb-4">
        <div class="col-lg-8 mb-3 mb-lg-0">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line mr-1"></i> Tren Penjualan 30 Hari Terakhir
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartSalesTrend" style="max-height:240px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-chart-pie mr-1"></i> Distribusi Status Order
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartOrderStatus" style="max-height:240px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-receipt mr-1"></i> 5 Transaksi Terakhir
                    </h6>
                    <a href="{{ route('dashboard.orders.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($latestOrders->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-receipt fa-2x mb-2 d-block" style="opacity:.3;"></i>
                            Belum ada transaksi
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-3">Kasir</th>
                                        <th>No Invoice</th>
                                        <th>Tanggal</th>
                                        <th>Metode</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($latestOrders as $order)
                                        <tr>
                                            <td class="px-3 align-middle">
                                                <div class="d-flex align-items-center" style="gap:8px;">
                                                    @php
                                                        $imgPath =
                                                            'storage/uploads/users/' .
                                                            ($order->user->image ?? 'default-user.jpg');
                                                        $imgUrl =
                                                            $order->user &&
                                                            $order->user->image &&
                                                            file_exists(public_path($imgPath))
                                                                ? asset($imgPath)
                                                                : asset('storage/uploads/users/default-user.jpg');
                                                    @endphp
                                                    <img src="{{ $imgUrl }}" alt="{{ $order->user->name ?? '' }}"
                                                        style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid #e8ecef;">
                                                    <span
                                                        class="small font-weight-600">{{ $order->user->name ?? '-' }}</span>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <code style="font-size:.78rem;">{{ $order->invoice_number }}</code>
                                            </td>
                                            <td class="align-middle small" style="white-space:nowrap;">
                                                {{ $order->created_at->format('d M Y') }}
                                                <small
                                                    class="text-muted d-block">{{ $order->created_at->format('H:i') }}</small>
                                            </td>
                                            <td class="align-middle">
                                                @if ($order->payment)
                                                    <i
                                                        class="fas {{ $order->payment->payment_method === 'cash' ? 'fa-money-bill-wave text-success' : 'fa-university text-info' }} mr-1"></i>
                                                    <small>{{ ucfirst($order->payment->payment_method) }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="align-middle font-weight-bold small">
                                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                            </td>
                                            <td class="align-middle">
                                                @if ($order->status === 'completed')
                                                    <span class="badge badge-success">Selesai</span>
                                                @elseif ($order->status === 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @else
                                                    <span class="badge badge-danger">Batal</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-6 mb-3 mb-lg-0">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-fire mr-1"></i> Produk Terlaris Bulan Ini
                    </h6>
                    <small class="text-muted">{{ now()->translatedFormat('F Y') }}</small>
                </div>
                <div class="card-body p-0">
                    @forelse ($topProducts as $i => $product)
                        <div class="d-flex align-items-center px-3 py-2 border-bottom" style="gap:10px;">
                            <span
                                class="badge {{ $i === 0 ? 'badge-warning' : ($i === 1 ? 'badge-secondary' : 'badge-light text-dark') }} font-weight-bold"
                                style="min-width:26px;">{{ $i + 1 }}</span>
                            <div style="flex:1; min-width:0;">
                                <div class="small font-weight-bold text-truncate">{{ $product->name }}</div>
                                <div class="text-muted" style="font-size:.72rem;">Rp
                                    {{ number_format($product->total_revenue, 0, ',', '.') }}</div>
                            </div>
                            <span class="badge badge-primary">{{ $product->total_qty }} terjual</span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="fas fa-box-open fa-2x mb-2 d-block" style="opacity:.3;"></i>
                            Belum ada data penjualan bulan ini
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-star mr-1"></i> Kasir Paling Aktif Bulan Ini
                    </h6>
                    <small class="text-muted">{{ now()->translatedFormat('F Y') }}</small>
                </div>
                <div class="card-body p-0">
                    @forelse ($topKasirs as $i => $kasir)
                        <div class="d-flex align-items-center px-3 py-2 border-bottom" style="gap:10px;">
                            <span
                                class="badge {{ $i === 0 ? 'badge-warning' : ($i === 1 ? 'badge-secondary' : 'badge-light text-dark') }} font-weight-bold"
                                style="min-width:26px;">{{ $i + 1 }}</span>
                            @php
                                $kImgPath = 'storage/uploads/users/' . ($kasir->image ?? 'default-user.jpg');
                                $kImgUrl =
                                    $kasir->image && file_exists(public_path($kImgPath))
                                        ? asset($kImgPath)
                                        : asset('storage/uploads/users/default-user.jpg');
                            @endphp
                            <img src="{{ $kImgUrl }}" alt="{{ $kasir->name }}"
                                style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid #e8ecef;">
                            <div style="flex:1; min-width:0;">
                                <div class="small font-weight-bold text-truncate">{{ $kasir->name }}</div>
                                <div class="text-muted" style="font-size:.72rem;">Rp
                                    {{ number_format($kasir->total_revenue, 0, ',', '.') }}</div>
                            </div>
                            <span class="badge badge-success">{{ $kasir->total_transactions }}x</span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="fas fa-user-slash fa-2x mb-2 d-block" style="opacity:.3;"></i>
                            Belum ada data transaksi bulan ini
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <h6 class="font-weight-bold text-info border-bottom pb-2 mb-3">
        <i class="fas fa-shopping-cart mr-1"></i> Informasi Pembelian
    </h6>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-chart-bar mr-1"></i> Nilai Pembelian per Supplier
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartPurchaseBySupplier" style="max-height:240px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-truck mr-1"></i> 5 Pembelian Terakhir
                    </h6>
                    <a href="{{ route('dashboard.purchases.index') }}" class="btn btn-sm btn-outline-info">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($latestPurchases->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-truck fa-2x mb-2 d-block" style="opacity:.3;"></i>
                            Belum ada data pembelian
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-3">No PO</th>
                                        <th>Tanggal</th>
                                        <th>Supplier</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($latestPurchases as $purchase)
                                        <tr>
                                            <td class="px-3 align-middle">
                                                <code style="font-size:.78rem;">{{ $purchase->purchase_number }}</code>
                                            </td>
                                            <td class="align-middle small">
                                                {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}
                                            </td>
                                            <td class="align-middle small">{{ $purchase->supplier->name ?? '-' }}</td>
                                            <td class="align-middle small font-weight-bold">
                                                Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                                            </td>
                                            <td class="align-middle">
                                                @if ($purchase->status === 'received')
                                                    <span class="badge badge-success">Selesai</span>
                                                @elseif ($purchase->status === 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @else
                                                    <span class="badge badge-danger">Batal</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-6 mb-3 mb-lg-0">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Stok Produk Menipis
                    </h6>
                    <span class="badge badge-danger">Stok &le; 10</span>
                </div>
                <div class="card-body p-0">
                    @forelse ($lowStockProducts as $product)
                        <div class="d-flex align-items-center px-3 py-2 border-bottom" style="gap:10px;">
                            <div style="flex:1; min-width:0;">
                                <div class="small font-weight-bold text-truncate">{{ $product->name }}</div>
                                <div class="text-muted" style="font-size:.72rem;">{{ $product->category->name ?? '-' }}
                                </div>
                            </div>
                            <div style="width:80px;">
                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar bg-danger"
                                        style="width:{{ min(100, ($product->stock / 10) * 100) }}%"></div>
                                </div>
                            </div>
                            <span class="badge badge-danger font-weight-bold">{{ $product->stock }}</span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="fas fa-check-circle fa-2x text-success mb-2 d-block"></i>
                            Semua stok dalam kondisi aman 🎉
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-handshake mr-1"></i> Supplier Terbanyak Supply Bulan Ini
                    </h6>
                    <small class="text-muted">{{ now()->translatedFormat('F Y') }}</small>
                </div>
                <div class="card-body p-0">
                    @forelse ($topSuppliers as $i => $supplier)
                        <div class="d-flex align-items-center px-3 py-2 border-bottom" style="gap:10px;">
                            <span
                                class="badge {{ $i === 0 ? 'badge-warning' : ($i === 1 ? 'badge-secondary' : 'badge-light text-dark') }} font-weight-bold"
                                style="min-width:26px;">{{ $i + 1 }}</span>
                            <div style="flex:1; min-width:0;">
                                <div class="small font-weight-bold text-truncate">{{ $supplier->name }}</div>
                                <div class="text-muted" style="font-size:.72rem;">Rp
                                    {{ number_format($supplier->total_value, 0, ',', '.') }}</div>
                            </div>
                            <span class="badge badge-info">{{ $supplier->total_purchases }}x pesanan</span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="fas fa-truck fa-2x mb-2 d-block" style="opacity:.3;"></i>
                            Belum ada data pembelian bulan ini
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            Chart.defaults.font.family = "'Nunito', sans-serif";
            Chart.defaults.font.size = 12;
            Chart.defaults.color = '#888';

            const C = {
                green: '#1cc88a',
                blue: '#4e73df',
                yellow: '#f6c23e',
                red: '#e74a3b',
                purple: '#6f42c1',
                teal: '#20c9a6',
                orange: '#fd7e14',
            };

            new Chart(document.getElementById('chartSalesTrend'), {
                type: 'line',
                data: {
                    labels: @json($salesChartLabels),
                    datasets: [{
                            label: 'Jumlah Order',
                            data: @json($salesChartOrders),
                            borderColor: C.blue,
                            backgroundColor: 'rgba(78,115,223,.08)',
                            borderWidth: 2.5,
                            pointRadius: 3,
                            tension: .4,
                            fill: true,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Revenue (Rp)',
                            data: @json($salesChartRevenue),
                            borderColor: C.yellow,
                            backgroundColor: 'rgba(246,194,62,.06)',
                            borderWidth: 2,
                            pointRadius: 3,
                            tension: .4,
                            fill: true,
                            borderDash: [4, 3],
                            yAxisID: 'y1',
                        },
                    ],
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.datasetIndex === 1 ?
                                    ' Rp ' + ctx.raw.toLocaleString('id-ID') :
                                    ' ' + ctx.raw + ' order',
                            },
                        },
                    },
                    scales: {
                        y: {
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Jumlah Order'
                            }
                        },
                        y1: {
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Revenue (Rp)'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        },
                        x: {
                            ticks: {
                                maxTicksLimit: 10
                            }
                        },
                    },
                },
            });

            const orderStatusData = @json($orderStatusData);
            new Chart(document.getElementById('chartOrderStatus'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(orderStatusData).map(s => s.charAt(0).toUpperCase() + s.slice(1)),
                    datasets: [{
                        data: Object.values(orderStatusData),
                        backgroundColor: [C.green, C.yellow, C.red],
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 6,
                    }],
                },
                options: {
                    responsive: true,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => ` ${ctx.label}: ${ctx.raw} order`
                            }
                        },
                    },
                },
            });

            const stockData = @json($stockByCategory);
            new Chart(document.getElementById('chartStockByCategory'), {
                type: 'bar',
                data: {
                    labels: stockData.map(d => d.category_name),
                    datasets: [{
                        label: 'Total Stok',
                        data: stockData.map(d => d.total_stock),
                        backgroundColor: [C.green, C.blue, C.yellow, C.red, C.purple, C.teal],
                        borderRadius: 6,
                        borderSkipped: false,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => ` Stok: ${ctx.raw} unit`
                            }
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah Stok (unit)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        },
                    },
                },
            });

            const supplierData = @json($purchaseBySupplier);
            new Chart(document.getElementById('chartPurchaseBySupplier'), {
                type: 'bar',
                data: {
                    labels: supplierData.map(d => d.name),
                    datasets: [{
                        label: 'Total Nilai Pembelian (Rp)',
                        data: supplierData.map(d => d.total_value),
                        backgroundColor: [C.blue, C.teal, C.purple, C.red, C.yellow, C.green],
                        borderRadius: 6,
                        borderSkipped: false,
                    }],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID')
                            }
                        },
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: val => 'Rp ' + (val / 1000000).toFixed(1) + 'jt'
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            }
                        },
                    },
                },
            });

        });
    </script>
@endpush

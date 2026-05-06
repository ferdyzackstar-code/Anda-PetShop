@extends('dashboard.layouts.admin')

@section('title', 'Riwayat Transaksi')

@push('styles')
    <style>
        :root {
            --ord-primary: #1565C0;
            --ord-radius: 12px;
        }

        /* ── HEADER ─────────────────────────────────────────────────── */
        .ord-header-card {
            background: linear-gradient(135deg, #0D47A1 0%, #1565C0 60%, #1976D2 100%);
            border-radius: var(--ord-radius);
            padding: 20px 24px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            box-shadow: 0 4px 20px rgba(21, 101, 192, .25);
        }

        .ord-header-card h4 {
            color: #fff;
            font-size: 1.05rem;
            font-weight: 700;
            margin: 0;
        }

        .ord-header-card p {
            color: rgba(255, 255, 255, .7);
            font-size: .82rem;
            margin: 2px 0 0;
        }

        .ord-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-hdr {
            font-size: .82rem;
            font-weight: 700;
            padding: 9px 18px;
            border-radius: 8px;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            position: relative;
            white-space: nowrap;
            border: 1.5px solid rgba(255, 255, 255, .3);
            color: #fff;
        }

        .btn-hdr:hover {
            text-decoration: none;
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-hdr-blue {
            background: rgba(255, 255, 255, .15);
        }

        .btn-hdr-blue:hover {
            background: rgba(255, 255, 255, .28);
        }

        .btn-hdr-yellow {
            background: linear-gradient(135deg, #F57F17, #F9A825);
            border-color: rgba(255, 255, 255, .25);
            box-shadow: 0 3px 12px rgba(245, 127, 23, .35);
        }

        .btn-hdr-yellow:hover {
            background: linear-gradient(135deg, #E65100, #F57F17);
        }

        .pending-badge {
            position: absolute;
            top: -9px;
            right: -9px;
            background: #E53935;
            color: #fff;
            font-size: .62rem;
            font-weight: 800;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
            animation: badge-pop 2s ease infinite;
        }

        @keyframes badge-pop {

            0%,
            100% {
                transform: scale(1)
            }

            50% {
                transform: scale(1.18)
            }
        }

        /* ── STAT CARDS ─────────────────────────────────────────────── */
        .ord-stat-card {
            background: #fff;
            border-radius: var(--ord-radius);
            padding: 16px 20px;
            box-shadow: 0 2px 12px rgba(21, 101, 192, .07);
            display: flex;
            align-items: center;
            gap: 14px;
            border-left: 4px solid transparent;
            transition: all .2s;
            height: 100%;
        }

        .ord-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(21, 101, 192, .12);
        }

        .ord-stat-card .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: #fff;
            flex-shrink: 0;
        }

        .ord-stat-card .stat-val {
            font-size: 1.7rem;
            font-weight: 800;
            color: #1A2332;
            line-height: 1;
        }

        .ord-stat-card .stat-lbl {
            font-size: .72rem;
            color: #7B8FA6;
            text-transform: uppercase;
            letter-spacing: .5px;
            font-weight: 600;
            margin-top: 3px;
        }

        .ord-stat-card.blue {
            border-color: #1565C0;
        }

        .ord-stat-card.green {
            border-color: #2E7D32;
        }

        .ord-stat-card.yellow {
            border-color: #F57F17;
        }

        .ord-stat-card.red {
            border-color: #C62828;
        }

        .bg-blue {
            background: linear-gradient(135deg, #1565C0, #1976D2);
        }

        .bg-green {
            background: linear-gradient(135deg, #2E7D32, #43A047);
        }

        .bg-yellow {
            background: linear-gradient(135deg, #F57F17, #F9A825);
        }

        .bg-red {
            background: linear-gradient(135deg, #C62828, #E53935);
        }

        /* ── TABLE CARD ─────────────────────────────────────────────── */
        .ord-table-card {
            background: #fff;
            border-radius: var(--ord-radius);
            box-shadow: 0 2px 16px rgba(21, 101, 192, .07);
            overflow: hidden;
        }

        .ord-table-header {
            background: linear-gradient(135deg, #1565C0, #1976D2);
            padding: 14px 20px;
        }

        .ord-table-header h6 {
            color: #fff;
            margin: 0;
            font-size: .9rem;
            font-weight: 700;
        }

        .ord-table-card .p-3 {
            padding: 20px !important;
        }

        #orders-table thead th {
            background: #F0F4F8 !important;
            color: #546E7A;
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            border: none !important;
            padding: 12px 14px !important;
            white-space: nowrap;
        }

        #orders-table tbody td {
            padding: 12px 14px !important;
            vertical-align: middle !important;
            border-top: 1px solid #F0F4F8 !important;
            font-size: .84rem;
            color: #2C3E50;
        }

        #orders-table tbody tr:hover {
            background: #F8FAFD;
        }

        #orders-table {
            border-collapse: collapse !important;
        }

        .invoice-code {
            font-family: monospace;
            font-size: .78rem;
            background: #EEF2FF;
            color: #3949AB;
            padding: 3px 8px;
            border-radius: 5px;
            font-weight: 700;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="ord-header-card">
            <div>
                <h4><i class="fas fa-clock-rotate-left mr-2"></i>Riwayat Transaksi</h4>
                <p>Semua transaksi penjualan Anda Petshop</p>
            </div>
            <div class="ord-header-actions">
                @php
                    $pendingCount = \App\Models\Order::where('status', 'pending')->count();
                    $completedCount = \App\Models\Order::where('status', 'completed')->count();
                    $cancelledCount = \App\Models\Order::where('status', 'cancelled')->count();
                    $totalOrders = \App\Models\Order::count();
                @endphp
                <a href="{{ route('dashboard.orders.confirmation') }}" class="btn-hdr btn-hdr-yellow">
                    <i class="fas fa-hourglass-half"></i> Konfirmasi
                    @if ($pendingCount > 0)
                        <span class="pending-badge">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('dashboard.orders.pos') }}" class="btn-hdr btn-hdr-blue">
                    <i class="fas fa-plus-circle"></i> Transaksi Baru
                </a>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="row mb-4">
            <div class="col-12 col-md-6 col-xl-3 mb-3">
                <div class="ord-stat-card blue">
                    <div class="stat-icon bg-blue"><i class="fas fa-receipt"></i></div>
                    <div>
                        <div class="stat-val">{{ $totalOrders }}</div>
                        <div class="stat-lbl">Total Transaksi</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3 mb-3">
                <div class="ord-stat-card yellow">
                    <div class="stat-icon bg-yellow"><i class="fas fa-hourglass-half"></i></div>
                    <div>
                        <div class="stat-val">{{ $pendingCount }}</div>
                        <div class="stat-lbl">Menunggu Konfirmasi</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3 mb-3">
                <div class="ord-stat-card green">
                    <div class="stat-icon bg-green"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <div class="stat-val">{{ $completedCount }}</div>
                        <div class="stat-lbl">Transaksi Selesai</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3 mb-3">
                <div class="ord-stat-card red">
                    <div class="stat-icon bg-red"><i class="fas fa-times-circle"></i></div>
                    <div>
                        <div class="stat-val">{{ $cancelledCount }}</div>
                        <div class="stat-lbl">Transaksi Batal</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="ord-table-card">
            <div class="ord-table-header">
                <h6><i class="fas fa-list mr-2"></i>Daftar Transaksi</h6>
            </div>
            <div class="p-3">
                <div class="table-responsive">
                    <table id="orders-table" class="table table-hover w-100">
                        <thead>
                            <tr>
                                <th width="40px">No</th>
                                <th>Invoice</th>
                                <th>Kasir</th>
                                <th>Tanggal</th>
                                <th>Metode</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th width="100px" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const receiptBaseUrl = "{{ route('dashboard.orders.receipt', ':id') }}";

            $('#orders-table').DataTable({
                autoWidth: false,
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.orders.index') }}",
                language: {
                    processing: '<div class="text-center py-3"><i class="fas fa-spinner fa-spin text-primary mr-2"></i>Memuat data...</div>',
                    emptyTable: '<div class="text-center py-4 text-muted"><i class="fas fa-receipt d-block mb-2" style="font-size:1.8rem;opacity:.3;"></i>Belum ada transaksi</div>',
                    search: '',
                    searchPlaceholder: 'Cari invoice, kasir...',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ transaksi',
                    paginate: {
                        previous: '‹',
                        next: '›'
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'invoice_number',
                        name: 'invoice_number'
                    },
                    {
                        data: 'user.name',
                        name: 'user.name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true
                    },
                    {
                        data: 'payment_method',
                        name: 'payment_method',
                        orderable: false
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                columnDefs: [{
                    targets: [0, 4, 6, 7],
                    className: 'text-center align-middle'
                }, ],
                order: [
                    [3, 'desc']
                ],
                dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6 text-right"f>>rt<"row align-items-center mt-3"<"col-sm-6"i><"col-sm-6"p>>',
            });

            $(document).on('click', '.btn-detail', function() {
                const id = $(this).data('id');
                const url = receiptBaseUrl.replace(':id', id) + '?from=index';
                window.location.href = url;
            });
        });
    </script>
@endpush

{{-- resources/views/dashboard/orders/index.blade.php --}}
@extends('dashboard.layouts.admin')

@section('title', 'Riwayat Transaksi — Anda Petshop')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')

    <div class="card w-100 border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-4 bg-primary rounded d-flex align-items-center justify-content-between flex-wrap"
            style="gap:.5rem;">
            <h5 class="mb-0 text-white font-weight-bold">
                <i class="fas fa-history mr-2"></i> Riwayat Transaksi
            </h5>
            <div class="d-flex flex-wrap" style="gap:.5rem;">
                @php $pendingCount = \App\Models\Order::where('status','pending')->count(); @endphp
                <a href="{{ route('dashboard.orders.confirmation') }}" class="btn btn-warning btn-sm font-weight-bold">
                    <i class="fas fa-hourglass-half mr-1"></i> Konfirmasi
                    @if ($pendingCount > 0)
                        <span class="badge badge-danger ml-1">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('dashboard.orders.pos') }}" class="btn btn-info btn-sm font-weight-bold text-white">
                    <i class="fas fa-cash-register mr-1"></i> Mesin Kasir
                </a>
            </div>
        </div>
    </div>

    <x-breadcrumb :items="[['label' => 'Transaksi', 'url' => route('dashboard.orders.index')]]" />


    @php
        $totalOrders = \App\Models\Order::count();
        $pendingCount = \App\Models\Order::where('status', 'pending')->count();
        $completedCount = \App\Models\Order::where('status', 'completed')->count();
        $cancelledCount = \App\Models\Order::where('status', 'cancelled')->count();
    @endphp

    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="card shadow-sm h-100 border-0 bg-info">
                <div class="card-body py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1" style="opacity:.8;">Total
                            Transaksi</div>
                        <div class="h4 mb-0 font-weight-bold text-white">{{ $totalOrders }}</div>
                    </div>
                    <i class="fas fa-receipt fa-2x text-white" style="opacity:.4;"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card shadow-sm h-100 border-0 bg-warning">
                <div class="card-body py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1" style="opacity:.8;">Menunggu
                            Konfirmasi</div>
                        <div class="h4 mb-0 font-weight-bold text-white">{{ $pendingCount }}</div>
                    </div>
                    <i class="fas fa-hourglass-half fa-2x text-white" style="opacity:.4;"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card shadow-sm h-100 border-0 bg-success">
                <div class="card-body py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1" style="opacity:.8;">Transaksi
                            Selesai</div>
                        <div class="h4 mb-0 font-weight-bold text-white">{{ $completedCount }}</div>
                    </div>
                    <i class="fas fa-check-circle fa-2x text-white" style="opacity:.4;"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card shadow-sm h-100 border-0 bg-danger">
                <div class="card-body py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1" style="opacity:.8;">Transaksi
                            Batal</div>
                        <div class="h4 mb-0 font-weight-bold text-white">{{ $cancelledCount }}</div>
                    </div>
                    <i class="fas fa-times-circle fa-2x text-white" style="opacity:.4;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-1"></i> Daftar Transaksi
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="orders-table">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th width="1%">No</th>
                            <th>Invoice</th>
                            <th>Kasir</th>
                            <th>Tanggal</th>
                            <th>Metode</th>
                            <th>Total</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {

            $('#orders-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                ajax: "{{ route('dashboard.orders.index') }}",
                order: [
                    [3, 'desc']
                ],
                language: {
                    processing: '<i class="fas fa-spinner fa-spin mr-1"></i> Memuat data...',
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    zeroRecords: 'Tidak ada data yang ditemukan',
                    emptyTable: 'Belum ada transaksi',
                    paginate: {
                        first: 'Pertama',
                        previous: 'Sebelumnya',
                        next: 'Berikutnya',
                        last: 'Terakhir'
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle'
                    },
                    {
                        data: 'invoice_number',
                        name: 'invoice_number',
                        className: 'align-middle font-weight-bold'
                    },
                    {
                        data: 'user.name',
                        name: 'user.name',
                        className: 'align-middle'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        className: 'align-middle'
                    },
                    {
                        data: 'payment_method',
                        name: 'payment_method',
                        orderable: false,
                        className: 'text-center align-middle'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        className: 'align-middle font-weight-bold'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center align-middle'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle'
                    },
                ],
            });

            $(document).on('click', '.btn-detail', function() {
                const id = $(this).data('id');
                const url = "{{ route('dashboard.orders.receipt', ':id') }}".replace(':id', id) +
                    '?from=index';
                window.location.href = url;
            });

        });
    </script>
@endpush

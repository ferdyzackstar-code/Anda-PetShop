@extends('dashboard.layouts.admin')

@section('title', 'Manajemen Pembelian — Anda Petshop')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')

    <div class="card w-100 border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-4 bg-primary rounded d-flex align-items-center justify-content-between flex-wrap"
            style="gap:.5rem;">
            @php $pendingCount = \App\Models\Purchase::where('status','pending')->count(); @endphp
            <h5 class="mb-0 text-white font-weight-bold">
                <i class="fas fa-shopping-cart mr-2"></i> Manajemen Pembelian
            </h5>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <a href="{{ route('dashboard.purchases.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus mr-1"></i> Tambah Pembelian
                </a>
                <a href="{{ route('dashboard.purchases.confirmation') }}" class="btn btn-warning btn-sm font-weight-bold">
                    <i class="fas fa-hourglass-half mr-1"></i> Konfirmasi Pembelian
                    <span class="badge badge-danger ml-1">{{ $pendingCount }}</span>
                </a>
            </div>
        </div>
    </div>

    @php
        $totalPurchases = \App\Models\Purchase::count();
        $pendingCount = \App\Models\Purchase::where('status', 'pending')->count();
        $receivedCount = \App\Models\Purchase::where('status', 'received')->count();
        $cancelledCount = \App\Models\Purchase::where('status', 'cancelled')->count();
    @endphp
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="card shadow-sm h-100 bg-info border-0">
                <div class="card-body py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1" style="opacity:.8;">Total
                            Pesanan</div>
                        <div class="h4 mb-0 font-weight-bold text-white">{{ $totalPurchases }}</div>
                    </div>
                    <i class="fas fa-shopping-cart fa-2x text-white" style="opacity:.4;"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card shadow-sm h-100 bg-warning border-0">
                <div class="card-body py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1" style="opacity:.8;">Dalam
                            Perjalanan</div>
                        <div class="h4 mb-0 font-weight-bold text-white">{{ $pendingCount }}</div>
                    </div>
                    <i class="fas fa-truck fa-2x text-white" style="opacity:.4;"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card shadow-sm h-100 bg-success border-0">
                <div class="card-body py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1" style="opacity:.8;">Pembelian
                            Selesai</div>
                        <div class="h4 mb-0 font-weight-bold text-white">{{ $receivedCount }}</div>
                    </div>
                    <i class="fas fa-check-circle fa-2x text-white" style="opacity:.4;"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card shadow-sm h-100 bg-danger border-0">
                <div class="card-body py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1" style="opacity:.8;">Pembelian
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
                <i class="fas fa-list mr-1"></i> Riwayat Pembelian
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" style="min-width: 700px;" id="purchaseTable">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th width="1%">No</th>
                            <th>No PO</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Total</th>
                            <th class="text-center">Status</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content shadow">
                <div class="modal-header bg-info">
                    <h5 class="modal-title font-weight-bold text-white">
                        <i class="fas fa-file-invoice mr-1"></i> Detail Pesanan Pembelian
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted small" width="40%">No. PO</td>
                                    <td class="small font-weight-bold" id="detail_po"></td>
                                </tr>
                                <tr>
                                    <td class="text-muted small">Tanggal</td>
                                    <td class="small" id="detail_date"></td>
                                </tr>
                                <tr>
                                    <td class="text-muted small">Supplier</td>
                                    <td class="small font-weight-bold" id="detail_supplier"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted small" width="40%">Status</td>
                                    <td id="detail_status"></td>
                                </tr>
                                <tr>
                                    <td class="text-muted small">Catatan</td>
                                    <td class="small" id="detail_notes"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <h6 class="font-weight-bold mb-2">Detail Produk</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Produk</th>
                                    <th width="20%">Harga Satuan</th>
                                    <th width="10%" class="text-center">Qty</th>
                                    <th width="20%" class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="detail_items"></tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td colspan="3" class="text-right font-weight-bold small">Total Pembayaran</td>
                                    <td class="text-right font-weight-bold text-primary" id="detail_total"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

    <script>
        function formatRupiah(n) {
            let s = Math.round(parseFloat(n) || 0).toString();
            let sisa = s.length % 3,
                r = s.substr(0, sisa);
            let k = s.substr(sisa).match(/\d{3}/gi);
            if (k) r += (sisa ? '.' : '') + k.join('.');
            return r;
        }

        $(document).ready(function() {

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            const table = $('#purchaseTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                ajax: "{{ route('dashboard.purchases.index') }}",
                order: [
                    [2, 'desc']
                ],
                language: {
                    processing: 'Memuat data...',
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    infoFiltered: '(difilter dari _MAX_ total data)',
                    zeroRecords: 'Tidak ada data yang ditemukan',
                    emptyTable: 'Tidak ada data tersedia',
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
                        data: 'purchase_number',
                        name: 'purchase_number',
                        className: 'align-middle font-weight-bold'
                    },
                    {
                        data: 'purchase_date',
                        name: 'purchase_date',
                        className: 'align-middle'
                    },
                    {
                        data: 'supplier_name',
                        name: 'supplier.name',
                        className: 'align-middle'
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

            $(document).on('click', '.detail-btn', function() {
                const id = $(this).data('id');
                $.get("{{ url('dashboard/purchases') }}/" + id, function(data) {
                    $('#detail_po').text(data.purchase_number);
                    const pd = new Date(data.purchase_date);
                    $('#detail_date').text(
                        String(pd.getUTCDate()).padStart(2, '0') + '/' +
                        String(pd.getUTCMonth() + 1).padStart(2, '0') + '/' +
                        pd.getUTCFullYear()
                    );
                    $('#detail_supplier').text(data.supplier.name);
                    $('#detail_notes').text(data.notes || '-');
                    $('#detail_total').text('Rp ' + formatRupiah(data.total_amount));

                    const badges = {
                        received: '<span class="badge badge-success">Selesai</span>',
                        cancelled: '<span class="badge badge-danger">Batal</span>',
                        pending: '<span class="badge badge-warning">Pending</span>'
                    };
                    $('#detail_status').html(badges[data.status] ?? badges.pending);

                    $('#detail_items').html(data.items.map(item => `
                        <tr>
                            <td>${item.product.name}</td>
                            <td class="text-right">Rp ${formatRupiah(item.price)}</td>
                            <td class="text-center">${item.quantity}</td>
                            <td class="text-right font-weight-bold">Rp ${formatRupiah(item.subtotal)}</td>
                        </tr>`).join(''));

                    $('#detailModal').modal('show');
                });
            });

            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: @json(session('success'))
                });
            @endif
            @if (session('error'))
                Toast.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: @json(session('error'))
                });
            @endif

        });
    </script>
@endpush

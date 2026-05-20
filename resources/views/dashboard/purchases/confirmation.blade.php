@extends('dashboard.layouts.admin')

@section('title', 'Konfirmasi Pembelian — Anda Petshop')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')

    <div class="card w-100 border-0 shadow-sm mb-3">
        <div class="card-body py-3 px-4 bg-warning rounded d-flex align-items-center justify-content-between flex-wrap"
            style="gap:.5rem;">
            <h5 class="mb-0 text-white font-weight-bold">
                <i class="fas fa-hourglass-half mr-2"></i> Konfirmasi Pembelian
            </h5>
            <a href="{{ route('dashboard.purchases.index') }}" class="btn btn-info btn-sm font-weight-bold text-white">
                <i class="fas fa-shopping-cart mr-1"></i> Riwayat Pembelian
            </a>
        </div>
    </div>

    <div class="card w-100 border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-tachometer-alt text-warning mr-2"></i>
                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted mr-2">Dashboard</a> >
                <a href="{{ route('dashboard.purchases.index') }}" class="text-decoration-none text-muted mr-2 ml-2">Pembelian</a> >
                <span class="font-weight-bold text-warning ml-2">Konfirmasi</span>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-clock mr-1"></i> Pesanan Menunggu Konfirmasi
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="confirmationTable">
                    <thead>
                        <tr class="bg-warning text-white">
                            <th width="1%">No</th>
                            <th>No PO</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Total</th>
                            <th width="23%" class="text-center">Aksi</th>
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
                                    <td class="small font-weight-bold text-dark" id="detail_po"></td>
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
                                    <td class="text-muted small">Email Supplier</td>
                                    <td class="small" id="detail_email"></td>
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
                                    <th>Nama Produk</th>
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

            const table = $('#confirmationTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                ajax: {
                    url: "{{ route('dashboard.purchases.confirmation') }}",
                    data: d => {
                        d._ts = Date.now();
                    }
                },
                order: [
                    [2, 'desc']
                ],
                language: {
                    processing: '<i class="fas fa-spinner fa-spin mr-1"></i> Memuat data...',
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    zeroRecords: 'Tidak ada data yang ditemukan',
                    emptyTable: 'Semua pesanan sudah dikonfirmasi',
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
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle'
                    },
                ],
            });

            function postAction(url, title, description) {
                $.ajax({
                    url,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: res => {
                        table.ajax.reload(function() {
                            Toast.fire({
                                icon: 'success',
                                title: title,
                                text: res.message ?? description
                            });
                        }, false);
                    },
                    error: xhr => Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message ?? 'Terjadi kesalahan pada server.'
                    })
                });
            }

            $(document).on('click', '.approve-btn', function() {
                const id = $(this).data('id');
                const po = $(this).closest('tr').find('td:nth-child(2)').text().trim();
                Swal.fire({
                    title: 'Setujui Pesanan?',
                    html: `Pesanan <strong>${po}</strong> akan disetujui.<br>
                           <small class="text-success"><i class="fas fa-info-circle"></i> Stok produk akan otomatis bertambah.</small>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Setujui!',
                    cancelButtonText: 'Batal'
                }).then(r => {
                    if (r.isConfirmed)
                        postAction(
                            "{{ url('dashboard/purchases') }}/" + id + "/approve",
                            'Disetujui!',
                            'Pesanan disetujui. Stok produk berhasil ditambahkan.'
                        );
                });
            });

            $(document).on('click', '.cancel-btn', function() {
                const id = $(this).data('id');
                const po = $(this).closest('tr').find('td:nth-child(2)').text().trim();
                Swal.fire({
                    title: 'Batalkan Pesanan?',
                    html: `Pesanan <strong>${po}</strong> akan dibatalkan.<br>
                           <small class="text-muted">Pesanan tersimpan dengan status Dibatalkan.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-times mr-1"></i> Ya, Batalkan!',
                    cancelButtonText: 'Kembali'
                }).then(r => {
                    if (r.isConfirmed)
                        postAction(
                            "{{ url('dashboard/purchases') }}/" + id + "/cancel",
                            'Ditolak!',
                            'Pesanan berhasil dibatalkan. Stok tidak berubah.'
                        );
                });
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
                    $('#detail_email').text(data.supplier.email || '-');
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

        });
    </script>
@endpush

{{-- resources/views/dashboard/purchases/confirmation.blade.php --}}
@extends('dashboard.layouts.admin')

@section('title', 'Konfirmasi Pembelian — Anda Petshop')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')

    {{-- ================================
         HEADER HALAMAN
    ================================ --}}
    <div class="card w-100 border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-4 bg-warning rounded d-flex align-items-center justify-content-between flex-wrap"
            style="gap:.5rem;">
            <h5 class="mb-0 text-white font-weight-bold">
                <i class="fas fa-hourglass-half mr-2"></i> Konfirmasi Pembelian
            </h5>
            <a href="{{ route('dashboard.purchases.index') }}" class="btn btn-info btn-sm font-weight-bold">
                <i class="fas fa-shopping-cart mr-1"></i> Riwayat Pembelian
            </a>
        </div>
    </div>

    {{-- ================================
         TABEL KONFIRMASI
    ================================ --}}
    <div class="card shadow-sm">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-clock mr-1"></i> Pesanan Menunggu Konfirmasi
            </h6>
        </div>
        <div class="card-body">
            @if ($pendingPurchases->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" style="min-width: 700px;" id="confirmationTable">
                        <thead>
                            <tr class="bg-warning text-white">
                                <th width="1%">No</th>
                                <th>No PO</th>
                                <th>Tanggal</th>
                                <th>Supplier</th>
                                <th>Total</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingPurchases as $i => $purchase)
                                <tr>
                                    <td class="text-center align-middle">{{ $i + 1 }}</td>
                                    <td class="align-middle font-weight-bold text-dark">
                                        {{ $purchase->purchase_number }}
                                    </td>
                                    <td class="align-middle"
                                        data-sort="{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('Ymd') }}">
                                        {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}
                                    </td>
                                    <td class="align-middle">{{ $purchase->supplier->name }}</td>
                                    <td class="align-middle font-weight-bold">
                                        Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center align-middle" style="white-space:nowrap;">
                                        <button class="btn btn-success btn-sm approve-btn ml-1"
                                        data-id="{{ $purchase->id }}">
                                        <i class="fas fa-check-circle"></i> Setuju
                                    </button>
                                    <button class="btn btn-danger btn-sm cancel-btn ml-1" data-id="{{ $purchase->id }}">
                                        <i class="fas fa-times-circle"></i> Batal
                                    </button>
                                    <button class="btn btn-info btn-sm detail-btn" data-id="{{ $purchase->id }}">
                                        <i class="fas fa-file-invoice"></i> Detail
                                    </button>
                                </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-check-circle fa-3x text-success mb-3 d-block"></i>
                    <h6 class="font-weight-bold">Tidak Ada Pesanan Menunggu Konfirmasi</h6>
                    <p class="small mb-0">Semua pesanan pembelian sudah dikonfirmasi atau dibatalkan.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ================================
         MODAL DETAIL
    ================================ --}}
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
        function formatRupiah(angka) {
            let number = Math.round(parseFloat(angka)) || 0;
            let s = number.toString();
            let sisa = s.length % 3;
            let rupiah = s.substr(0, sisa);
            let ribuan = s.substr(sisa).match(/\d{3}/gi);
            if (ribuan) rupiah += (sisa ? '.' : '') + ribuan.join('.');
            return rupiah;
        }

        $(document).ready(function() {

            @if ($pendingPurchases->count() > 0)
                $('#confirmationTable').DataTable({
                    responsive: false,
                    order: [
                        [2, 'desc']
                    ],
                    language: {
                        search: 'Cari:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                        infoEmpty: 'Tidak ada data',
                        zeroRecords: 'Tidak ada data yang ditemukan',
                        emptyTable: 'Tidak ada data tersedia',
                        paginate: {
                            first: 'Pertama',
                            previous: 'Sebelumnya',
                            next: 'Berikutnya',
                            last: 'Terakhir'
                        }
                    },
                    createdRow: function(row) {
                        var dateCell = $(row).find('td').eq(2);
                        var sortVal = dateCell.data('sort');
                        if (sortVal) dateCell.attr('data-order', sortVal);
                    }
                });
            @endif

            // ── Detail modal ─────────────────────────────────────────────
            $(document).on('click', '.detail-btn', function() {
                let id = $(this).data('id');
                $.get("{{ url('dashboard/purchases') }}/" + id, function(data) {
                    $('#detail_po').text(data.purchase_number);

                    // Format tanggal dari purchase_date
                    let pd = new Date(data.purchase_date);
                    let dd = String(pd.getUTCDate()).padStart(2, '0');
                    let mm = String(pd.getUTCMonth() + 1).padStart(2, '0');
                    let yyyy = pd.getUTCFullYear();
                    $('#detail_date').text(dd + '/' + mm + '/' + yyyy);

                    $('#detail_supplier').text(data.supplier.name);
                    $('#detail_email').text(data.supplier.email || '-');
                    $('#detail_notes').text(data.notes || '-');
                    $('#detail_total').text('Rp ' + formatRupiah(data.total_amount));

                    const badges = {
                        received: '<span class="badge badge-success">Selesai</span>',
                        cancelled: '<span class="badge badge-danger">Batal</span>',
                        pending: '<span class="badge badge-warning">Pending</span>'
                    };
                    $('#detail_status').html(badges[data.status] || badges.pending);

                    let rows = '';
                    data.items.forEach(item => {
                        rows += `<tr>
                            <td>${item.product.name}</td>
                            <td class="text-right">Rp ${formatRupiah(item.price)}</td>
                            <td class="text-center">${item.quantity}</td>
                            <td class="text-right font-weight-bold">Rp ${formatRupiah(item.subtotal)}</td>
                        </tr>`;
                    });
                    $('#detail_items').html(rows);
                    $('#detailModal').modal('show');
                });
            });

            // ── Approve ──────────────────────────────────────────────────
            $(document).on('click', '.approve-btn', function() {
                let id = $(this).data('id');
                let po = $(this).closest('tr').find('td:nth-child(2)').text().trim();

                Swal.fire({
                    title: 'Setujui Pesanan?',
                    html: `Pesanan <strong>${po}</strong> akan disetujui.<br><small class="text-success"><i class="fas fa-info-circle"></i> Stok produk akan otomatis bertambah.</small>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Setujui!',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) {
                        $.post("{{ url('dashboard/purchases') }}/" + id + "/approve", {
                                _token: "{{ csrf_token() }}"
                            })
                            .done(res => Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => location.reload()))
                            .fail(xhr => Swal.fire('Error!', xhr.responseJSON?.message ||
                                'Gagal menyetujui!', 'error'));
                    }
                });
            });

            // ── Cancel ───────────────────────────────────────────────────
            $(document).on('click', '.cancel-btn', function() {
                let id = $(this).data('id');
                let po = $(this).closest('tr').find('td:nth-child(2)').text().trim();

                Swal.fire({
                    title: 'Batalkan Pesanan?',
                    html: `Pesanan <strong>${po}</strong> akan dibatalkan.<br><small class="text-muted">Pesanan tetap tersimpan dengan status Dibatalkan.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-times mr-1"></i> Ya, Batalkan!',
                    cancelButtonText: 'Kembali'
                }).then(result => {
                    if (result.isConfirmed) {
                        $.post("{{ url('dashboard/purchases') }}/" + id + "/cancel", {
                                _token: "{{ csrf_token() }}"
                            })
                            .done(res => Swal.fire({
                                icon: 'success',
                                title: 'Dibatalkan!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => location.reload()))
                            .fail(xhr => Swal.fire('Error!', xhr.responseJSON?.message ||
                                'Gagal membatalkan!', 'error'));
                    }
                });
            });
        });
    </script>
@endpush

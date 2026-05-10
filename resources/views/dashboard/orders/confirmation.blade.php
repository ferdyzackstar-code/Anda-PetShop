{{-- resources/views/dashboard/orders/confirmation.blade.php --}}
@extends('dashboard.layouts.admin')

@section('title', 'Konfirmasi Pembayaran — Anda Petshop')

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
                <i class="fas fa-hourglass-half mr-2"></i> Konfirmasi Pembayaran Transfer
            </h5>
            <div class="d-flex flex-wrap" style="gap:.5rem;">
                <a href="{{ route('dashboard.orders.pos') }}" class="btn btn-primary btn-sm font-weight-bold text-white">
                    <i class="fas fa-cash-register mr-1"></i> Mesin Kasir
                </a>
                <a href="{{ route('dashboard.orders.index') }}" class="btn btn-info btn-sm font-weight-bold text-white">
                    <i class="fas fa-history mr-1"></i> Riwayat
                </a>
            </div>
        </div>
    </div>

    {{-- ================================
         TABEL DATA
    ================================ --}}
    <div class="card shadow-sm">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-clock mr-1"></i> Transaksi Menunggu Konfirmasi
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="confirmation-table">
                    <thead>
                        <tr class="bg-warning text-white">
                            <th width="1%">No</th>
                            <th>Invoice</th>
                            <th>Kasir</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th width="23%" class="text-center">Aksi</th>
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

            const table = $('#confirmation-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                ajax: "{{ route('dashboard.orders.confirmation') }}",
                order: [
                    [3, 'desc']
                ],
                language: {
                    processing: 'Memuat data...',
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    zeroRecords: 'Tidak ada data yang ditemukan',
                    emptyTable: 'Tidak ada transaksi yang menunggu konfirmasi',
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

            // ── Approve ──────────────────────────────────────────────────
            $(document).on('click', '.btn-approve', function() {
                const id = $(this).data('id');
                const url = "{{ route('dashboard.orders.approve', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: 'Approve Transaksi?',
                    text: 'Pastikan dana transfer sudah masuk ke rekening toko.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Approve',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) {
                        $.post(url, {
                                _token: "{{ csrf_token() }}"
                            })
                            .done(() => {
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Transaksi disetujui!'
                                });
                                table.ajax.reload();
                            })
                            .fail(xhr => Swal.fire('Error!', xhr.responseJSON?.message ??
                                'Gagal approve.', 'error'));
                    }
                });
            });

            // ── Cancel ───────────────────────────────────────────────────
            $(document).on('click', '.btn-cancel', function() {
                const id = $(this).data('id');
                const url = "{{ route('dashboard.orders.cancel', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: 'Batalkan Transaksi?',
                    text: 'Stok tidak akan dikembalikan karena transfer belum pernah dipotong.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-times mr-1"></i> Ya, Batalkan',
                    cancelButtonText: 'Kembali'
                }).then(result => {
                    if (result.isConfirmed) {
                        $.post(url, {
                                _token: "{{ csrf_token() }}"
                            })
                            .done(res => {
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Transaksi dibatalkan!'
                                });
                                table.ajax.reload();
                            })
                            .fail(xhr => Swal.fire('Error!', xhr.responseJSON?.message ??
                                'Terjadi kesalahan.', 'error'));
                    }
                });
            });
        });
    </script>
@endpush

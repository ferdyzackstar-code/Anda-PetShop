{{-- resources/views/dashboard/purchases/index.blade.php --}}
@extends('dashboard.layouts.admin')

@section('title', 'Manajemen Pembelian — Anda Petshop')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')

    {{-- ================================
         HEADER HALAMAN
    ================================ --}}
    <div class="card w-100 border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-4 bg-primary rounded d-flex align-items-center justify-content-between flex-wrap"
            style="gap:.5rem;">
            <h5 class="mb-0 text-white font-weight-bold">
                <i class="fas fa-shopping-cart mr-2"></i> Manajemen Pembelian
            </h5>
            <a href="{{ route('dashboard.purchases.confirmation') }}" class="btn btn-warning btn-sm font-weight-bold">
                <i class="fas fa-hourglass-half mr-1"></i> Konfirmasi Pembelian
            </a>
        </div>
    </div>

    {{-- ================================
         ALERT ERROR VALIDASI
    ================================ --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-exclamation-circle mr-1"></i> Terjadi Kesalahan:</strong>
            <ul class="mb-0 mt-2 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- ================================
         KOTAK INFORMASI RINGKASAN
    ================================ --}}
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

    {{-- ================================
         FORM TAMBAH / EDIT
    ================================ --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary" id="cardTitle">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Pesanan Pembelian Baru
            </h6>
        </div>
        <div class="card-body">
            <form id="purchaseForm" action="{{ route('dashboard.purchases.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="purchase_id" id="purchaseId">

                {{-- Pilih Supplier --}}
                <div class="form-group">
                    <label class="font-weight-bold text-gray-700 small">Pilih Supplier <span
                            class="text-danger">*</span></label>
                    <select name="supplier_id" id="inputSupplier"
                        class="form-control @error('supplier_id') is-invalid @enderror">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tanggal & Catatan --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">Tanggal Pembelian <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="purchase_date" id="inputDate"
                                class="form-control @error('purchase_date') is-invalid @enderror"
                                value="{{ old('purchase_date', date('Y-m-d')) }}">
                            @error('purchase_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">Catatan <span
                                    class="text-muted">(opsional)</span></label>
                            <input type="text" name="notes" id="inputNotes" class="form-control"
                                placeholder="Catatan tambahan..." value="{{ old('notes') }}">
                        </div>
                    </div>
                </div>

                <hr>

                {{-- Detail Produk --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="font-weight-bold text-gray-700 small mb-0">
                        <i class="fas fa-box-open mr-1 text-primary"></i> Detail Produk
                    </label>
                    <button type="button" class="btn btn-success btn-sm" id="addProductBtn">
                        <i class="fas fa-plus mr-1"></i> Tambah Produk
                    </button>
                </div>

                <div id="productItemsContainer"></div>

                {{-- Grand Total --}}
                <div class="card bg-light mb-3">
                    <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                        <span class="font-weight-bold text-gray-700 small">
                            <i class="fas fa-receipt mr-1"></i> Total Pembayaran
                        </span>
                        <span class="font-weight-bold text-primary" id="grandTotal">Rp 0</span>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="d-flex mt-2">
                    <button type="submit" class="btn btn-primary btn-sm mr-2" id="submitBtn">
                        <i class="fas fa-save mr-1"></i> Buat Pesanan
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" id="resetBtn">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================================
         TABEL RIWAYAT PEMBELIAN
    ================================ --}}
    <div class="card shadow-sm">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-1"></i> Riwayat Pembelian
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="purchaseTable">
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
        // ── Helpers ──────────────────────────────────────────────────────
        let productRowIndex = 0;
        const products = @json($products);

        function formatRupiah(angka) {
            let number = Math.round(parseFloat(angka)) || 0;
            let s = number.toString();
            let sisa = s.length % 3;
            let rupiah = s.substr(0, sisa);
            let ribuan = s.substr(sisa).match(/\d{3}/gi);
            if (ribuan) rupiah += (sisa ? '.' : '') + ribuan.join('.');
            return rupiah;
        }

        function calculateGrandTotal() {
            let total = 0;
            $('.product-row').each(function() {
                let qty = parseInt($(this).find('.qty-input').val()) || 0;
                let price = parseFloat($(this).find('.price-input').val().replace(/\./g, '')) || 0;
                let sub = qty * price;
                $(this).find('.subtotal-display').text('Rp ' + formatRupiah(sub));
                total += sub;
            });
            $('#grandTotal').text('Rp ' + formatRupiah(total));
        }

        function buildProductOptions(selectedId) {
            let opts = '<option value="">-- Pilih Produk --</option>';
            products.forEach(p => {
                opts += `<option value="${p.id}" ${p.id == selectedId ? 'selected' : ''}>${p.name}</option>`;
            });
            return opts;
        }

        function addProductRow(productId = '', quantity = 1, price = '') {
            productRowIndex++;
            const html = `
                <div class="card border mb-2 product-row" data-index="${productRowIndex}">
                    <div class="card-body py-2 px-3">
                        <div class="row align-items-end">
                            <div class="col-md-4 mb-2 mb-md-0">
                                <label class="font-weight-bold text-gray-700 small">Produk</label>
                                <select name="product_id[]" class="form-control form-control-sm">
                                    ${buildProductOptions(productId)}
                                </select>
                            </div>
                            <div class="col-5 col-md-2 mb-2 mb-md-0">
                                <label class="font-weight-bold text-gray-700 small">Jumlah</label>
                                <input type="number" name="quantity[]" class="form-control form-control-sm qty-input"
                                    value="${quantity}" min="1">
                            </div>
                            <div class="col-7 col-md-3 mb-2 mb-md-0">
                                <label class="font-weight-bold text-gray-700 small">Harga Satuan</label>
                                <input type="text" name="price[]" class="form-control form-control-sm price-input"
                                    value="${price}" placeholder="0">
                            </div>
                            <div class="col-md-2 mb-2 mb-md-0">
                                <label class="font-weight-bold text-gray-700 small">Subtotal</label>
                                <div class="form-control form-control-sm subtotal-display bg-light font-weight-bold text-primary">Rp 0</div>
                            </div>
                            <div class="col-md-1 text-right">
                                <button type="button" class="btn btn-danger btn-sm remove-product mt-3 mt-md-0" style="margin-top:24px!important;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
            $('#productItemsContainer').append(html);
            calculateGrandTotal();
        }

        // ── DataTable server-side ─────────────────────────────────────
        $(document).ready(function() {

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

            // Tambah 1 baris produk saat pertama kali
            addProductRow();

            // ── Tambah baris produk ──────────────────────────────────────
            $('#addProductBtn').on('click', () => addProductRow());

            // ── Hapus baris produk ───────────────────────────────────────
            $(document).on('click', '.remove-product', function() {
                if ($('.product-row').length > 1) {
                    $(this).closest('.product-row').remove();
                    calculateGrandTotal();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Minimal harus ada 1 produk!',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });

            // ── Hitung total saat qty/harga berubah ──────────────────────
            $(document).on('input', '.price-input', function() {
                let raw = $(this).val().replace(/[^0-9]/g, '').substring(0, 15);
                let formatted = formatRupiah(raw);
                $(this).val(formatted);
                this.setSelectionRange(formatted.length, formatted.length);
                calculateGrandTotal();
            });

            $(document).on('input change', '.qty-input', () => calculateGrandTotal());

            // ── Edit pesanan (inline) ────────────────────────────────────
            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');
                $.get("{{ url('dashboard/purchases') }}/" + id, function(data) {
                    $('html, body').animate({
                        scrollTop: $('#purchaseForm').offset().top - 80
                    }, 400);

                    $('#cardTitle').html('<i class="fas fa-edit mr-1"></i> Edit Pesanan: <strong>' +
                            data.purchase_number + '</strong>')
                        .removeClass('text-primary').addClass('text-warning');

                    $('#submitBtn').html('<i class="fas fa-save mr-1"></i> Update Pesanan')
                        .removeClass('btn-primary').addClass('btn-warning');

                    $('#formMethod').val('PUT');
                    $('#purchaseId').val(data.id);
                    $('#purchaseForm').attr('action', "{{ url('dashboard/purchases') }}/" + data
                        .id);

                    $('#inputSupplier').val(data.supplier_id);
                    $('#inputDate').val(data.purchase_date);
                    $('#inputNotes').val(data.notes);

                    $('#productItemsContainer').empty();
                    data.items.forEach(item => addProductRow(item.product_id, item.quantity,
                        formatRupiah(item.price)));
                    calculateGrandTotal();
                });
            });

            // ── Reset form ───────────────────────────────────────────────
            $('#resetBtn').on('click', function() {
                $('#cardTitle').html(
                        '<i class="fas fa-plus-circle mr-1"></i> Tambah Pesanan Pembelian Baru')
                    .removeClass('text-warning').addClass('text-primary');

                $('#submitBtn').html('<i class="fas fa-save mr-1"></i> Buat Pesanan')
                    .removeClass('btn-warning').addClass('btn-primary');

                $('#formMethod').val('POST');
                $('#purchaseId').val('');
                $('#purchaseForm').attr('action', "{{ route('dashboard.purchases.store') }}");
                $('#inputSupplier').val('');
                $('#inputDate').val('{{ date('Y-m-d') }}');
                $('#inputNotes').val('');
                $('#productItemsContainer').empty();
                addProductRow();
                calculateGrandTotal();
            });

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
        });
    </script>
@endpush

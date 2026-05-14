@extends('dashboard.layouts.admin')

@section('title', 'Tambah Pembelian — Anda Petshop')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')

    <div class="card w-100 border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-4 bg-primary rounded d-flex align-items-center justify-content-between" style="flex-wrap: wrap; gap: 1rem;">
            <h5 class="mb-0 text-white font-weight-bold">
                <i class="fas fa-shopping-cart mr-2"></i> Tambah Pembelian
            </h5>
            <a href="{{ route('dashboard.purchases.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

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

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Pesanan Pembelian Baru
            </h6>
        </div>
        <div class="card-body">
            <form id="purchaseForm" action="{{ route('dashboard.purchases.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="font-weight-bold small">Pilih Supplier <span class="text-danger">*</span></label>
                    <select name="supplier_id" id="inputSupplier"
                        class="form-control @error('supplier_id') is-invalid @enderror">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold small">Tanggal Pembelian <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="purchase_date" id="inputDate"
                                class="form-control @error('purchase_date') is-invalid @enderror"
                                value="{{ old('purchase_date', date('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold small">Catatan <span class="text-muted">(opsional)</span></label>
                            <input type="text" name="notes" id="inputNotes" class="form-control"
                                placeholder="Catatan tambahan..." value="{{ old('notes') }}">
                        </div>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="font-weight-bold small mb-0">
                        <i class="fas fa-box-open mr-1 text-primary"></i> Detail Produk
                    </label>
                    <button type="button" class="btn btn-success btn-sm" id="addProductBtn">
                        <i class="fas fa-plus mr-1"></i> Tambah Produk
                    </button>
                </div>

                <div id="productItemsContainer"></div>

                <div class="card bg-light mb-3">
                    <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                        <span class="font-weight-bold small"><i class="fas fa-receipt mr-1"></i> Total Pembayaran</span>
                        <span class="font-weight-bold text-primary" id="grandTotal">Rp 0</span>
                    </div>
                </div>

                <div class="d-flex mt-2 gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i> Buat Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        let productRowIndex = 0;
        const products = @json($products);

        function formatRupiah(n) {
            let s = Math.round(parseFloat(n) || 0).toString();
            let sisa = s.length % 3,
                r = s.substr(0, sisa);
            let k = s.substr(sisa).match(/\d{3}/gi);
            if (k) r += (sisa ? '.' : '') + k.join('.');
            return r;
        }

        function calculateGrandTotal() {
            let total = 0;
            $('.product-row').each(function() {
                const qty = parseInt($(this).find('.qty-input').val()) || 0;
                const price = parseFloat($(this).find('.price-input').val().replace(/\./g, '')) || 0;
                const sub = qty * price;
                $(this).find('.subtotal-display').text('Rp ' + formatRupiah(sub));
                total += sub;
            });
            $('#grandTotal').text('Rp ' + formatRupiah(total));
        }

        function buildProductOptions(selectedId) {
            return '<option value="">-- Pilih Produk --</option>' +
                products.map(p => `<option value="${p.id}" ${p.id == selectedId ? 'selected' : ''}>${p.name}</option>`)
                .join('');
        }

        function addProductRow(productId = '', quantity = 1, price = '') {
            productRowIndex++;
            $('#productItemsContainer').append(`
                <div class="card border mb-2 product-row" data-index="${productRowIndex}">
                    <div class="card-body py-2 px-3">
                        <div class="row align-items-end">
                            <div class="col-md-4 mb-2 mb-md-0">
                                <label class="font-weight-bold small">Produk</label>
                                <select name="product_id[]" class="form-control form-control-sm">
                                    ${buildProductOptions(productId)}
                                </select>
                            </div>
                            <div class="col-5 col-md-2 mb-2 mb-md-0">
                                <label class="font-weight-bold small">Jumlah</label>
                                <input type="number" name="quantity[]" class="form-control form-control-sm qty-input" value="${quantity}" min="1">
                            </div>
                            <div class="col-7 col-md-3 mb-2 mb-md-0">
                                <label class="font-weight-bold small">Harga Satuan</label>
                                <input type="text" name="price[]" class="form-control form-control-sm price-input" value="${price}" placeholder="0">
                            </div>
                            <div class="col-md-2 mb-2 mb-md-0">
                                <label class="font-weight-bold small">Subtotal</label>
                                <div class="form-control form-control-sm subtotal-display bg-light font-weight-bold text-primary">Rp 0</div>
                            </div>
                            <div class="col-md-1 text-right">
                                <button type="button" class="btn btn-danger btn-sm remove-product" style="margin-top:24px!important;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`);
            calculateGrandTotal();
        }

        $(document).ready(function() {
            addProductRow();

            $('#addProductBtn').on('click', () => addProductRow());

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

            $(document).on('input', '.price-input', function() {
                const raw = $(this).val().replace(/[^0-9]/g, '').substring(0, 15);
                const fmt = formatRupiah(raw);
                $(this).val(fmt);
                this.setSelectionRange(fmt.length, fmt.length);
                calculateGrandTotal();
            });

            $(document).on('input change', '.qty-input', () => calculateGrandTotal());
        });
    </script>
@endpush

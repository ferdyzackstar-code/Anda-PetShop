{{-- resources/views/dashboard/orders/pos.blade.php --}}
@extends('dashboard.layouts.admin')

@section('title', 'Point of Sales — Anda Petshop')

@push('styles')
    <style>
        /* ── POS LAYOUT ──────────────────────────────────────────────── */
        .pos-wrapper {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }

        /* LEFT: produk */
        .pos-left {
            flex: 1;
            min-width: 0;
        }

        /* RIGHT: cart */
        .pos-right {
            width: 320px;
            flex-shrink: 0;
            position: sticky;
            top: 70px;
        }

        /* ── PRODUCT GRID ────────────────────────────────────────────── */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(145px, 1fr));
            gap: 12px;
            max-height: 65vh;
            overflow-y: auto;
            padding-right: 4px;
        }

        .product-grid::-webkit-scrollbar {
            width: 4px;
        }

        .product-grid::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }

        .product-card {
            border: 1.5px solid #e3eaf2;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            transition: all .2s;
            background: #fff;
            position: relative;
        }

        .product-card:hover {
            border-color: #4e73df;
            box-shadow: 0 4px 16px rgba(78, 115, 223, .15);
            transform: translateY(-2px);
        }

        .product-card.out-of-stock {
            opacity: .5;
            cursor: not-allowed;
            pointer-events: none;
        }

        .product-card.out-of-stock::after {
            content: 'Habis';
            position: absolute;
            top: 6px;
            left: 6px;
            background: #e74a3b;
            color: #fff;
            font-size: .6rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 20px;
        }

        .product-card .pc-img {
            width: 100%;
            height: 110px;
            object-fit: cover;
            display: block;
            background: #f0f4f8;
        }

        .product-card .pc-body {
            padding: 8px 10px;
        }

        .product-card .pc-name {
            font-size: .78rem;
            font-weight: 700;
            color: #1a2332;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 4px;
            line-height: 1.3;
        }

        .product-card .pc-price {
            font-size: .8rem;
            font-weight: 800;
            color: #4e73df;
        }

        .product-card .pc-stock {
            font-size: .68rem;
            color: #7b8fa6;
        }

        /* ── CART ────────────────────────────────────────────────────── */
        .cart-items {
            max-height: 300px;
            overflow-y: auto;
        }

        .cart-items::-webkit-scrollbar {
            width: 4px;
        }

        .cart-items::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 0;
            border-bottom: 1px solid #f0f4f8;
        }

        .cart-item-name {
            flex: 1;
            font-size: .78rem;
            font-weight: 600;
            color: #1a2332;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cart-item-price {
            font-size: .7rem;
            color: #7b8fa6;
        }

        .qty-ctrl {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .qty-btn {
            width: 24px;
            height: 24px;
            border-radius: 5px;
            border: 1.5px solid #e3eaf2;
            background: #f0f4f8;
            font-size: .7rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .15s;
            padding: 0;
        }

        .qty-btn.plus:hover {
            background: #4e73df;
            color: #fff;
            border-color: #4e73df;
        }

        .qty-btn.minus:hover {
            background: #e74a3b;
            color: #fff;
            border-color: #e74a3b;
        }

        .qty-num {
            font-size: .82rem;
            font-weight: 800;
            min-width: 20px;
            text-align: center;
        }

        .cart-item-sub {
            font-size: .78rem;
            font-weight: 800;
            color: #4e73df;
            min-width: 65px;
            text-align: right;
        }

        /* ── PAY METHOD TABS ─────────────────────────────────────────── */
        .pay-tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            margin-bottom: 10px;
        }

        .pay-tab {
            padding: 7px;
            border: 1.5px solid #e3eaf2;
            border-radius: 7px;
            background: #f8fafd;
            color: #7b8fa6;
            font-size: .78rem;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            transition: all .2s;
        }

        .pay-tab.active {
            background: #4e73df;
            border-color: #4e73df;
            color: #fff;
        }

        /* ── RESPONSIVE ──────────────────────────────────────────────── */
        @media (max-width: 991px) {
            .pos-wrapper {
                flex-direction: column;
            }

            .pos-right {
                width: 100%;
                position: static;
            }

            .product-grid {
                max-height: 50vh;
            }
        }

        @media (max-width: 575px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endpush

@section('content')

    {{-- ================================
         HEADER HALAMAN
    ================================ --}}
    <div class="card w-100 border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-4 bg-primary rounded d-flex align-items-center justify-content-between flex-wrap"
            style="gap:.5rem;">
            <div class="d-flex flex-column flex-md-row align-items-md-center">
                <h5 class="mb-0 text-white font-weight-bold">
                    <i class="fas fa-cash-register mr-2"></i> Point of Sales
                </h5>
                <small class="font-weight-normal ml-md-2 text-white-50" style="font-size:.90rem;">
                    {{ now()->translatedFormat('l, d F Y') }}
                </small>
            </div>
            <div class="d-flex flex-wrap" style="gap:.5rem;">
                @php $pendingOrders = \App\Models\Order::where('status','pending')->count(); @endphp
                @if ($pendingOrders > 0)
                    <a href="{{ route('dashboard.orders.confirmation') }}" class="btn btn-warning btn-sm font-weight-bold">
                        <i class="fas fa-hourglass-half mr-1"></i> Konfirmasi
                        <span class="badge badge-danger ml-1">{{ $pendingOrders }}</span>
                    </a>
                @endif
                <a href="{{ route('dashboard.orders.index') }}" class="btn btn-info btn-sm font-weight-bold">
                    <i class="fas fa-history mr-1"></i> Riwayat
                </a>
            </div>
        </div>
    </div>

    {{-- ================================
         POS LAYOUT
    ================================ --}}
    <div class="pos-wrapper">

        {{-- ── LEFT: PRODUK ────────────────────────────────────────── --}}
        <div class="pos-left">
            <div class="card shadow-sm">
                <div class="card-header py-3 d-flex align-items-center justify-content-between flex-wrap"
                    style="gap:.5rem;">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-store mr-1"></i> Pilih Produk
                    </h6>
                    <input type="text" id="product-search" class="form-control form-control-sm" style="max-width:220px;"
                        placeholder="Cari produk...">
                </div>
                <div class="card-body">
                    <div class="product-grid" id="product-list">
                        @forelse ($products as $product)
                            <div class="product-card {{ $product->stock <= 0 ? 'out-of-stock' : '' }} product-item mt-1"
                                data-name="{{ strtolower($product->name) }}"
                                onclick="addToCart({{ json_encode($product) }})">
                                <img class="pc-img"
                                    src="{{ asset('storage/uploads/products/' . ($product->image ?? 'default-product.jpg')) }}"
                                    alt="{{ $product->name }}" loading="lazy"
                                    onerror="this.src='{{ asset('storage/uploads/products/default-product.jpg') }}'">
                                <div class="pc-body">
                                    <div class="pc-name">{{ $product->name }}</div>
                                    <div class="pc-price">Rp{{ number_format($product->price, 0, ',', '.') }}</div>
                                    <div class="pc-stock">Stok: {{ $product->stock }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted d-flex flex-column align-items-center justify-content-center"
                                style="grid-column: 1 / -1; min-height: 200px;">
                                <i class="fas fa-box-open fa-3x mb-3" style="opacity:.2;"></i>
                                <h5 class="font-weight-bold mb-1" style="opacity:.5;">Belum ada produk tersedia</h5>
                                <p class="small">Silakan tambah produk di menu Manajemen Produk.</p>
                            </div>
                        @endforelse
                        <div id="search-empty" style="display: none; grid-column: 1 / -1;">
                            <div class="d-flex flex-column align-items-center justify-content-center text-muted"
                                style="min-height: 200px; width: 100%;">
                                <i class="fas fa-search fa-3x mb-3" style="opacity:.2;"></i>
                                <h5 class="font-weight-bold mb-1" style="opacity:.5;">Produk tidak ditemukan</h5>
                                <small class="text-muted">Coba gunakan kata kunci lain</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── RIGHT: CART ──────────────────────────────────────────── --}}
        <div class="pos-right">
            <div class="card shadow-sm">

                {{-- Cart Header --}}
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white d-flex">
                        <i class="fas fa-shopping-cart mr-1"></i> Keranjang
                        <span class="badge badge-light ml-auto" id="cart-count">0 Item</span>
                    </h6>
                </div>

                <div class="card-body px-3 py-3">

                    {{-- Cart Items --}}
                    <div class="cart-items mb-3" id="cart-items-wrap" style="min-height: 150px;">
                        <div class="d-flex flex-column align-items-center justify-content-center py-4 text-muted"
                            id="cart-empty-state" style="height: 100%;">
                            <i class="fas fa-shopping-cart fa-3x mb-3" style="opacity:.3;"></i>
                            <small class="mb-0">Keranjang masih kosong</small>
                        </div>
                        <div id="cart-table-body"></div>
                    </div>

                    {{-- Grand Total --}}
                    <div class="card bg-light mb-3">
                        <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold small">
                                <i class="fas fa-receipt mr-1"></i> Total
                            </span>
                            <span class="font-weight-bold text-primary" id="total-display">Rp0</span>
                        </div>
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div class="mb-2">
                        <label class="font-weight-bold text-gray-700 small text-uppercase mb-1 d-block"
                            style="letter-spacing:.5px;">Metode Pembayaran</label>
                        <div class="pay-tabs">
                            <div class="pay-tab active" data-value="cash" onclick="selectPayMethod('cash')">
                                <i class="fas fa-money-bill-wave mr-1"></i> Tunai
                            </div>
                            <div class="pay-tab" data-value="transfer" onclick="selectPayMethod('transfer')">
                                <i class="fas fa-university mr-1"></i> Transfer
                            </div>
                        </div>
                        <select id="payment_method" style="display:none;">
                            <option value="cash">cash</option>
                            <option value="transfer">transfer</option>
                        </select>
                    </div>

                    {{-- Uang Diterima (cash only) --}}
                    <div class="mb-2" id="cash-input-group">
                        <label class="font-weight-bold text-gray-700 small">Uang Diterima</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold">Rp</span>
                            </div>
                            <input type="text" id="paid_amount_format" class="form-control font-weight-bold"
                                placeholder="0" autocomplete="off">
                            <input type="hidden" id="paid_amount" value="0">
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small class="text-muted">
                                <i class="fas fa-coins mr-1"></i> Kembalian
                            </small>
                            <small class="font-weight-bold text-success" id="change_amount">Rp0</small>
                        </div>
                    </div>

                    <hr class="my-2">

                    {{-- Tombol Aksi --}}
                    <button class="btn btn-outline-danger btn-sm btn-block mb-2" onclick="clearCart()">
                        <i class="fas fa-trash-alt mr-1"></i> Kosongkan Keranjang
                    </button>
                    <button class="btn btn-primary btn-block font-weight-bold" id="btn-submit"
                        onclick="submitTransaction()">
                        <i class="fas fa-check-circle mr-1"></i> PROSES TRANSAKSI
                    </button>

                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script>
        window.posConfig = {
            storeUrl: "{{ route('dashboard.orders.store') }}",
            csrfToken: "{{ csrf_token() }}",
            assetUrl: "{{ asset('storage/uploads/products') }}"
        };

        // ── Pilih Metode Pembayaran ───────────────────────────────────
        function selectPayMethod(val) {
            document.getElementById('payment_method').value = val;
            document.querySelectorAll('.pay-tab').forEach(t =>
                t.classList.toggle('active', t.dataset.value === val)
            );
            const cashGroup = document.getElementById('cash-input-group');
            cashGroup.style.display = val === 'transfer' ? 'none' : 'block';
            if (val === 'transfer') {
                document.getElementById('paid_amount').value = 0;
                document.getElementById('paid_amount_format').value = '';
            }
            calculateChange();
        }
    </script>
    <script src="{{ asset('asset/js/pos-logic.js') }}"></script>
@endpush

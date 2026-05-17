{{-- resources/views/dashboard/orders/receipt.blade.php --}}
@extends('dashboard.layouts.admin')

@section('title', 'Struk — ' . $order->invoice_number)

@push('styles')
    <style>
        .receipt-wrapper {
            max-width: 480px;
            margin: 0 auto;
            padding-bottom: 40px;
        }

        @media print {

            .sidebar,
            .navbar,
            .topbar,
            .d-print-none {
                display: none !important;
            }

            .content-wrapper {
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
            }

            .receipt-card {
                box-shadow: none !important;
            }

            @page {
                margin: .5cm;
            }
        }
    </style>
@endpush

@section('content')

    @php
        $from = request('from', 'index');
        $backUrl = match ($from) {
            'pos' => route('dashboard.orders.pos'),
            'confirmation' => route('dashboard.orders.confirmation'),
            default => route('dashboard.orders.index'),
        };
        $backLabel = match ($from) {
            'pos' => 'Kembali ke Kasir',
            'confirmation' => 'Kembali ke Konfirmasi',
            default => 'Kembali ke Riwayat',
        };
        $logo = \App\Models\SettingApp::get('app_image');
        $logoPath = 'storage/' . $logo;
        $hasLogo = $logo && file_exists(public_path($logoPath));
        $storeName = \App\Models\SettingApp::get('app_name', 'Anda Petshop');
        $storeAddr = \App\Models\SettingApp::get('store_address', '');
        $storePhone = \App\Models\SettingApp::get('store_phone', '');
    @endphp

    <div class="container-fluid">
        <div class="receipt-wrapper">

            <div class="d-flex align-items-center justify-content-between mb-3 d-print-none">
                <a href="{{ $backUrl }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> {{ $backLabel }}
                </a>
                <button class="btn btn-primary btn-sm" onclick="window.print()">
                    <i class="fas fa-print mr-1"></i> Cetak Struk
                </button>
            </div>

            <div class="card shadow receipt-card">

                <div style="height:5px; background:linear-gradient(90deg,#0D47A1,#42A5F5,#0D47A1);"></div>

                <div class="card-body px-4 py-4">

                    <div class="text-center mb-3">
                        @if ($hasLogo)
                            <img src="{{ asset($logoPath) }}" alt="{{ $storeName }}"
                                style="width:52px;height:52px;border-radius:12px;object-fit:cover;margin-bottom:10px;">
                        @else
                            <div class="mx-auto mb-2 d-flex align-items-center justify-content-center text-white"
                                style="width:52px;height:52px;border-radius:12px;background:linear-gradient(135deg,#1565C0,#42A5F5);">
                                <i class="fas fa-paw fa-lg"></i>
                            </div>
                        @endif
                        <h5 class="font-weight-bold text-uppercase mb-0" style="letter-spacing:1px;">{{ $storeName }}
                        </h5>
                        @if ($storeAddr)
                            <p class="small text-muted mb-0">{{ $storeAddr }}</p>
                        @endif
                        @if ($storePhone)
                            <p class="small text-muted mb-0"><i class="fas fa-phone mr-1"></i>{{ $storePhone }}</p>
                        @endif
                    </div>

                    <hr class="border-top border-dashed my-3">

                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted small" width="40%">No. Invoice</td>
                            <td class="small font-weight-bold text-dark">{{ $order->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">Tanggal</td>
                            <td class="small">{{ $order->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">Kasir</td>
                            <td class="small font-weight-bold">{{ $order->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">Metode Bayar</td>
                            <td class="small font-weight-bold">{{ ucfirst($order->payment->payment_method ?? '-') }}</td>
                        </tr>
                    </table>

                    <hr class="border-top border-dashed my-3">

                    <table class="table table-sm table-bordered mb-3">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Produk</th>
                                <th width="10%" class="text-center">Qty</th>
                                <th width="30%" class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold small">{{ $item->product->name ?? 'Produk dihapus' }}
                                        </div>
                                        <div class="text-muted" style="font-size:.73rem;">
                                            Rp{{ number_format($item->price, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="text-center align-middle small">{{ $item->qty }}</td>
                                    <td class="text-right align-middle small font-weight-bold">
                                        Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="card bg-light mb-3">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Subtotal</span>
                                <span
                                    class="font-weight-bold">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                            @if ($order->payment)
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Dibayar
                                        ({{ ucfirst($order->payment->payment_method) }})</span>
                                    <span
                                        class="font-weight-bold">Rp{{ number_format($order->payment->paid_amount ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Kembalian</span>
                                    <span
                                        class="font-weight-bold">Rp{{ number_format($order->payment->change_amount ?? 0, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span class="font-weight-bold">Total</span>
                                <span
                                    class="font-weight-bold text-primary">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mb-3">
                        @if ($order->status === 'completed')
                            <span class="badge badge-success px-3 py-2">
                                <i class="fas fa-check-circle mr-1"></i> Lunas
                            </span>
                        @elseif ($order->status === 'pending')
                            <span class="badge badge-warning px-3 py-2">
                                <i class="fas fa-hourglass-half mr-1"></i> Menunggu Konfirmasi Admin
                            </span>
                        @else
                            <span class="badge badge-danger px-3 py-2">
                                <i class="fas fa-times-circle mr-1"></i> Dibatalkan
                            </span>
                        @endif
                    </div>

                    @if ($order->status === 'pending')
                        <div class="alert alert-warning alert-sm d-print-none py-2 px-3 small">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Stok belum dipotong. Menunggu konfirmasi admin sebelum transaksi diproses.
                        </div>
                    @endif

                    <hr class="border-top border-dashed my-3">
                    <div class="text-center text-muted" style="font-size:.78rem;">
                        <p class="mb-0">Terima kasih telah mempercayakan</p>
                        <p class="mb-0">kebutuhan anabul Anda kepada kami! 🐾</p>
                        <p class="mb-0 mt-1" style="font-size:.7rem; opacity:.6;">{{ $storeName }} —
                            {{ now()->format('Y') }}</p>
                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            const status = params.get('status');
            const invoice = params.get('invoice');

            if (status === 'success' && invoice && invoice !== 'null') {
                const isTransfer =
                    {{ $order->payment && $order->payment->payment_method === 'transfer' ? 'true' : 'false' }};

                let htmlContent = `No Invoice: <b>${invoice}</b>`;

                if (isTransfer) {
                    htmlContent += `<br>
                        <span class="text-warning" style="font-size:0.9rem;">
                            <i class="fas fa-hourglass-half mr-1"></i>
                            Menunggu konfirmasi admin
                        </span>`;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Transaksi Berhasil!',
                    html: htmlContent,
                    timer: 2000,
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
                window.history.replaceState({}, document.title,
                    window.location.pathname + '?from={{ request('from', 'index') }}');
            }
        });
    </script>
@endpush

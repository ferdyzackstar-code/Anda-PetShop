<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:order.history')->only(['index']);
        $this->middleware('permission:order.pos')->only(['pos', 'store']);
        $this->middleware('permission:order.confirm')->only(['confirmation', 'approve', 'cancel']);
        $this->middleware('permission:order.receipt')->only(['receipt']);
    }

    // =========================================================
    // POS — Halaman kasir
    // =========================================================
    public function pos()
    {
        $categories = Category::where('status', 'active')->whereNull('parent_id')->get();
        $products = Product::where('stock', '>', 0)->where('status', 'active')->whereHas('category', fn($q) => $q->where('status', 'active'))->with('category')->get();

        return view('dashboard.orders.pos', compact('products', 'categories'));
    }

    // =========================================================
    // STORE — Proses checkout dari POS
    // =========================================================
    public function store(Request $request)
    {
        // Ambil data dari JSON body (fetch API kirim sebagai application/json)
        // $request->input() bekerja untuk keduanya (form & JSON)
        $paymentMethod = $request->input('payment_method');
        $totalAmount = $request->input('total_amount');
        $paidRaw = $request->input('paid_amount', 0);

        // Bersihkan format Rupiah jika ada titik (misal "50.000" → 50000)
        $paidAmount = (int) str_replace('.', '', (string) $paidRaw);

        // Validasi manual setelah clean
        $request->validate([
            'cart' => 'required|array',
            'payment_method' => 'required|in:cash,transfer',
            'total_amount' => 'required|numeric|min:1',
        ]);

        if ($paymentMethod === 'cash' && $paidAmount < $totalAmount) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Uang yang diterima kurang dari total belanja.',
                ],
                422,
            );
        }

        DB::beginTransaction();
        try {
            // Tentukan status berdasarkan metode pembayaran
            // cash   → langsung completed, stok langsung dipotong
            // transfer → pending, stok belum dipotong (dipotong saat approve)
            $isCash = $paymentMethod === 'cash';
            $orderStatus = $isCash ? 'completed' : 'pending';
            $paymentStatus = $isCash ? 'paid' : 'pending';

            // 1. Buat order
            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_number' => Order::generateInvoiceNumber(),
                'total_amount' => $totalAmount,
                'status' => $orderStatus,
            ]);

            // 2. Simpan item & potong stok (HANYA jika cash)
            foreach ($request->input('cart') as $item) {
                $product = Product::lockForUpdate()->find($item['id']);

                if (!$product) {
                    throw new Exception('Produk tidak ditemukan.');
                }

                if ($isCash && $product->stock < $item['qty']) {
                    throw new Exception("Stok produk {$product->name} tidak mencukupi.");
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['qty'] * $item['price'],
                ]);

                // Transfer: stok TIDAK dipotong di sini, dipotong saat approve()
                if ($isCash) {
                    $product->decrement('stock', $item['qty']);
                }
            }

            // 3. Simpan payment
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'paid_amount' => $isCash ? $paidAmount : $totalAmount,
                'change_amount' => $isCash ? $paidAmount - $totalAmount : 0,
                'payment_status' => $paymentStatus,
                'approved_at' => $isCash ? now() : null,
                'approved_by' => $isCash ? Auth::id() : null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan!',
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'receipt_url' => route('dashboard.orders.receipt', $order->id),
                'is_transfer' => !$isCash,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal transaksi: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    // =========================================================
    // INDEX — Riwayat transaksi (DataTables server-side)
    // =========================================================
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with(['user', 'payment'])
                ->latest()
                ->get();

            return datatables()
                ->of($orders)
                ->addIndexColumn()
                ->editColumn('created_at', fn($row) => $row->created_at->format('d/m/Y H:i'))
                ->editColumn('total_amount', fn($row) => 'Rp ' . number_format($row->total_amount, 0, ',', '.'))
                ->addColumn('payment_method', function ($row) {
                    return match ($row->payment->payment_method ?? '') {
                        'cash' => '<span class="badge badge-success">Cash</span>',
                        'transfer' => '<span class="badge badge-info">Transfer</span>',
                        default => '-',
                    };
                })
                ->editColumn('status', function ($row) {
                    return match ($row->status) {
                        'completed' => '<span class="badge badge-success">Selesai</span>',
                        'pending' => '<span class="badge badge-warning">Pending</span>',
                        'cancelled' => '<span class="badge badge-danger">Batal</span>',
                        default => '-',
                    };
                })
                ->addColumn(
                    'action',
                    fn($row) => '<button class="btn btn-info btn-sm btn-detail" data-id="' .
                        $row->id .
                        '">
                        <i class="fas fa-print mr-1"></i> Struk
                    </button>',
                )
                ->rawColumns(['payment_method', 'status', 'action'])
                ->make(true);
        }

        return view('dashboard.orders.index');
    }

    // =========================================================
    // RECEIPT — Struk transaksi
    // =========================================================
    public function receipt($id)
    {
        $order = Order::with(['items.product', 'payment', 'user'])->findOrFail($id);
        return view('dashboard.orders.receipt', compact('order'));
    }

    // =========================================================
    // CONFIRMATION — Daftar transaksi transfer pending (DataTables)
    // Hanya tampil: status=pending DAN payment_method=transfer
    // =========================================================
    public function confirmation(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with(['user', 'payment'])
                ->where('status', 'pending')
                ->whereHas('payment', fn($q) => $q->where('payment_method', 'transfer'))
                ->latest()
                ->get();

            return datatables()
                ->of($orders)
                ->addIndexColumn()
                ->editColumn('created_at', fn($row) => $row->created_at->format('d/m/Y H:i'))
                ->editColumn('total_amount', fn($row) => 'Rp ' . number_format($row->total_amount, 0, ',', '.'))
                ->addColumn('action', function ($row) {
                    $receiptUrl = route('dashboard.orders.receipt', $row->id) . '?from=confirmation';
                    return '
                        <button class="btn btn-success btn-sm btn-approve mr-1" data-id="' .
                        $row->id .
                        '">
                            <i class="fas fa-check-circle mr-1"></i> Approve
                        </button>
                        <button class="btn btn-danger btn-sm btn-cancel mr-1" data-id="' .
                        $row->id .
                        '">
                            <i class="fas fa-times-circle mr-1"></i> Batalkan
                        </button>
                        <a href="' .
                        $receiptUrl .
                        '" class="btn btn-info btn-sm">
                            <i class="fas fa-print mr-1"></i> Struk
                        </a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('dashboard.orders.confirmation');
    }

    // =========================================================
    // APPROVE — pending → completed + potong stok
    // =========================================================
    public function approve(Order $order)
    {
        if ($order->status !== 'pending') {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Order bukan dalam status pending.',
                ],
                422,
            );
        }

        DB::beginTransaction();
        try {
            $order->update(['status' => 'completed']);

            if ($order->payment) {
                $order->payment->update([
                    'payment_status' => 'paid',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);
            }

            // Potong stok setelah approved (transfer belum dipotong saat store)
            $order->load('items.product');
            foreach ($order->items as $item) {
                if (!$item->product) {
                    continue;
                }

                if ($item->product->stock < $item->qty) {
                    throw new Exception("Stok {$item->product->name} tidak mencukupi saat approve.");
                }

                $item->product->decrement('stock', $item->qty);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Transaksi disetujui & stok dipotong.']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal approve: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================
    // CANCEL — pending → cancelled (stok tidak dikembalikan)
    // Transfer pending = stok belum pernah dipotong saat store()
    // =========================================================
    public function cancel($id)
    {
        $order = Order::with(['items.product', 'payment'])->findOrFail($id);

        if ($order->status !== 'pending') {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Hanya order berstatus pending yang bisa dibatalkan.',
                ],
                422,
            );
        }

        DB::beginTransaction();
        try {
            $order->update(['status' => 'cancelled']);

            if ($order->payment) {
                $order->payment->update(['payment_status' => 'failed']);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Transaksi dibatalkan & stok tidak bertambah.']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal membatalkan: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================
    // CONFIRM PAYMENT — alias approve (legacy, dipertahankan)
    // =========================================================
    public function confirmPayment(Request $request, Order $order)
    {
        return $this->approve($order);
    }
}

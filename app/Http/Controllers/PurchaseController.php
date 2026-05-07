<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    // =========================================================
    // INDEX — Tampilkan semua riwayat pembelian
    // =========================================================
    public function index()
    {
        $purchases = Purchase::with('supplier')->latest()->get();
        $suppliers = Supplier::where('status', 'active')->get();
        $products = Product::all();
        $totalProducts = Product::count();
        $pendingCount = Purchase::where('status', 'pending')->count();
        $receivedCount = Purchase::where('status', 'received')->count();
        $cancelledCount = Purchase::where('status', 'cancelled')->count();

        return view('dashboard.purchases.index', compact('purchases', 'suppliers', 'products', 'totalProducts', 'pendingCount', 'receivedCount', 'cancelledCount'));
    }

    // =========================================================
    // STORE — Buat pesanan baru (status: pending)
    // =========================================================
    public function store(Request $request)
    {
        $request->validate(
            [
                'supplier_id' => 'required|exists:suppliers,id',
                'purchase_date' => 'required|date',
                'notes' => 'nullable|string',
                'product_id' => 'required|array|min:1',
                'product_id.*' => 'required|exists:products,id',
                'quantity' => 'required|array|min:1',
                'quantity.*' => 'required|numeric|min:1',
                'price' => 'required|array|min:1',
                'price.*' => 'required|numeric|min:1',
            ],
            [
                'supplier_id.required' => 'Supplier harus dipilih!',
                'purchase_date.required' => 'Tanggal harus diisi!',
                'product_id.*.required' => 'Produk harus dipilih!',
                'quantity.*.required' => 'Jumlah harus diisi!',
                'quantity.*.min' => 'Jumlah minimal 1!',
                'quantity.*.numeric' => 'Jumlah harus berupa angka!',
                'price.*.required' => 'Harga harus diisi!',
                'price.*.min' => 'Harga minimal 1!',
                'price.*.numeric' => 'Harga harus berupa angka!',
            ],
        );

        // Bersihkan format Rupiah dari harga (misal: "1.500.000" → 1500000)
        $cleanPrices = array_map(fn($p) => (float) preg_replace('/[^0-9]/', '', $p), $request->price);

        DB::beginTransaction();
        try {
            // Generate nomor PO otomatis: PO-YYYYMMDD-0001
            $datePrefix = Carbon::parse($request->purchase_date)->format('Ymd');
            $last = Purchase::whereDate('purchase_date', $request->purchase_date)->latest('id')->first();
            $seq = $last ? ((int) substr($last->purchase_number, -4)) + 1 : 1;
            $poNumber = 'PO-' . $datePrefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

            // Hitung grand total
            $totalAmount = 0;
            foreach ($request->product_id as $i => $pid) {
                $totalAmount += $request->quantity[$i] * $cleanPrices[$i];
            }

            // Simpan header purchase (status selalu pending)
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'purchase_number' => $poNumber,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
                'status' => 'pending',
            ]);

            // Simpan detail item (stok belum bertambah)
            foreach ($request->product_id as $i => $pid) {
                $qty = $request->quantity[$i];
                $price = $cleanPrices[$i];
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $pid,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $qty * $price,
                ]);
            }

            DB::commit();
            return redirect()->route('dashboard.purchases.index')->with('success', 'Pesanan pembelian berhasil ditambah!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    // =========================================================
    // SHOW — Detail pesanan (JSON untuk modal)
    // =========================================================
    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'items.product'])->findOrFail($id);
        return response()->json($purchase);
    }

    // =========================================================
    // UPDATE — Edit pesanan (hanya jika status: pending)
    // =========================================================
    public function update(Request $request, $id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);

        if ($purchase->status !== 'pending') {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Pesanan dengan status ' . strtoupper($purchase->status) . ' tidak dapat diedit!',
                ],
                403,
            );
        }

        $request->validate(
            [
                'supplier_id' => 'required|exists:suppliers,id',
                'purchase_date' => 'required|date',
                'notes' => 'nullable|string',
                'product_id' => 'required|array|min:1',
                'product_id.*' => 'required|exists:products,id',
                'quantity' => 'required|array|min:1',
                'quantity.*' => 'required|numeric|min:1',
                'price' => 'required|array|min:1',
                'price.*' => 'required|numeric|min:1',
            ],
            [
                'supplier_id.required' => 'Supplier harus dipilih!',
                'purchase_date.required' => 'Tanggal harus diisi!',
                'product_id.*.required' => 'Produk harus dipilih!',
                'quantity.*.required' => 'Jumlah harus diisi!',
                'quantity.*.min' => 'Jumlah minimal 1!',
                'quantity.*.numeric' => 'Jumlah harus berupa angka!',
                'price.*.required' => 'Harga harus diisi!',
                'price.*.min' => 'Harga minimal 1!',
                'price.*.numeric' => 'Harga harus berupa angka!',
            ],
        );

        $cleanPrices = array_map(fn($p) => (float) preg_replace('/[^0-9]/', '', $p), $request->price);

        DB::beginTransaction();
        try {
            // Hapus item lama lalu simpan yang baru
            $purchase->items()->delete();

            $totalAmount = 0;
            foreach ($request->product_id as $i => $pid) {
                $qty = $request->quantity[$i];
                $price = $cleanPrices[$i];
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $pid,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $qty * $price,
                ]);
                $totalAmount += $qty * $price;
            }

            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
            ]);

            DB::commit();
            return redirect()->route('dashboard.purchases.index')->with('success', 'Pesanan pembelian berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }

    // =========================================================
    // CONFIRMATION — Daftar pesanan pending untuk dikonfirmasi
    // =========================================================
    public function confirmation()
    {
        $pendingPurchases = Purchase::with('supplier')->where('status', 'pending')->latest()->get();
        return view('dashboard.purchases.confirmation', compact('pendingPurchases'));
    }

    // =========================================================
    // APPROVE — Setujui pesanan (pending → received + stok bertambah)
    // =========================================================
    public function approve($id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);

        if ($purchase->status !== 'pending') {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Hanya pesanan berstatus Pending yang dapat disetujui!',
                ],
                403,
            );
        }

        DB::beginTransaction();
        try {
            $purchase->update(['status' => 'received']);

            foreach ($purchase->items as $item) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pesanan disetujui! Stok produk berhasil ditambahkan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyetujui: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================
    // CANCEL — Batalkan pesanan (pending → cancelled, stok tidak berubah)
    // =========================================================
    public function cancel($id)
    {
        $purchase = Purchase::findOrFail($id);

        if ($purchase->status !== 'pending') {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Hanya pesanan berstatus Pending yang dapat dibatalkan!',
                ],
                403,
            );
        }

        DB::beginTransaction();
        try {
            $purchase->update(['status' => 'cancelled']);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pesanan berhasil dibatalkan. Stok tidak berubah.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal membatalkan: ' . $e->getMessage()], 500);
        }
    }
}

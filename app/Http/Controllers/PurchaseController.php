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
    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->get();
        $products = Product::where('status', 'active')->get();

        return view('dashboard.purchases.create', compact('suppliers', 'products'));
    }

    public function edit($id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);
        
        $supplierIdInPurchase = $purchase->supplier_id;
        $suppliers = Supplier::where('status', 'active')->orWhere('id', $supplierIdInPurchase)->get();

        $productIdsInPurchase = $purchase->items->pluck('product_id')->toArray();
        $products = Product::where('status', 'active')->orWhereIn('id', $productIdsInPurchase)->get();

        return view('dashboard.purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $purchases = Purchase::with('supplier')->latest('purchase_date');

            return datatables()
                ->eloquent($purchases)
                ->addIndexColumn()
                ->editColumn('purchase_date', fn($row) => Carbon::parse($row->purchase_date)->format('d/m/Y'))
                ->addColumn('supplier_name', fn($row) => $row->supplier->name ?? '-')
                ->editColumn('total_amount', fn($row) => 'Rp ' . number_format($row->total_amount, 0, ',', '.'))
                ->editColumn('status', function ($row) {
                    return match ($row->status) {
                        'received' => '<span class="badge badge-success">Selesai</span>',
                        'cancelled' => '<span class="badge badge-danger">Batal</span>',
                        default => '<span class="badge badge-warning">Pending</span>',
                    };
                })
                ->addColumn('action', function ($row) {
                    $btn =
                        '<button class="btn btn-info btn-sm detail-btn" data-id="' .
                        $row->id .
                        '">
                                <i class="fas fa-file-invoice mr-1"></i> Detail
                            </button>';
                    if ($row->status === 'pending') {
                        $btn .=
                            ' <a href="' .
                            route('dashboard.purchases.edit', $row->id) .
                            '" class="btn btn-warning btn-sm ml-1">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>';
                    }
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('dashboard.purchases.index');
    }

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

        $cleanPrices = array_map(fn($p) => (float) preg_replace('/[^0-9]/', '', $p), $request->price);

        DB::beginTransaction();
        try {
            $datePrefix = Carbon::parse($request->purchase_date)->format('Ymd');
            $last = Purchase::whereDate('purchase_date', $request->purchase_date)->latest('id')->first();
            $seq = $last ? ((int) substr($last->purchase_number, -4)) + 1 : 1;
            $poNumber = 'PO-' . $datePrefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

            $totalAmount = 0;
            foreach ($request->product_id as $i => $pid) {
                $totalAmount += $request->quantity[$i] * $cleanPrices[$i];
            }

            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'purchase_number' => $poNumber,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
                'status' => 'pending',
            ]);

            foreach ($request->product_id as $i => $pid) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $pid,
                    'quantity' => $request->quantity[$i],
                    'price' => $cleanPrices[$i],
                    'subtotal' => $request->quantity[$i] * $cleanPrices[$i],
                ]);
            }

            DB::commit();
            return redirect()->route('dashboard.purchases.index')->with('success', 'Pesanan pembelian berhasil ditambah!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'items.product'])->findOrFail($id);
        return response()->json($purchase);
    }

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

    public function confirmation(Request $request)
    {
        if ($request->ajax()) {
            $purchases = Purchase::with('supplier')->where('status', 'pending')->latest('purchase_date');

            return datatables()
                ->eloquent($purchases)
                ->addIndexColumn()
                ->editColumn('purchase_date', fn($row) => Carbon::parse($row->purchase_date)->format('d/m/Y'))
                ->addColumn('supplier_name', fn($row) => $row->supplier->name ?? '-')
                ->editColumn('total_amount', fn($row) => 'Rp ' . number_format($row->total_amount, 0, ',', '.'))
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-success btn-sm approve-btn" data-id="' .
                        $row->id .
                        '">
                            <i class="fas fa-check-circle mr-1"></i> Setuju
                        </button>
                        <button class="btn btn-danger btn-sm cancel-btn ml-1" data-id="' .
                        $row->id .
                        '">
                            <i class="fas fa-times-circle mr-1"></i> Batal
                        </button>
                        <button class="btn btn-info btn-sm detail-btn ml-1" data-id="' .
                        $row->id .
                        '">
                            <i class="fas fa-file-invoice mr-1"></i> Detail
                        </button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('dashboard.purchases.confirmation');
    }

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

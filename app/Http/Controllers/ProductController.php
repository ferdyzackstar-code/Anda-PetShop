<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:product-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['category', 'outlet'])->select('products.*');

            return DataTables::eloquent($products)
                ->addIndexColumn()
                ->addColumn('supplier_name', function ($row) {
                    return $row->supplier ? $row->supplier->name : '<span class="text-danger">No Supplier</span>';
                })
                ->addColumn('image', function (Product $product) {
                    $url = $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png');
                    return '<img src="' . $url . '" width="50" class="img-thumbnail">';
                })
                ->addColumn('status', function (Product $product) {
                    $badge = $product->status == 'active' ? 'success' : 'danger';
                    return '<span class="badge badge-' . $badge . '">' . ucfirst($product->status) . '</span>';
                })
                ->addColumn('category', function (Product $product) {
                    return $product->category->name ?? '-';
                })
                ->addColumn('outlet_name', function (Product $product) {
                    return $product->outlet->name ?? '-';
                })
                ->addColumn('price', function (Product $product) {
                    return 'Rp ' . number_format($product->price ?? 0, 0, ',', '.');
                })
                ->addColumn('action', function (Product $product) {
                    $btn = '<button type="button" class="btn btn-info btn-sm mr-1" data-toggle="modal" data-target="#modalShowProduct' . $product->id . '"><i class="fa fa-eye"></i></button>';
                    $btn .= '<button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#modalEditProduct' . $product->id . '"><i class="fa fa-edit"></i></button>';
                    $btn .= '<form action="' . route('dashboard.products.destroy', $product->id) . '" method="POST" style="display:inline">' . csrf_field() . method_field('DELETE') . '<button type="button" class="btn btn-danger btn-sm show_confirm"><i class="fa fa-trash"></i></button></form>';
                    return $btn;
                })
                ->orderColumns(['name', 'city'], ':column $1')
                ->rawColumns(['supplier_name', 'image', 'status', 'action'])
                ->make(true);
        }

        $outlets = Outlet::all();
        $categories = Category::with('children')->whereNull('parent_id')->get();
        $products = Product::all();
        $suppliers = Supplier::all();

        return view('dashboard.products.index', compact('categories', 'products', 'outlets', 'suppliers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'category_id' => 'required',
            'outlet_id' => 'required',
            'price' => 'required',
            'stock' => 'required|integer',
            'detail' => 'required',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        $data['price'] = str_replace('.', '', $request->price);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);
        return redirect()->route('dashboard.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'supplier_id' => 'nullable|exists:suppliers,id', 
            'category_id' => 'required',
            'outlet_id' => 'required',
            'price' => 'required',
            'stock' => 'required|integer',
            'detail' => 'required',
            'status' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();
        $data['price'] = str_replace('.', '', $request->price); 

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        return redirect()->route('dashboard.products.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Hapus file fisik dari storage sebelum menghapus data dari database
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('dashboard.products.index')->with('success', 'Product deleted successfully');
    }
}

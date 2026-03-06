<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:product-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::with('children')->whereNull('parent_id')->get();
        if ($request->ajax()) {
            $data = Product::with('category')->get();
            // Tambahkan ini agar modal create/edit punya data kategori

            return DataTables::of($data)
                ->addIndexColumn() // Menambah kolom indeks secara otomatis
                ->addColumn('nomor', function ($data) {
                    static $counter = 0; // Variabel untuk menghitung nomor urut
                    return ++$counter; // Kembalikan nomor urut yang ditingkatkan
                })
                ->addColumn('category', function ($data) {
                    return $data->category->name ?? 'N/A'; // Kembalikan nama atau 'N/A' jika kosong
                })
                ->rawColumns(['category', 'nomor']) // Biarkan HTML dalam kolom 'name' dan 'action'
                ->make(true); // Kembalikan data dalam format DataTables
        }
        return view('dashboard.products.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        // Mengambil kategori yang tidak punya parent (Utama) beserta anak-anaknya
        $categories = \App\Models\Category::with('children')->whereNull('parent_id')->get();

        return view('dashboard.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'branch_name' => 'required',
            'detail' => 'required',
        ]);

        \App\Models\Product::create($request->all());

        return redirect()->route('dashboard.products.index')->with('success', 'Produk berhasil ditambahkan ke cabang tersebut.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product): View
    {
        return view('dashboard.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product): View
    {
        return view('dashboard.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        request()->validate([
            'name' => 'required',
            'detail' => 'required',
            'branch_name' => 'required',
        ]);

        $product->update($request->all());

        return redirect()->route('dashboard.products.index')->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('dashboard.products.index')->with('success', 'Product deleted successfully');
    }
}

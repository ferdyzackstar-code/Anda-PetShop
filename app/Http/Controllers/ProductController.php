<?php

namespace App\Http\Controllers;

use App\Imports\ProductsImport;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:product.index|product.create|product.edit|product.delete', ['only' => ['index']]);
        $this->middleware('permission:product.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['category.parent'])->select('products.*');

            return DataTables::eloquent($products)
                ->addIndexColumn()

                ->addColumn('image', function (Product $product) {
                    $path = 'storage/uploads/products/' . $product->image;
                    $url = $product->image && file_exists(public_path($path)) ? asset($path) : asset('storage/uploads/products/default-product.jpg');
                    return '<img src="' . $url . '" style="width:50px;height:50px;object-fit:cover;" class="img-thumbnail shadow-sm">';
                })

                ->addColumn('species', function (Product $product) {
                    $name = optional(optional($product->category)->parent)->name ?? '—';
                    return '<span class="badge badge-primary">' . e($name) . '</span>';
                })

                ->addColumn('category', function (Product $product) {
                    $name = optional($product->category)->name ?? '—';
                    return '<span class="badge badge-info">' . e($name) . '</span>';
                })

                ->addColumn('status', function (Product $product) {
                    $class = $product->status === 'active' ? 'badge-success' : 'badge-danger';
                    return '<span class="badge ' . $class . '">' . ucfirst($product->status) . '</span>';
                })

                ->addColumn('price', function (Product $product) {
                    return 'Rp ' . number_format($product->price ?? 0, 0, ',', '.');
                })

                ->addColumn('stock', function (Product $product) {
                    $stock = $product->stock ?? 0;
                    $class = match (true) {
                        $stock === 0 => 'badge-danger',
                        $stock <= 5 => 'badge-warning',
                        default => 'badge-success',
                    };
                    return '<span class="badge ' . $class . '">' . $stock . ' Pcs</span>';
                })

                ->addColumn('action', function (Product $product) {
                    $editBtn =
                        '
                        <a href="' .
                        route('dashboard.products.edit', $product->id) .
                        '"
                           class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>';

                    $deleteBtn =
                        '
                        <form action="' .
                        route('dashboard.products.destroy', $product->id) .
                        '" method="POST" style="display:inline">
                            ' .
                        csrf_field() .
                        method_field('DELETE') .
                        '
                            <button type="submit" class="btn btn-danger btn-sm show_confirm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>';

                    return $editBtn . ' ' . $deleteBtn;
                })

                ->rawColumns(['image', 'species', 'category', 'status', 'price', 'stock', 'action'])
                ->make(true);
        }

        return view('dashboard.products.index');
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->where('status', 'active')->orderBy('name')->get();

        return view('dashboard.products.create', compact('parentCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'name' => 'required|string|max:255',
                'price' => 'required',
                'stock' => 'required|integer|min:0',
                'status' => 'required|in:active,inactive',
                'category_id' => 'required|exists:categories,id',
                'detail' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'species_id' => 'nullable', // tidak ke DB, hanya untuk old()
            ],
            [
                'name.required' => 'Nama produk harus diisi!',
                'price.required' => 'Harga harus diisi!',
                'stock.required' => 'Stok harus diisi!',
                'stock.min' => 'Stok tidak boleh negatif!',
                'status.required' => 'Status harus diisi!',
                'category_id.required' => 'Spesies dan Kategori harus dipilih!',
                'category_id.exists' => 'Kategori tidak valid!',
                'image.mimes' => 'Foto harus berformat jpeg, png, atau jpg!',
                'image.max' => 'Ukuran foto maksimal 2MB!',
            ],
        );

        $data['price'] = (int) str_replace('.', '', $request->price);
        unset($data['species_id']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), $request->name);
        }

        Product::create($data);

        return redirect()->route('dashboard.products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit(Product $product)
    {
        $parentCategories = Category::whereNull('parent_id')->where('status', 'active')->orderBy('name')->get();

        return view('dashboard.products.edit', compact('product', 'parentCategories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate(
            [
                'name' => 'required|string|max:255',
                'price' => 'required',
                'stock' => 'required|integer|min:0',
                'status' => 'required|in:active,inactive',
                'category_id' => 'required|exists:categories,id',
                'detail' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'species_id' => 'nullable', // tidak ke DB, hanya untuk old()
            ],
            [
                'name.required' => 'Nama produk harus diisi!',
                'price.required' => 'Harga harus diisi!',
                'stock.required' => 'Stok harus diisi!',
                'stock.min' => 'Stok tidak boleh negatif!',
                'status.required' => 'Status harus diisi!',
                'category_id.required' => 'Spesies dan Kategori harus dipilih!',
                'category_id.exists' => 'Kategori tidak valid!',
                'image.mimes' => 'Foto harus berformat jpeg, png, atau jpg!',
                'image.max' => 'Ukuran foto maksimal 2MB!',
            ],
        );

        $data['price'] = (int) str_replace('.', '', $request->price);
        unset($data['species_id']);

        if ($request->hasFile('image')) {
            $this->deleteImage($product->image);
            $data['image'] = $this->uploadImage($request->file('image'), $request->name);
        }

        $product->update($data);

        return redirect()->route('dashboard.products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteImage($product->image);
        $product->delete();

        return redirect()->route('dashboard.products.index')->with('success', 'Produk berhasil dihapus!');
    }

    public function getSubCategories(int $parentId)
    {
        $categories = Category::where('parent_id', $parentId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($categories);
    }

    public function downloadImportTemplate()
    {
        $categories = Category::orderByRaw('COALESCE(parent_id, id), parent_id IS NOT NULL')->get();
        return Excel::download(new \App\Exports\ProductsImportTemplateExport($categories), 'template_import_data_products.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'file' => 'required|mimes:xlsx,xls,csv',
            ],
            [
                'file.required' => 'File harus diisi!',
                'file.mimes' => 'File harus dalam format xlsx, xls atau csv!',
            ],
        );

        $import = new ProductsImport();
        Excel::import($import, $request->file('file'));

        $failures = $import->failures();
        $imported = $import->getImportedCount();

        if ($imported === 0 && $failures->isEmpty()) {
            return back()->withErrors(['file' => 'File kosong atau tidak mengandung data yang valid!']);
        }

        $failureMessages = $failures->isNotEmpty()
            ? $failures
                ->map(function ($failure) {
                    return $failure->errors()[0] ?? 'Unknown error';
                })
                ->toArray()
            : [];

        if ($imported > 0 && $failures->isNotEmpty()) {
            return back()
                ->with('success', "Berhasil import {$imported} produk!")
                ->with('import_failures', $failureMessages);
        }

        if ($imported === 0 && $failures->isNotEmpty()) { 
            return back()->with('import_failures', $failureMessages);
        }

        return redirect()
            ->route('dashboard.products.index')
            ->with('success', "Berhasil import {$imported} produk!");
    }

    public function export()
    {
        $fileName = 'data_products_anda_petshop_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new \App\Exports\ProductsExport(), $fileName);
    }

    private function uploadImage($file, string $productName): string
    {
        $dest = public_path('storage/uploads/products');
        if (!File::isDirectory($dest)) {
            File::makeDirectory($dest, 0755, true, true);
        }
        $filename = time() . '-' . Str::slug($productName) . '.' . $file->getClientOriginalExtension();
        $file->move($dest, $filename);
        return $filename;
    }

    private function deleteImage(?string $image): void
    {
        if (!$image || $image === 'default-product.jpg') {
            return;
        }
        $path = public_path('storage/uploads/products/' . $image);
        if (File::exists($path)) {
            File::delete($path);
        }
    }
}

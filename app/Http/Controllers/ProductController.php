<?php

namespace App\Http\Controllers;

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
        $this->middleware('permission:product.index|product.create|product.edit|product.delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:product.create', ['only' => ['store']]);
        $this->middleware('permission:product.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product.delete', ['only' => ['destroy']]);
    }

    // ─────────────────────────────────────────────────────────────
    //  INDEX — tampilkan halaman + feed DataTable (AJAX)
    // ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['category.parent'])->select('products.*');

            return DataTables::eloquent($products)
                ->addIndexColumn()

                // Kolom Foto
                ->addColumn('image', function (Product $product) {
                    $path = 'storage/uploads/products/' . $product->image;
                    $url = $product->image && file_exists(public_path($path)) ? asset($path) : asset('storage/uploads/products/default-product.jpg');

                    return '<img src="' . $url . '" style="width:50px;height:50px;object-fit:cover;" class="img-thumbnail shadow-sm">';
                })

                // Kolom Spesies (parent kategori)
                ->addColumn('species', function (Product $product) {
                    $name = optional(optional($product->category)->parent)->name ?? '—';
                    return '<span class="badge badge-primary">' . e($name) . '</span>';
                })

                // Kolom Kategori
                ->addColumn('category', function (Product $product) {
                    $name = optional($product->category)->name ?? '—';
                    return '<span class="badge badge-info">' . e($name) . '</span>';
                })

                // Kolom Status
                ->addColumn('status', function (Product $product) {
                    $class = $product->status === 'active' ? 'badge-success' : 'badge-danger';
                    return '<span class="badge ' . $class . '">' . ucfirst($product->status) . '</span>';
                })

                // Kolom Harga
                ->addColumn('price', function (Product $product) {
                    return 'Rp ' . number_format($product->price ?? 0, 0, ',', '.');
                })

                // Kolom Stok
                ->addColumn('stock', function (Product $product) {
                    $stock = $product->stock ?? 0;
                    $class = match (true) {
                        $stock === 0 => 'badge-danger',
                        $stock <= 5 => 'badge-warning',
                        default => 'badge-success',
                    };
                    return '<span class="badge ' . $class . '">' . $stock . ' Pcs</span>';
                })

                // Kolom Aksi — hanya bawa data-id, konsisten dengan semua halaman lain
                ->addColumn('action', function (Product $product) {
                    $editBtn =
                        '
                        <button class="btn btn-warning btn-sm btn-edit" data-id="' .
                        $product->id .
                        '">
                            <i class="fas fa-edit"></i> Edit
                        </button>';

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
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>';

                    return $editBtn . ' ' . $deleteBtn;
                })

                ->rawColumns(['image', 'species', 'category', 'status', 'price', 'stock', 'action'])
                ->make(true);
        }

        // Ambil spesies (parent) untuk dropdown di form
        $parentCategories = Category::whereNull('parent_id')->where('status', 'active')->orderBy('name')->get();

        return view('dashboard.products.index', compact('parentCategories'));
    }

    // ─────────────────────────────────────────────────────────────
    //  STORE — simpan produk baru
    // ─────────────────────────────────────────────────────────────
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
            ],
            [
                'name.required' => 'Nama produk harus diisi!',
                'price.required' => 'Harga harus diisi!',
                'stock.required' => 'Stok harus diisi!',
                'stock.min' => 'Stok tidak boleh negatif!',
                'status.required' => 'Status harus diisi!',
                'category_id.required' => 'Kategori harus dipilih!',
                'category_id.exists' => 'Kategori tidak valid!',
                'image.mimes' => 'Foto harus berformat jpeg, png, atau jpg!',
                'image.max' => 'Ukuran foto maksimal 2MB!',
            ],
        );

        // Bersihkan format rupiah → simpan sebagai integer
        $data['price'] = (int) str_replace('.', '', $request->price);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), $request->name);
        }

        Product::create($data);

        return redirect()->route('dashboard.products.index')->with('success', 'Produk Berhasil Ditambahkan!');
    }

    // ─────────────────────────────────────────────────────────────
    //  EDIT — kembalikan JSON untuk JS (AJAX)
    // ─────────────────────────────────────────────────────────────
    public function edit(Product $product)
    {
        $imgPath = 'storage/uploads/products/' . $product->image;
        $imgUrl = $product->image && file_exists(public_path($imgPath)) ? asset($imgPath) : asset('storage/uploads/products/default-product.jpg');

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price_formatted' => number_format($product->price ?? 0, 0, ',', '.'),
            'stock' => $product->stock,
            'status' => $product->status,
            'detail' => $product->detail,
            'species_id' => optional(optional($product->category)->parent)->id,
            'category_id' => $product->category_id,
            'image_url' => $imgUrl,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  UPDATE — perbarui produk
    // ─────────────────────────────────────────────────────────────
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
            ],
            [
                'name.required' => 'Nama produk harus diisi!',
                'price.required' => 'Harga harus diisi!',
                'stock.required' => 'Stok harus diisi!',
                'stock.min' => 'Stok tidak boleh negatif!',
                'status.required' => 'Status harus diisi!',
                'category_id.required' => 'Kategori harus dipilih!',
                'category_id.exists' => 'Kategori tidak valid!',
                'image.mimes' => 'Foto harus berformat jpeg, png, atau jpg!',
                'image.max' => 'Ukuran foto maksimal 2MB!',
            ],
        );

        $data['price'] = (int) str_replace('.', '', $request->price);

        if ($request->hasFile('image')) {
            $this->deleteImage($product->image);
            $data['image'] = $this->uploadImage($request->file('image'), $request->name);
        }

        $product->update($data);

        return redirect()->route('dashboard.products.index')->with('success', 'Produk Berhasil Diperbarui!');
    }

    // ─────────────────────────────────────────────────────────────
    //  DESTROY
    // ─────────────────────────────────────────────────────────────
    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteImage($product->image);
        $product->delete();

        return redirect()->route('dashboard.products.index')->with('success', 'Produk Berhasil Dihapus!');
    }

    // ─────────────────────────────────────────────────────────────
    //  GET SUB-CATEGORIES — AJAX untuk dropdown kategori bertingkat
    // ─────────────────────────────────────────────────────────────
    public function getSubCategories(int $parentId)
    {
        $categories = Category::where('parent_id', $parentId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($categories);
    }

    // ─────────────────────────────────────────────────────────────
    //  IMPORT / EXPORT / TEMPLATE
    // ─────────────────────────────────────────────────────────────
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
                'file.mimes' => 'File harus dalam format xlsx, xls, atau csv!',
            ],
        );

        $import = new \App\Imports\ProductsImport();

        Excel::import($import, $request->file('file'));

        // getFailures() return array of string (bukan Maatwebsite Failure object)
        if (!empty($import->getFailures())) {
            return back()->with('import_failures', $import->getFailures());
        }

        return redirect()
            ->route('dashboard.products.index')
            ->with('success', 'Berhasil Mengimpor ' . $import->getImportedCount() . ' Produk!');
    }

    public function export()
    {
        $fileName = 'data_products_anda_petshop_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new \App\Exports\ProductsExport(), $fileName);
    }

    // ─────────────────────────────────────────────────────────────
    //  PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────
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

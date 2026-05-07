<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:category.index|category.create|category.edit|category.delete', ['only' => ['index']]);
        $this->middleware('permission:category.create', ['only' => ['store']]);
        $this->middleware('permission:category.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:category.delete', ['only' => ['destroy']]);
    }

    // ─────────────────────────────────────────────────────────────
    //  INDEX — tampilkan halaman + feed DataTable (AJAX)
    // ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Category::with('parent')
                ->withCount(['products', 'childrenProducts'])
                ->orderByRaw('COALESCE(parent_id, id), parent_id IS NOT NULL, id');

            return DataTables::of($data)
                ->addIndexColumn()

                // Kolom Nama — indentasi visual untuk kategori child
                ->addColumn('name_display', function ($row) {
                    if ($row->parent_id) {
                        return '<div class="ml-4 text-muted"><i class="fas fa-level-up-alt fa-rotate-90 mr-1"></i>' . e($row->name) . '</div>';
                    }
                    return '<strong><i class="fas fa-folder-open text-primary mr-1"></i>' . e($row->name) . '</strong>';
                })

                // Kolom Tipe (Spesies / Kategori)
                ->addColumn('type_badge', function ($row) {
                    if ($row->parent_id) {
                        return '<span class="badge badge-info">Kategori</span>';
                    }
                    return '<span class="badge badge-primary">Spesies</span>';
                })

                // Kolom Jumlah Produk
                ->addColumn('product_qty', function ($row) {
                    $count = $row->parent_id ? $row->products_count : $row->children_products_count;
                    return '<span class="badge badge-secondary">' . $count . '</span>';
                })

                // Kolom Status
                ->addColumn('status_badge', function ($row) {
                    $class = $row->status === 'active' ? 'badge-success' : 'badge-danger';
                    return '<span class="badge ' . $class . '">' . ucfirst($row->status) . '</span>';
                })

                // Kolom Aksi — dua class tombol edit berbeda untuk JS
                ->addColumn('action', function ($row) {
                    $isSpecies = is_null($row->parent_id);
                    $editClass = $isSpecies ? 'btn-edit-species' : 'btn-edit-category';

                    $editBtn =
                        '
                        <button class="btn btn-warning btn-sm ' .
                        $editClass .
                        '"
                            data-id="' .
                        $row->id .
                        '"
                            data-name="' .
                        e($row->name) .
                        '"
                            data-parent="' .
                        $row->parent_id .
                        '"
                            data-status="' .
                        $row->status .
                        '"
                            data-description="' .
                        e($row->description) .
                        '">
                            <i class="fas fa-edit"></i>
                        </button>';

                    $deleteBtn =
                        '
                        <form action="' .
                        route('dashboard.categories.destroy', $row->id) .
                        '"
                              method="POST" style="display:inline">
                            ' .
                        csrf_field() .
                        method_field('DELETE') .
                        '
                            <button type="submit" class="btn btn-danger btn-sm show_confirm"
                                data-is-species="' .
                        ($isSpecies ? 1 : 0) .
                        '">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>';

                    return $editBtn . ' ' . $deleteBtn;
                })

                ->rawColumns(['name_display', 'type_badge', 'product_qty', 'status_badge', 'action'])
                ->make(true);
        }

        $parentCategories = Category::whereNull('parent_id')->where('status', 'active')->orderBy('name')->get();

        return view('dashboard.categories.index', compact('parentCategories'));
    }

    // ─────────────────────────────────────────────────────────────
    //  STORE — simpan Spesies ATAU Kategori
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $isSpecies = $request->boolean('is_species');

        // Validasi berbeda tergantung tipe data
        if ($isSpecies) {
            $data = $request->validate(
                [
                    'name' => 'required|string|max:255',
                    'status' => 'required|in:active,inactive',
                    'description' => 'nullable|string|max:500',
                ],
                [
                    'name.required' => 'Nama spesies harus diisi!',
                    'status.required' => 'Status harus diisi!',
                ],
            );

            // Pastikan parent_id null untuk spesies
            $data['parent_id'] = null;
        } else {
            $data = $request->validate(
                [
                    'name' => 'required|string|max:255',
                    'parent_id' => 'required|exists:categories,id',
                    'status' => 'required|in:active,inactive',
                    'description' => 'nullable|string|max:500',
                ],
                [
                    'name.required' => 'Nama kategori harus diisi!',
                    'parent_id.required' => 'Spesies harus dipilih!',
                    'parent_id.exists' => 'Spesies tidak valid!',
                    'status.required' => 'Status harus diisi!',
                ],
            );
        }

        // Buang flag is_species sebelum insert
        unset($data['is_species']);

        Category::create($data);

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', ($isSpecies ? 'Spesies' : 'Kategori') . ' berhasil ditambah!');
    }

    // ─────────────────────────────────────────────────────────────
    //  EDIT — return JSON untuk JS (opsional, jika dibutuhkan AJAX)
    // ─────────────────────────────────────────────────────────────
    public function edit(Category $category)
    {
        return response()->json($category);
    }

    // ─────────────────────────────────────────────────────────────
    //  UPDATE — update Spesies ATAU Kategori
    // ─────────────────────────────────────────────────────────────
    public function update(Request $request, Category $category)
    {
        $isSpecies = is_null($category->parent_id);

        if ($isSpecies) {
            // Spesies: parent_id tetap null, tidak boleh diubah
            $data = $request->validate(
                [
                    'name' => 'required|string|max:255',
                    'status' => 'required|in:active,inactive',
                    'description' => 'nullable|string|max:500',
                ],
                [
                    'name.required' => 'Nama spesies harus diisi!',
                    'status.required' => 'Status harus diisi!',
                ],
            );

            $data['parent_id'] = null; // lock, tidak bisa dipindah
        } else {
            // Kategori: bisa ganti spesies
            $data = $request->validate(
                [
                    'name' => 'required|string|max:255',
                    'parent_id' => 'required|exists:categories,id',
                    'status' => 'required|in:active,inactive',
                    'description' => 'nullable|string|max:500',
                ],
                [
                    'name.required' => 'Nama kategori harus diisi!',
                    'parent_id.required' => 'Spesies harus dipilih!',
                    'parent_id.exists' => 'Spesies tidak valid!',
                    'status.required' => 'Status harus diisi!',
                ],
            );
        }

        $category->update($data);

        // Jika spesies dinonaktifkan, nonaktifkan juga semua kategori di dalamnya
        if ($isSpecies && $category->status === 'inactive') {
            Category::where('parent_id', $category->id)->update(['status' => 'inactive']);
        }

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', ($isSpecies ? 'Spesies' : 'Kategori') . ' berhasil diperbarui!');
    }

    // ─────────────────────────────────────────────────────────────
    //  DESTROY
    // ─────────────────────────────────────────────────────────────
    public function destroy(Category $category)
    {
        $isSpecies = is_null($category->parent_id);
        $category->delete(); // cascade ke children via DB atau model event

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', ($isSpecies ? 'Spesies' : 'Kategori') . ' berhasil dihapus!');
    }
}

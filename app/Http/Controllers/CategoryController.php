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

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Category::with('parent')
                ->withCount(['products', 'childrenProducts'])
                ->orderByRaw('COALESCE(parent_id, id), parent_id IS NOT NULL, id');

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('name_display', function ($row) {
                    if ($row->parent_id) {
                        return '<div class="ml-4 text-muted"><i class="fas fa-level-up-alt fa-rotate-90 mr-1"></i>' . e($row->name) . '</div>';
                    }
                    return '<strong><i class="fas fa-folder-open text-primary mr-1"></i>' . e($row->name) . '</strong>';
                })

                ->addColumn('type_badge', function ($row) {
                    if ($row->parent_id) {
                        return '<span class="badge badge-info">Kategori</span>';
                    }
                    return '<span class="badge badge-primary">Spesies</span>';
                })

                ->addColumn('product_qty', function ($row) {
                    $count = $row->parent_id ? $row->products_count : $row->children_products_count;
                    return '<span class="badge badge-secondary">' . $count . '</span>';
                })

                ->addColumn('status_badge', function ($row) {
                    $class = $row->status === 'active' ? 'badge-success' : 'badge-danger';
                    return '<span class="badge ' . $class . '">' . ucfirst($row->status) . '</span>';
                })

                ->addColumn('action', function ($row) {
                    $editBtn =
                        '
                        <button class="btn btn-warning btn-sm btn-edit" data-id="' .
                        $row->id .
                        '">
                            <i class="fas fa-edit"></i>
                        </button>';

                    $deleteBtn =
                        '
                        <form action="' .
                        route('dashboard.categories.destroy', $row->id) .
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

                ->rawColumns(['name_display', 'type_badge', 'product_qty', 'status_badge', 'action'])
                ->make(true);
        }

        $parentCategories = Category::whereNull('parent_id')->where('status', 'active')->orderBy('name')->get();

        return view('dashboard.categories.index', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $isSpecies = $request->boolean('is_species');

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

        unset($data['is_species']);

        Category::create($data);

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', ($isSpecies ? 'Spesies' : 'Kategori') . ' berhasil ditambah!');
    }

    public function edit(Category $category)
    {
        return response()->json($category);
    }

    public function update(Request $request, Category $category)
    {
        $isSpecies = is_null($category->parent_id);

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

        $category->update($data);

        if ($isSpecies && $category->status === 'inactive') {
            Category::where('parent_id', $category->id)->update(['status' => 'inactive']);
        }

        if ($isSpecies && $category->status === 'active') {
            Category::where('parent_id', $category->id)->update(['status' => 'active']);
        }

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', ($isSpecies ? 'Spesies' : 'Kategori') . ' berhasil diperbarui!');
    }

    public function destroy(Category $category)
    {
        $isSpecies = is_null($category->parent_id);
        $category->delete();

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', ($isSpecies ? 'Spesies' : 'Kategori') . ' berhasil dihapus!');
    }
}

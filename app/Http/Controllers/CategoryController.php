<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Logika pengurutan: Urutkan berdasarkan ID jika Utama, atau urutkan berdasarkan parent_id
            // agar anak selalu berada di bawah bapaknya.
            $data = Category::with('parent')->orderByRaw('COALESCE(parent_id, id), parent_id IS NOT NULL, id');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name_display', function ($row) {
                    if ($row->parent_id) {
                        // Berikan ikon panah dan margin agar menjorok ke dalam
                        return '<div style="margin-left: 30px;"><i class="fas fa-level-up-alt fa-rotate-90 text-muted mr-2"></i>' . $row->name . '</div>';
                    }
                    // Kategori Utama dibuat tebal
                    return '<strong><i class="fas fa-folder-open text-primary mr-2"></i>' . $row->name . '</strong>';
                })
                ->addColumn('type_badge', function ($row) {
                    if ($row->parent_id) {
                        return '<span class="badge badge-light border text-dark shadow-sm">Sub-Kategori</span>';
                    }
                    return '<span class="badge badge-primary shadow-sm">Kategori Utama</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    $class = $row->status == 'active' ? 'badge-success' : 'badge-danger';
                    return '<span class="badge ' . $class . ' text-uppercase">' . $row->status . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                    <button class="btn btn-outline-primary btn-sm editCategory"
                        data-id="' .
                        $row->id .
                        '"
                        data-name="' .
                        $row->name .
                        '"
                        data-parent="' .
                        $row->parent_id .
                        '"
                        data-description="' .
                        $row->description .
                        '"

                        data-status="' .
                        $row->status .
                        '">

                        <i class="fa fa-edit"></i> Edit
                    </button>
                    <form action="' .
                        route('dashboard.categories.destroy', $row->id) .
                        '" method="POST" style="display:inline">
                        ' .
                        csrf_field() .
                        method_field('DELETE') .
                        '
                        <button type="submit" class="btn btn-outline-danger btn-sm show_confirm">
                            <i class="fa fa-trash"></i> Hapus
                        </button>
                    </form>';
                })
                ->rawColumns(['name_display', 'type_badge', 'status_badge', 'action'])
                ->make(true);
        }

        $parentCategories = Category::whereNull('parent_id')->get();
        return view('dashboard.categories.index', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required', 'status' => 'required']);

        Category::create($request->all());

        return redirect()->back()->with('success', 'Kategori berhasil ditambah!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        Category::findOrFail($id)->update($request->all());

        return redirect()->route('dashboard.categories.index')->with('success', 'Kategori diperbarui!');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }
}

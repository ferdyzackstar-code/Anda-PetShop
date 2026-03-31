<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;

class PermissionController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:permission-index|permission-create|permission-edit|permission-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:permission-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:permission-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:permission-delete', ['only' => ['destroy']]);
    } 
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = \Spatie\Permission\Models\Permission::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                    <button class="btn btn-primary btn-sm editPermission" data-id="' .
                        $row->id .
                        '" data-name="' .
                        $row->name .
                        '"><i class="fa fa-edit"></i> Edit</button>
                    <form action="' .
                        route('dashboard.permissions.destroy', $row->id) .
                        '" method="POST" style="display:inline">
                        ' .
                        csrf_field() .
                        method_field('DELETE') .
                        '
                        <button type="submit" class="btn btn-danger btn-sm show_confirm"><i class="fa fa-trash"></i> Hapus</button>
                    </form>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('dashboard.permissions.index');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:permissions,name']);
        \Spatie\Permission\Models\Permission::create(['name' => $request->name]);
        return redirect()->back()->with('success', 'Permission berhasil ditambah!');
    }

    public function destroy($id)
    {
        Permission::findOrFail($id)->delete();
        return back()->with('success', 'Permission berhasil dihapus!');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|unique:permissions,name,' . $id]);
        $permission = Permission::findOrFail($id);
        $permission->update(['name' => $request->name]);

        return redirect()->route('dashboard.permissions.index')->with('success', 'Permission diperbarui!');
    }
}

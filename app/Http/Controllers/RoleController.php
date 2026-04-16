<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class RoleController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:role.index|role.create|role.edit|role.delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:role.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:role.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $datas = Role::with('permissions')->orderBy('created_at', 'desc');

            return DataTables::of($datas)
                ->addIndexColumn() 
                ->addColumn('permission', function ($row) {
                    return $row->permissions
                        ->map(function ($p) {
                            return '<span class="badge bg-label-primary mb-1">' . $p->name . '</span>';
                        })
                        ->implode(' ');
                })
                ->addColumn('action', function ($row) {
                    return '
                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalShowRole' .
                        $row->id .
                        '">
                    <i class="fa-solid fa-circle-info"></i> Detail
                </button>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalEditRole' .
                        $row->id .
                        '">
                    <i class="fa fa-edit"></i> Edit
                </button>
                <form method="POST" action="' .
                        route('dashboard.roles.destroy', $row->id) .
                        '" style="display:inline;">
                    ' .
                        csrf_field() .
                        method_field('DELETE') .
                        '
                    <button type="button" class="btn btn-danger btn-sm show_confirm">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                </form>';
                })
                ->rawColumns(['permission', 'action'])
                ->make(true);
        }
        $allPermissions = Permission::orderBy('name', 'asc')->get();
        $roles = Role::with('permissions')->get();
        $groupedPermissions = $allPermissions->groupBy(function ($item) {
            return explode('.', $item->name)[0];
        });

        return view('dashboard.roles.index', [
            'roles' => $roles,
            'groupedPermissions' => $groupedPermissions,
            'permission' => $allPermissions, 
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);

        // Memastikan input permission berupa ID integer
        $permissionsID = array_map('intval', $request->input('permission'));
        $role->syncPermissions($permissionsID);

        return redirect()->route('dashboard.roles.index')->with('success', 'Role created successfully');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $permissionsID = array_map('intval', $request->input('permission'));
        $role->syncPermissions($permissionsID);

        return redirect()->route('dashboard.roles.index')->with('success', 'Role updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        DB::table('roles')->where('id', $id)->delete();
        return redirect()->route('dashboard.roles.index')->with('success', 'Role deleted successfully');
    }

    public function exportPdf($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permission = Permission::orderBy('name', 'asc')->get();

        $groupedPermissions = $permission->groupBy(function ($item) {
            return explode('-', $item->name)[0];
        });

        $pdf = Pdf::loadView('dashboard.roles.pdf', compact('role', 'groupedPermissions'));

        return $pdf->download('Role-' . $role->name . '.pdf');
    }
}

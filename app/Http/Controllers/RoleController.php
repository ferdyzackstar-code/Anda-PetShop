<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $datas = Role::with('permissions')->orderBy('created_at', 'desc')->get();
            return DataTables::of($datas)
                ->addIndexColumn() // M                ->addIndexColumn()

                ->addColumn('nomor', function ($datas) {
                    static $counter = 0; // Variabel untuk menghitung nomor urut
                    return ++$counter; // Kembalikan nomor urut yang ditingkatkan
                })
                ->addColumn('name', function ($datas) {
                    return $datas->name ?? 'N/A'; // Kembalikan nama atau 'N/A' jika kosong
                })

                ->addColumn('permission', function ($datas) {
                    // Gabungkan semua permission dalam satu string dengan badge
                    return $datas->permissions
                        ->map(function ($permission) {
                            return '<span class="badge bg-label-primary mb-1">' . $permission->name . '</span>';
                        })
                        ->implode(' ');
                })

                ->addColumn('action', function ($datas) {
                    return '<button type="button" class="btn btn-info btn-sm mr-1" data-toggle="modal" data-target="#modalShowRole' .
                        $datas->id .
                        '">
                            <i class="fa fa-eye"></i> Show
                        </button>
                        <button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#modalEditRole' .
                        $datas->id .
                        '">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <form method="POST" action="' .
                        route('dashboard.roles.destroy', $datas->id) .
                        '" class="delete-form" style="display:inline;">
                            ' .
                        csrf_field() .
                        '
                            <input name="_method" type="hidden" value="DELETE">
                            <button type="button" class="btn btn-danger btn-sm show_confirm">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </form>';
                })

                ->rawColumns(['name', 'action', 'nomor', 'permission'])
                ->make(true);
        }

        $permission = Permission::get();
        $roles = Role::with('permissions')->get();

        $datasRole = [($permission = Permission::get()), ($roles = Role::with('permissions')->get())];

        return view('dashboard.roles.index', compact('permission', 'roles', 'datasRole'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $permission = Permission::get();
        return view('dashboard.roles.create', compact('permission'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $permissionsID = array_map(function ($value) {
            return (int) $value;
        }, $request->input('permission'));

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($permissionsID);

        return redirect()->route('dashboard.roles.index')->with('success', 'Role created successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join('role_has_permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')->where('role_has_permissions.role_id', $id)->get();

        return view('dashboard.roles.show', compact('role', 'rolePermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table('role_has_permissions')->where('role_has_permissions.role_id', $id)->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')->all();

        return view('dashboard.dasboard.roles.edit', compact('role', 'permission', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $permissionsID = array_map(function ($value) {
            return (int) $value;
        }, $request->input('permission'));

        $role->syncPermissions($permissionsID);

        return redirect()->route('dashboard.roles.index')->with('success', 'Role updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): RedirectResponse
    {
        DB::table('roles')->where('id', $id)->delete();
        return redirect()->route('dashboard.roles.index')->with('success', 'Role deleted successfully');
    }
}

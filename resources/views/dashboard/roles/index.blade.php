@extends('dashboard.layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Role Management</h2>
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-success btn-sm mb-2" data-toggle="modal" data-target="#modalCreateRole">
                    <i class="fa fa-plus"></i> Create New Role
                </button>
            </div>
        </div>
    </div>

    @session('success')
        <div class="alert alert-success" role="alert">{{ $value }}</div>
    @endsession

    <table class="table table-bordered bg-white shadow-sm">
        <thead class="bg-light">
            <tr>
                <th width="100px">No</th>
                <th>Name</th>
                <th width="280px">Action</th>
            </tr>
        </thead>
        @foreach ($roles as $key => $role)
            <tr>
                <td>{{ ++$i }}</td>
                <td><span class="badge bg-success text-white">{{ $role->name }}</span></td>
                <td>
                    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalShowRole{{ $role->id }}">
                        <i class="fa fa-eye"></i> Show
                    </button>

                    <button class="btn btn-primary btn-sm" data-toggle="modal"
                        data-target="#modalEditRole{{ $role->id }}">
                        <i class="fa fa-edit"></i> Edit
                    </button>

                    @can('role-delete')
                        <form method="POST" action="{{ route('dashboard.roles.destroy', $role->id) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus role ini?')">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </form>
                    @endcan
                </td>
            </tr>
            @include('dashboard.roles.modals.show', [
                'role' => $role,
                'rolePermissions' => $role->permissions,
            ])
            @include('dashboard.roles.modals.edit', ['role' => $role, 'permission' => $permission])
        @endforeach
    </table>

    @include('dashboard.roles.modals.create', ['permission' => $permission])

    {!! $roles->links('pagination::bootstrap-5') !!}
@endsection

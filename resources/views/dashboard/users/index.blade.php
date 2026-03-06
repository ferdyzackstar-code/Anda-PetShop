@extends('dashboard.layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Users Management</h2>
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-success btn-sm mb-2" data-toggle="modal" data-target="#modalCreateUser">
                    <i class="fa fa-plus"></i> Create New User
                </button>
            </div>
        </div>
    </div>

    @session('success')
        <div class="alert alert-success" role="alert">{{ $value }}</div>
    @endsession

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Email</th>
            <th>Roles</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($data as $key => $user)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @foreach ($user->getRoleNames() as $v)
                        <label class="badge bg-success text-white">{{ $v }}</label>
                    @endforeach
                </td>
                <td>
                    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalShowUser{{ $user->id }}">
                        <i class="fa fa-eye"></i> Show
                    </button>
                    <button class="btn btn-primary btn-sm" data-toggle="modal"
                        data-target="#modalEditUser{{ $user->id }}">
                        <i class="fa fa-edit"></i> Edit
                    </button>

                    <form method="POST" action="{{ route('dashboard.users.destroy', $user->id) }}" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus user?')">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </form>
                </td>
            </tr>
            @include('dashboard.users.modals.show', ['user' => $user])
            @include('dashboard.users.modals.edit', ['user' => $user, 'roles' => $roles])
        @endforeach
    </table>

    @include('dashboard.users.modals.create', ['roles' => $roles])

    {!! $data->links('pagination::bootstrap-5') !!}
@endsection

@extends('dashboard.layouts.admin')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Outlet</h1>
        <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addOutletModal">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Outlet Baru
        </button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>No</th>
                            <th>Nama Outlet</th>
                            <th>Alamat</th>
                            <th>Telepon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($outlets as $key => $outlet)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $outlet->name }}</td>
                                <td>{{ $outlet->address }}</td>
                                <td>{{ $outlet->phone ?? '-' }}</td>
                                <td>
                                    <form action="{{ route('dashboard.outlets.destroy', $outlet->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Hapus outlet ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addOutletModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('dashboard.outlets.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5>Tambah Outlet</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Outlet</label>
                            <input type="text" name="name" class="form-control"
                                placeholder="Contoh: Anda Petshop Cipinang" required>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="address" class="form-control" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

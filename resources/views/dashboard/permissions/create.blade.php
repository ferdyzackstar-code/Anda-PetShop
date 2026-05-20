@extends('dashboard.layouts.admin')

@section('title', 'Tambah Hak Akses — Anda Petshop')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')

    <div class="card w-100 border-0 shadow-sm mb-3">
        <div class="card-body py-3 px-4 bg-primary rounded d-flex align-items-center justify-content-between"
            style="flex-wrap: wrap; gap: 1rem;">
            <h5 class="mb-0 text-white font-weight-bold">
                <i class="fas fa-key mr-2"></i> Tambah Hak Akses
            </h5>
            <a href="{{ route('dashboard.permissions.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card w-100 border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-tachometer-alt text-primary mr-2"></i>
                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted mr-2">Dashboard</a> >
                <a href="{{ route('dashboard.permissions.index') }}" class="text-decoration-none text-muted mr-2 ml-2">Hak Akses</a> >
                <span class="font-weight-bold text-primary ml-2">Tambah</span>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-exclamation-circle mr-1"></i> Terjadi Kesalahan:</strong>
            <ul class="mb-0 mt-2 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Hak Akses Baru
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('dashboard.permissions.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="permissionName" class="font-weight-bold text-gray-700 small">
                        Nama Hak Akses <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" id="permissionName"
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder="Contoh: product.create atau category.delete" value="{{ old('name') }}"
                        autocomplete="off">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah Hak Akses
                    </button>
                </div>

            </form>
        </div>
    </div>

@endsection

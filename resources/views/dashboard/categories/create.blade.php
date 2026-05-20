@extends('dashboard.layouts.admin')

@section('title', 'Tambah Kategori — Anda Petshop')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')

    <div class="card w-100 border-0 shadow-sm mb-3">
        <div class="card-body py-3 px-4 bg-primary rounded d-flex align-items-center justify-content-between"
            style="flex-wrap: wrap; gap: 1rem;">
            <h5 class="mb-0 text-white font-weight-bold">
                <i class="fas fa-tags mr-2"></i> Tambah Kategori
            </h5>
            <a href="{{ route('dashboard.categories.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card w-100 border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-tachometer-alt text-primary mr-2"></i>
                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted mr-2">Dashboard</a> >
                <a href="{{ route('dashboard.categories.index') }}" class="text-decoration-none text-muted mr-2 ml-2">Kategori</a> >
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
        <div class="card-header py-0 border-bottom-0">
            <ul class="nav nav-tabs card-header-tabs" id="categoryTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link font-weight-bold {{ old('active_tab', 'species') === 'species' ? 'active' : '' }}"
                        id="tab-species" data-toggle="tab" href="#panel-species" role="tab">
                        <i class="fas fa-folder-open mr-1"></i> Spesies
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold {{ old('active_tab') === 'category' ? 'active' : '' }}"
                        id="tab-category" data-toggle="tab" href="#panel-category" role="tab">
                        <i class="fas fa-tag mr-1"></i> Kategori
                    </a>
                </li>
            </ul>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade {{ old('active_tab', 'species') === 'species' ? 'show active' : '' }}"
                id="panel-species" role="tabpanel">
                <div class="card-body">
                    <h6 class="font-weight-bold text-primary mb-3">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Spesies Baru
                    </h6>
                    <form action="{{ route('dashboard.categories.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="is_species" value="1">
                        <input type="hidden" name="active_tab" value="species">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-700 small">
                                        Nama Spesies <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Misal: Anjing, Kucing, Ikan" value="{{ old('name') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-700 small">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-700 small">Deskripsi</label>
                                    <input type="text" name="description" class="form-control" placeholder="Opsional"
                                        value="{{ old('description') }}">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex mt-1">
                            <button type="submit" class="btn btn-primary btn-sm mr-2">
                                <i class="fas fa-plus mr-1"></i> Tambah Spesies
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="tab-pane fade {{ old('active_tab') === 'category' ? 'show active' : '' }}" id="panel-category"
                role="tabpanel">
                <div class="card-body">
                    <h6 class="font-weight-bold text-primary mb-3">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Kategori Baru
                    </h6>
                    <form action="{{ route('dashboard.categories.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="active_tab" value="category">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-700 small">
                                        Nama Kategori <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Misal: Makanan, Aksesoris" value="{{ old('name') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-700 small">
                                        Spesies <span class="text-danger">*</span>
                                    </label>
                                    <select name="parent_id"
                                        class="form-control @error('parent_id') is-invalid @enderror">
                                        <option value="">— Pilih Spesies —</option>
                                        @foreach ($parentCategories as $parent)
                                            <option value="{{ $parent->id }}"
                                                {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-700 small">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                        <option value="active"
                                            {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-700 small">Deskripsi</label>
                                    <input type="text" name="description" class="form-control" placeholder="Opsional"
                                        value="{{ old('description') }}">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex mt-1">
                            <button type="submit" class="btn btn-primary btn-sm mr-2">
                                <i class="fas fa-plus mr-1"></i> Tambah Kategori
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@extends('dashboard.layouts.admin')

@section('title', 'Edit Kategori — Anda Petshop')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')

    <div class="card w-100 border-0 shadow-sm mb-3">
        <div class="card-body py-3 px-4 bg-warning rounded d-flex align-items-center justify-content-between" style="flex-wrap: wrap; gap: 1rem;">
            <h5 class="mb-0 text-white font-weight-bold">
                <i class="fas fa-tags mr-2"></i> Edit Produk
            </h5>
            <a href="{{ route('dashboard.categories.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <x-breadcrumb :items="[['label' => 'Kategori', 'url' => route('dashboard.categories.index')], ['label' => 'Edit']]" />

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
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-edit mr-1"></i>
                Edit {{ $isSpecies ? 'Spesies' : 'Kategori' }}: <strong>{{ $category->name }}</strong>
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('dashboard.categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')
                @if ($isSpecies)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-700 small">
                                    Nama Spesies <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Misal: Anjing, Kucing, Ikan" value="{{ old('name', $category->name) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-700 small">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select name="status" class="form-control @error('status') is-invalid @enderror">
                                    <option value="active"
                                        {{ old('status', $category->status) == 'active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="inactive"
                                        {{ old('status', $category->status) == 'inactive' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-700 small">Deskripsi</label>
                                <input type="text" name="description" class="form-control" placeholder="Opsional"
                                    value="{{ old('description', $category->description) }}">
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-700 small">
                                    Nama Kategori <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Misal: Makanan, Aksesoris" value="{{ old('name', $category->name) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-700 small">
                                    Spesies <span class="text-danger">*</span>
                                </label>
                                <select name="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                                    <option value="">— Pilih Spesies —</option>
                                    @foreach ($parentCategories as $parent)
                                        <option value="{{ $parent->id }}"
                                            {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
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
                                        {{ old('status', $category->status) == 'active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="inactive"
                                        {{ old('status', $category->status) == 'inactive' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-700 small">Deskripsi</label>
                                <input type="text" name="description" class="form-control" placeholder="Opsional"
                                    value="{{ old('description', $category->description) }}">
                            </div>
                        </div>
                    </div>
                @endif

                <div class="d-flex mt-2">
                    <button type="submit" class="btn btn-warning btn-sm mr-2">
                        <i class="fas fa-save mr-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

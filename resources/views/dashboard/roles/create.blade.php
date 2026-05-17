@extends('dashboard.layouts.admin')

@section('title', 'Tambah Peran — Anda Petshop')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')

    <x-breadcrumb :items="[['label' => 'Peran', 'url' => route('dashboard.roles.index')], ['label' => 'Tambah']]" />

    <div class="card w-100 border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-4 bg-primary rounded d-flex align-items-center justify-content-between"
            style="flex-wrap: wrap; gap: 1rem;">
            <h5 class="mb-0 text-white font-weight-bold">
                <i class="fas fa-user-shield mr-2"></i> Tambah Peran
            </h5>
            <a href="{{ route('dashboard.roles.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
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
                <i class="fas fa-plus-circle mr-1"></i> Tambah Peran Baru
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('dashboard.roles.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="roleName" class="font-weight-bold text-gray-700 small">
                        Nama Peran <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" id="roleName"
                        class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Manager atau Kasir"
                        value="{{ old('name') }}" autocomplete="off">
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="font-weight-bold text-gray-700 small mb-0">
                        <i class="fas fa-key mr-1"></i> Hak Akses <span class="text-danger">*</span>
                    </label>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="checkAll">
                        <label class="custom-control-label small font-weight-bold" for="checkAll" style="cursor:pointer;">
                            Pilih Semua
                        </label>
                    </div>
                </div>
                <hr class="mt-1 mb-3">

                <div class="row mb-4">
                    @foreach ($groupedPermissions as $group => $permissions)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <h6 class="font-weight-bold text-uppercase border-bottom pb-2 mb-3 text-primary"
                                style="font-size:.78rem;">
                                <i class="fas fa-folder-open mr-1"></i> {{ $group }}
                            </h6>
                            @foreach ($permissions as $value)
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" name="permission[]" value="{{ $value->id }}"
                                        class="custom-control-input perm-check" id="perm_{{ $value->id }}">
                                    <label class="custom-control-label small" for="perm_{{ $value->id }}"
                                        style="cursor:pointer;">
                                        {{ explode('.', $value->name)[1] ?? $value->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah Peran
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            {{-- Checkbox Pilih Semua --}}
            $('#checkAll').on('change', function() {
                $('.perm-check').prop('checked', this.checked);
            });

            $(document).on('change', '.perm-check', function() {
                $('#checkAll').prop('checked',
                    $('.perm-check').length === $('.perm-check:checked').length);
            });

        });
    </script>
@endpush

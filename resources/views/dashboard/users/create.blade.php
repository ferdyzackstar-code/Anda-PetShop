@extends('dashboard.layouts.admin')

@section('title', 'Tambah Pengguna — Anda Petshop')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')

    <x-breadcrumb :items="[['label' => 'Pengguna', 'url' => route('dashboard.users.index')], ['label' => 'Tambah']]" />

    <div class="card w-100 border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-4 bg-primary rounded d-flex align-items-center justify-content-between"
            style="flex-wrap: wrap; gap: 1rem;">
            <h5 class="mb-0 text-white font-weight-bold">
                <i class="fas fa-users mr-2"></i> Tambah Pengguna
            </h5>
            <a href="{{ route('dashboard.users.index') }}" class="btn btn-light btn-sm">
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
                <i class="fas fa-plus-circle mr-1"></i> Tambah Pengguna Baru
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('dashboard.users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                placeholder="Masukkan nama lengkap" value="{{ old('name') }}">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                placeholder="Masukkan email" value="{{ old('email') }}">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">
                                Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Masukkan password">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">
                                Konfirmasi Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="confirm-password"
                                class="form-control @error('confirm-password') is-invalid @enderror"
                                placeholder="Ulangi password">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">Foto Profil</label>
                            <div class="d-flex align-items-start">
                                <img id="previewFoto" src="{{ asset('storage/uploads/users/default-user.jpg') }}"
                                    class="img-thumbnail mr-3" style="width:80px; height:80px; object-fit:cover;">
                                <div class="flex-fill">
                                    <input type="file" name="image"
                                        class="form-control-file @error('image') is-invalid @enderror"
                                        accept="image/jpeg,image/png,image/jpg"
                                        onchange="previewImage(this, 'previewFoto')">
                                    <small class="text-muted">Format: JPG, PNG. Maks: 2MB.</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">Bio</label>
                            <textarea name="bio" class="form-control" rows="3" placeholder="Deskripsi singkat (opsional)">{{ old('bio') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">
                                Peran <span class="text-danger">*</span>
                            </label>
                            <select name="roles" class="form-control @error('roles') is-invalid @enderror">
                                <option value="">-- Pilih Peran --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}" {{ old('roles') == $role ? 'selected' : '' }}>
                                        {{ $role }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex mt-2">
                    <button type="submit" class="btn btn-primary btn-sm mr-2">
                        <i class="fas fa-plus mr-1"></i> Tambah Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => document.getElementById(previewId).src = e.target.result;
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush

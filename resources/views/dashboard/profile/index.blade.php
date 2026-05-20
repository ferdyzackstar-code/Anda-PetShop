@extends('dashboard.layouts.admin')

@section('title', 'Profil Saya — Anda Petshop')

@section('content')

    <div class="card w-100 border-0 shadow-sm mb-3">
        <div class="card-body py-3 px-4 bg-primary rounded">
            <h5 class="mb-0 text-white font-weight-bold">
                <i class="fas fa-user-circle mr-2"></i> Profil Saya
            </h5>
        </div>
    </div>

    <div class="card w-100 border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-tachometer-alt text-primary mr-2"></i>
                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted mr-2">Dashboard</a> >
                <span class="font-weight-bold text-primary ml-2">Profil</span>
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
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ route('dashboard.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-info">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-camera mr-2"></i> Foto Profil
                </h6>
            </div>
            <div class="card-body">
                <div class="row align-items-center">

                    <div class="col-lg-3 text-center mb-4 mb-lg-0">
                        @php
                            $photoPath = 'storage/uploads/users/' . $user->image;
                            $photoUrl =
                                $user->image && file_exists(public_path($photoPath))
                                    ? asset($photoPath)
                                    : asset('storage/uploads/users/default-user.jpg');
                        @endphp
                        <img id="previewProfile" src="{{ $photoUrl }}" alt="{{ $user->name }}"
                            class="rounded-circle shadow"
                            style="width:130px; height:130px; object-fit:cover; border:4px solid #e3e6f0;">
                    </div>

                    <div class="col-lg-9">
                        <label class="font-weight-bold mb-2 d-block">
                            <i class="fas fa-image-portrait text-info mr-1"></i> Ganti Foto Profil
                        </label>
                        <div class="custom-file mb-1">
                            <input type="file" class="custom-file-input" id="imgInput" name="image" accept="image/*"
                                onchange="previewImage('imgInput', 'previewProfile')">
                            <label class="custom-file-label" for="imgInput">Pilih Foto...</label>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-info-circle mr-1"></i> Format: JPG, PNG. Maksimal 2MB.
                        </small>
                    </div>

                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-id-card mr-2"></i> Data Diri
                </h6>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold mb-2 d-block">
                            <i class="fas fa-user-pen text-success mr-1"></i> Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name) }}" required placeholder="Nama lengkap Anda">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold mb-2 d-block">
                            <i class="fas fa-envelope text-success mr-1"></i> Email
                        </label>
                        <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly disabled>
                        <small class="text-muted d-block mt-1">Email tidak dapat diubah.</small>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="font-weight-bold mb-2 d-block">
                            <i class="fas fa-pen text-success mr-1"></i> Bio / Tentang Saya
                        </label>
                        <textarea name="bio" class="form-control @error('bio') is-invalid @enderror" rows="4"
                            placeholder="Ceritakan sedikit tentang dirimu...">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-5">
            <a href="{{ url()->previous() }}" class="btn btn-secondary mr-2">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save mr-1"></i> Simpan Perubahan
            </button>
        </div>

    </form>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('imgInput');
            if (input) {
                input.addEventListener('change', function() {
                    const label = this.nextElementSibling;
                    if (label) label.textContent = this.files[0]?.name || 'Pilih Foto...';
                });
            }
        });
    </script>
@endpush

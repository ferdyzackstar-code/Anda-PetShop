@extends('dashboard.layouts.admin')

@section('title', 'Edit Produk — Anda Petshop')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')

    <div class="card w-100 border-0 shadow-sm mb-3">
        <div class="card-body py-3 px-4 bg-warning rounded d-flex align-items-center justify-content-between"
            style="flex-wrap: wrap; gap: 1rem;">
            <h5 class="mb-0 text-white font-weight-bold">
                <i class="fas fa-box-open mr-2"></i> Edit Produk
            </h5>
            <a href="{{ route('dashboard.products.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <x-breadcrumb :items="[['label' => 'Produk', 'url' => route('dashboard.products.index')], ['label' => 'Edit']]" />

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
                <i class="fas fa-edit mr-1"></i> Edit Produk: <strong>{{ $product->name }}</strong>
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('dashboard.products.update', $product->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">
                                Nama Produk <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                placeholder="Contoh: Whiskas Tuna 1kg" value="{{ old('name', $product->name) }}">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">
                                Harga (Rp) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" name="price" id="inputPrice"
                                    class="form-control @error('price') is-invalid @enderror" placeholder="50.000"
                                    value="{{ old('price') ? number_format((int) old('price'), 0, ',', '.') : number_format($product->price, 0, ',', '.') }}"
                                    autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">
                                Stok <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror"
                                placeholder="0" min="0" value="{{ old('stock', $product->stock) }}">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" class="form-control @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>
                                    Active</option>
                                <option value="inactive"
                                    {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        @php
                            $selectedSpecies = old('species_id', optional(optional($product->category)->parent)->id);
                            $selectedCategory = old('category_id', $product->category_id);
                        @endphp

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">
                                Spesies <span class="text-danger">*</span>
                            </label>
                            <select name="species_id" id="inputSpecies"
                                class="form-control @error('category_id') is-invalid @enderror">
                                <option value="">-- Pilih Spesies --</option>
                                @foreach ($parentCategories as $parent)
                                    <option value="{{ $parent->id }}"
                                        {{ $selectedSpecies == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">
                                Kategori <span class="text-danger">*</span>
                            </label>
                            <select name="category_id" id="inputCategory"
                                class="form-control @error('category_id') is-invalid @enderror">
                                <option value="">Memuat...</option>
                            </select>
                            <small class="text-muted">Pilih spesies terlebih dahulu.</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">Foto Produk</label>
                            <div class="d-flex align-items-start">
                                @php
                                    $imgPath = 'storage/uploads/products/' . $product->image;
                                    $imgUrl =
                                        $product->image && file_exists(public_path($imgPath))
                                            ? asset($imgPath)
                                            : asset('storage/uploads/products/default-product.jpg');
                                @endphp
                                <img id="previewFoto" src="{{ $imgUrl }}" class="img-thumbnail mr-3"
                                    style="width:80px; height:80px; object-fit:cover;">
                                <div class="flex-fill">
                                    <input type="file" name="image"
                                        class="form-control-file @error('image') is-invalid @enderror"
                                        accept="image/jpeg,image/png,image/jpg"
                                        onchange="previewImage(this, 'previewFoto')">
                                    <small class="text-muted">Format: JPG, PNG. Maks: 2MB. Kosongkan jika tidak ingin
                                        mengubah foto.</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">Deskripsi</label>
                            <textarea name="detail" class="form-control" rows="3" placeholder="Deskripsi produk (opsional)">{{ old('detail', $product->detail) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex mt-2">
                    <button type="submit" class="btn btn-warning btn-sm mr-2">
                        <i class="fas fa-save mr-1"></i> Update Produk
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            var speciesId = "{{ $selectedSpecies }}";
            var categoryId = "{{ $selectedCategory }}";
            if (speciesId) {
                fetchCategories(speciesId, categoryId);
            }

            $('#inputSpecies').on('change', function() {
                fetchCategories($(this).val(), null);
            });

            $('form').on('submit', function() {
                $('#inputPrice').val($('#inputPrice').val().replace(/\./g, ''));
            });

            $('#inputPrice').on('keyup', function() {
                $(this).val(formatRupiah($(this).val()));
            });

        });

        function fetchCategories(speciesId, preselectId) {
            var $cat = $('#inputCategory');
            if (!speciesId) {
                $cat.prop('disabled', true).html('<option value="">-- Pilih Spesies Dulu --</option>');
                return;
            }
            $cat.prop('disabled', true).html('<option value="">Memuat...</option>');
            $.ajax({
                url: '{{ url('dashboard/get-subcategories') }}/' + speciesId,
                type: 'GET',
                success: function(data) {
                    $cat.html('<option value="">-- Pilih Kategori --</option>');
                    $.each(data, function(i, item) {
                        $cat.append('<option value="' + item.id + '">' + item.name + '</option>');
                    });
                    $cat.prop('disabled', data.length === 0);
                    if (preselectId) $cat.val(preselectId);
                },
                error: function() {
                    Swal.fire('Gagal', 'Gagal memuat kategori.', 'error');
                    $cat.html('<option value="">-- Error --</option>').prop('disabled', false);
                }
            });
        }

        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function formatRupiah(angka) {
            var str = String(angka).replace(/[^,\d]/g, '');
            var parts = str.split(',');
            var sisa = parts[0].length % 3;
            var rp = parts[0].substr(0, sisa);
            var ribuan = parts[0].substr(sisa).match(/\d{3}/gi);
            if (ribuan) rp += (sisa ? '.' : '') + ribuan.join('.');
            return parts[1] !== undefined ? rp + ',' + parts[1] : rp;
        }
    </script>
@endpush

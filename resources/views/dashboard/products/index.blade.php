{{-- resources/views/dashboard/products/index.blade.php --}}
@extends('dashboard.layouts.admin')

@section('title', 'Manajemen Produk — Anda Petshop')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')

    {{-- ================================
         HEADER HALAMAN
    ================================ --}}
    <div class="card w-100 border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-4 bg-primary rounded">
            <h5 class="mb-0 text-white font-weight-bold">
                <i class="fas fa-box-open mr-2"></i> Manajemen Produk
            </h5>
        </div>
    </div>

    {{-- ================================
         ALERT IMPORT FAILURES
    ================================ --}}
    @if (session()->has('import_failures'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-exclamation-triangle mr-1"></i> Beberapa baris gagal diimport:</strong>
            <ul class="mb-0 mt-2 pl-3">
                @foreach (session()->get('import_failures') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- ================================
         ALERT ERROR VALIDASI
    ================================ --}}
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

    {{-- ================================
         FORM TAMBAH / EDIT
    ================================ --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary" id="cardTitle">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Produk Baru
            </h6>
        </div>
        <div class="card-body">
            <form id="productForm" action="{{ route('dashboard.products.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="row">

                    {{-- ── Kolom Kiri ── --}}
                    <div class="col-md-6">

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">
                                Nama Produk <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="inputName"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Contoh: Whiskas Tuna 1kg" value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                    value="{{ old('price') }}" autocomplete="off">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">
                                Stok <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="stock" id="inputStock"
                                class="form-control @error('stock') is-invalid @enderror" placeholder="0" min="0"
                                value="{{ old('stock') }}">
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" id="inputStatus"
                                class="form-control @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive"{{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    {{-- ── Kolom Kanan ── --}}
                    <div class="col-md-6">

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">
                                Spesies <span class="text-danger">*</span>
                            </label>
                            <select id="inputSpecies" class="form-control @error('category_id') is-invalid @enderror">
                                <option value="">-- Pilih Spesies --</option>
                                @foreach ($parentCategories as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">
                                Kategori <span class="text-danger">*</span>
                            </label>
                            <select name="category_id" id="inputCategory"
                                class="form-control @error('category_id') is-invalid @enderror" disabled>
                                <option value="">-- Pilih Spesies Dulu --</option>
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Pilih spesies terlebih dahulu.</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">Foto Produk</label>
                            <div class="d-flex align-items-start">
                                <img id="previewFoto" src="{{ asset('storage/uploads/products/default-product.jpg') }}"
                                    class="img-thumbnail mr-3" style="width:80px; height:80px; object-fit:cover;">
                                <div class="flex-fill">
                                    <input type="file" name="image" id="inputFoto"
                                        class="form-control-file @error('image') is-invalid @enderror"
                                        accept="image/jpeg,image/png,image/jpg"
                                        onchange="previewImage(this, 'previewFoto')">
                                    <small class="text-muted">Format: JPG, PNG. Maks: 2MB.</small>
                                    @error('image')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">Deskripsi</label>
                            <textarea name="detail" id="inputDetail" class="form-control" rows="3"
                                placeholder="Deskripsi produk (opsional)">{{ old('detail') }}</textarea>
                        </div>

                    </div>

                </div>

                {{-- Tombol Aksi --}}
                <div class="d-flex mt-2">
                    <button type="submit" class="btn btn-primary btn-sm mr-2" id="submitBtn">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" id="resetBtn">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- ================================
         IMPORT / EXPORT
    ================================ --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3 d-flex flex-wrap align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-exchange-alt mr-1"></i> Import & Export Data
            </h6>
            <div class="d-flex flex-wrap mt-2 mt-sm-0" id="importExportBtns">
                <a href="{{ route('dashboard.products.downloadImportTemplate') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-file-download mr-1"></i> Template Import
                </a>
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalImport">
                    <i class="fas fa-upload mr-1"></i> Import
                </button>
                <a href="{{ route('dashboard.products.export') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-file-export mr-1"></i> Export
                </a>
            </div>
        </div>
    </div>

    {{-- ================================
         TABEL DATA
    ================================ --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" style="min-width: 900px;" id="table-products">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th width="1%" class="text-center">No</th>
                            <th width="6%" class="text-center">Foto</th>
                            <th>Nama Produk</th>
                            <th>Spesies</th>
                            <th>Kategori</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Harga</th>
                            <th class="text-center">Stok</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ================================
         MODAL IMPORT
    ================================ --}}
    <div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow">
                <div class="modal-header bg-success">
                    <h5 class="modal-title font-weight-bold text-white">
                        <i class="fas fa-upload mr-1"></i> Import Data Produk
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="{{ route('dashboard.products.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-gray-700 small">Pilih File</label>
                            <input type="file" name="file" class="form-control-file" required
                                accept=".xlsx,.xls,.csv">
                            <small class="text-muted">Format yang diterima: XLSX, XLS, CSV.</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-upload mr-1"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {

            // ── DataTable ─────────────────────────────────────────────
            $('#table-products').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                ajax: "{{ route('dashboard.products.index') }}",
                language: {
                    processing: 'Memuat data...',
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    infoFiltered: '(difilter dari _MAX_ total data)',
                    zeroRecords: 'Tidak ada data yang ditemukan',
                    emptyTable: 'Tidak ada data tersedia',
                    paginate: {
                        first: 'Pertama',
                        previous: 'Sebelumnya',
                        next: 'Berikutnya',
                        last: 'Terakhir'
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle'
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        className: 'align-middle'
                    },
                    {
                        data: 'species',
                        name: 'species',
                        orderable: false,
                        searchable: false,
                        className: 'align-middle'
                    },
                    {
                        data: 'category',
                        name: 'category',
                        orderable: false,
                        searchable: false,
                        className: 'align-middle'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle'
                    },
                    {
                        data: 'price',
                        name: 'price',
                        className: 'text-right align-middle'
                    },
                    {
                        data: 'stock',
                        name: 'stock',
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle'
                    }
                ]
            });

            // ── Klik Tombol Edit → AJAX ambil data fresh dari server ──
            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('dashboard.products.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function(product) {

                        // Isi field kiri
                        $('#inputName').val(product.name);
                        $('#inputPrice').val(product.price_formatted);
                        $('#inputStock').val(product.stock);
                        $('#inputStatus').val(product.status);

                        // Isi field kanan — dropdown bertingkat
                        // 1. Set spesies dulu
                        $('#inputSpecies').val(product.species_id);

                        // 2. Fetch kategori berdasarkan spesies, lalu preselect
                        fetchCategories(product.species_id, product.category_id);

                        // Foto
                        $('#previewFoto').attr('src', product.image_url);

                        // Deskripsi
                        $('#inputDetail').val(product.detail);

                        // Ganti judul & warna card → warning (mode edit)
                        $('#cardTitle')
                            .html('<i class="fas fa-edit mr-1"></i> Edit Produk: <strong>' +
                                product.name + '</strong>')
                            .removeClass('text-primary').addClass('text-warning');

                        // Ganti tombol → warning
                        $('#submitBtn')
                            .html('<i class="fas fa-save mr-1"></i> Update')
                            .removeClass('btn-primary').addClass('btn-warning');

                        // Spoof PUT & ubah action
                        $('#formMethod').val('PUT');
                        $('#productForm').attr(
                            'action',
                            "{{ route('dashboard.products.update', ':id') }}".replace(
                                ':id', product.id)
                        );

                        // Scroll ke form
                        $('html, body').animate({
                            scrollTop: $('#productForm').offset().top - 80
                        }, 300);
                    },
                    error: function() {
                        Swal.fire('Gagal', 'Data produk tidak ditemukan.', 'error');
                    }
                });
            });

            // ── Dropdown Spesies → fetch Kategori ─────────────────────
            $('#inputSpecies').on('change', function() {
                fetchCategories($(this).val(), null);
            });

            // ── Reset → kembali ke mode Tambah ────────────────────────
            $('#resetBtn').on('click', function() {
                $('#inputName, #inputPrice, #inputDetail').val('');
                $('#inputStock').val('');
                $('#inputStatus').val('active');
                $('#inputSpecies').val('');
                $('#inputFoto').val('');
                $('#previewFoto').attr('src',
                    "{{ asset('storage/uploads/products/default-product.jpg') }}");

                // Reset dropdown kategori
                $('#inputCategory')
                    .prop('disabled', true)
                    .html('<option value="">-- Pilih Spesies Dulu --</option>');

                // Kembalikan judul & warna card → primary
                $('#cardTitle')
                    .html('<i class="fas fa-plus-circle mr-1"></i> Tambah Produk Baru')
                    .removeClass('text-warning').addClass('text-primary');

                $('#submitBtn')
                    .html('<i class="fas fa-plus mr-1"></i> Tambah')
                    .removeClass('btn-warning').addClass('btn-primary');

                $('#formMethod').val('POST');
                $('#productForm').attr('action', "{{ route('dashboard.products.store') }}");
            });

            // ── SweetAlert konfirmasi sebelum Delete ──────────────────
            $(document).on('click', '.show_confirm', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Hapus Produk?',
                    text: 'Data yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then(function(result) {
                    if (result.isConfirmed) form.submit();
                });
            });

            // ── Format Rupiah saat mengetik ───────────────────────────
            $('#inputPrice').on('keyup', function() {
                $(this).val(formatRupiah($(this).val()));
            });

            // ── Strip titik sebelum form disubmit ─────────────────────
            // Supaya Laravel terima angka bersih, bukan "50.000"
            $('#productForm').on('submit', function() {
                var raw = $('#inputPrice').val().replace(/\./g, '');
                $('#inputPrice').val(raw);
            });

        });

        // ── Fetch Kategori berdasarkan Spesies (AJAX) ─────────────────
        // preselectId: ID kategori yang ingin dipilih otomatis (mode edit)
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

                    // Preselect saat mode edit
                    if (preselectId) $cat.val(preselectId);
                },
                error: function() {
                    Swal.fire('Gagal', 'Gagal memuat kategori.', 'error');
                    $cat.prop('disabled', false).html('<option value="">-- Error --</option>');
                }
            });
        }

        // ── Preview foto sebelum upload ───────────────────────────────
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // ── Format Rupiah ─────────────────────────────────────────────
        // Contoh: "50000" → "50.000"
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

    <style>
        #importExportBtns {
            gap: .5rem;
        }

        @media (max-width: 575.98px) {
            #importExportBtns {
                width: 100%;
            }

            #importExportBtns .btn {
                flex: 1 1 100%;
                width: 100%;
                text-align: center;
            }
        }
    </style>
@endpush

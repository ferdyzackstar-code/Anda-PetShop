{{-- resources/views/dashboard/categories/index.blade.php --}}
@extends('dashboard.layouts.admin')

@section('title', 'Manajemen Kategori — Anda Petshop')

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
                <i class="fas fa-tags mr-2"></i> Manajemen Kategori
            </h5>
        </div>
    </div>

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
         FORM TAMBAH / EDIT — TAB
         Tab 1: Spesies (parent)
         Tab 2: Kategori (child)
    ================================ --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header py-0 border-bottom-0">
            <ul class="nav nav-tabs card-header-tabs" id="categoryTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold" id="tab-species" data-toggle="tab" href="#panel-species"
                        role="tab">
                        <i class="fas fa-folder-open mr-1"></i> Spesies
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="tab-category" data-toggle="tab" href="#panel-category"
                        role="tab">
                        <i class="fas fa-tag mr-1"></i> Kategori
                    </a>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="categoryTabContent">

            {{-- ── Tab Spesies ──────────────────────────────────── --}}
            <div class="tab-pane fade show active" id="panel-species" role="tabpanel">
                <div class="card-body">
                    <h6 class="font-weight-bold text-primary mb-3" id="speciesTitleLabel">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Spesies Baru
                    </h6>
                    <form id="speciesForm" action="{{ route('dashboard.categories.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="speciesMethod" value="POST">
                        {{-- Flag: ini adalah spesies (parent_id = null) --}}
                        <input type="hidden" name="is_species" value="1">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-700 small">Nama Spesies <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="speciesName"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Misal: Anjing, Kucing, Ikan">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-700 small">Status <span
                                            class="text-danger">*</span></label>
                                    <select name="status" id="speciesStatus"
                                        class="form-control @error('status') is-invalid @enderror">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-700 small">Deskripsi</label>
                                    <input type="text" name="description" id="speciesDescription" class="form-control"
                                        placeholder="Opsional">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex mt-1">
                            <button type="submit" class="btn btn-primary btn-sm mr-2" id="speciesSubmitBtn">
                                <i class="fas fa-plus mr-1"></i> Tambah
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" id="speciesResetBtn">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── Tab Kategori ─────────────────────────────────── --}}
            <div class="tab-pane fade" id="panel-category" role="tabpanel">
                <div class="card-body">
                    <h6 class="font-weight-bold text-primary mb-3" id="categoryTitleLabel">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Kategori Baru
                    </h6>
                    <form id="categoryForm" action="{{ route('dashboard.categories.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="categoryMethod" value="POST">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-700 small">Nama Kategori <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="categoryName"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Misal: Makanan, Aksesoris">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-700 small">Spesies <span
                                            class="text-danger">*</span></label>
                                    <select name="parent_id" id="categoryParent"
                                        class="form-control @error('parent_id') is-invalid @enderror">
                                        <option value="">— Pilih Spesies —</option>
                                        @foreach ($parentCategories as $parent)
                                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-700 small">Status <span
                                            class="text-danger">*</span></label>
                                    <select name="status" id="categoryStatus"
                                        class="form-control @error('status') is-invalid @enderror">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-700 small">Deskripsi</label>
                                    <input type="text" name="description" id="categoryDescription"
                                        class="form-control" placeholder="Opsional">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex mt-1">
                            <button type="submit" class="btn btn-primary btn-sm mr-2" id="categorySubmitBtn">
                                <i class="fas fa-plus mr-1"></i> Tambah
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" id="categoryResetBtn">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>{{-- end tab-content --}}
    </div>

    {{-- ================================
         TABEL DATA
    ================================ --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" style="min-width: 700px;" id="table-categories">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th width="1%">No</th>
                            <th>Nama</th>
                            <th>Tipe</th>
                            <th class="text-center">Produk</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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
            $('#table-categories').DataTable({
                pageLength: 25,
                processing: true,
                serverSide: true,
                responsive: false,
                ajax: "{{ route('dashboard.categories.index') }}",
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
                        data: 'name_display',
                        name: 'name',
                        className: 'align-middle'
                    },
                    {
                        data: 'type_badge',
                        name: 'type_badge',
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle'
                    },
                    {
                        data: 'product_qty',
                        name: 'product_qty',
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle'
                    },
                    {
                        data: 'status_badge',
                        name: 'status',
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

            // ── Klik Tombol Edit ──────────────────────────────────────
            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');        

                $.ajax({
                    url: "{{ route('dashboard.categories.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function(category) {
                        var isSpecies = category.parent_id === null;

                        if (isSpecies) {
                            // ── Mode Edit Spesies ──
                            $('#speciesName').val(category.name);
                            $('#speciesStatus').val(category.status);
                            $('#speciesDescription').val(category.description);

                            $('#speciesTitleLabel')
                                .html(
                                    '<i class="fas fa-edit mr-1"></i> Edit Spesies: <strong>' +
                                    category.name + '</strong>')
                                .removeClass('text-primary').addClass('text-warning');

                            $('#speciesSubmitBtn')
                                .html('<i class="fas fa-save mr-1"></i> Update')
                                .removeClass('btn-primary').addClass('btn-warning');

                            $('#speciesMethod').val('PUT');
                            $('#speciesForm').attr('action',
                                "{{ route('dashboard.categories.update', ':id') }}"
                                .replace(':id', id));

                            // Pindah ke tab Spesies
                            $('#tab-species').tab('show');
                            $('html, body').animate({
                                scrollTop: $('#speciesForm').offset().top - 80
                            }, 300);

                        } else {
                            // ── Mode Edit Kategori ──
                            $('#categoryName').val(category.name);
                            $('#categoryParent').val(category.parent_id);
                            $('#categoryStatus').val(category.status);
                            $('#categoryDescription').val(category.description);

                            $('#categoryTitleLabel')
                                .html(
                                    '<i class="fas fa-edit mr-1"></i> Edit Kategori: <strong>' +
                                    category.name + '</strong>')
                                .removeClass('text-primary').addClass('text-warning');

                            $('#categorySubmitBtn')
                                .html('<i class="fas fa-save mr-1"></i> Update')
                                .removeClass('btn-primary').addClass('btn-warning');

                            $('#categoryMethod').val('PUT');
                            $('#categoryForm').attr('action',
                                "{{ route('dashboard.categories.update', ':id') }}"
                                .replace(':id', id));

                            // Pindah ke tab Kategori
                            $('#tab-category').tab('show');
                            $('html, body').animate({
                                scrollTop: $('#categoryForm').offset().top - 80
                            }, 300);
                        }
                    },
                    error: function() {
                        Swal.fire('Gagal', 'Data kategori tidak ditemukan.', 'error');
                    }
                });
            });

            // ── Reset Spesies ──────────────────────────────────────────
            $('#speciesResetBtn').on('click', function() {
                $('#speciesName, #speciesDescription').val('');
                $('#speciesStatus').val('active');
                $('#speciesTitleLabel')
                    .html('<i class="fas fa-plus-circle mr-1"></i> Tambah Spesies Baru')
                    .removeClass('text-warning').addClass('text-primary');
                $('#speciesSubmitBtn')
                    .html('<i class="fas fa-plus mr-1"></i> Tambah')
                    .removeClass('btn-warning').addClass('btn-primary');
                $('#speciesMethod').val('POST');
                $('#speciesForm').attr('action', "{{ route('dashboard.categories.store') }}");
            });

            // ── Reset Kategori ─────────────────────────────────────────
            $('#categoryResetBtn').on('click', function() {
                $('#categoryName, #categoryDescription').val('');
                $('#categoryParent').val('');
                $('#categoryStatus').val('active');
                $('#categoryTitleLabel')
                    .html('<i class="fas fa-plus-circle mr-1"></i> Tambah Kategori Baru')
                    .removeClass('text-warning').addClass('text-primary');
                $('#categorySubmitBtn')
                    .html('<i class="fas fa-plus mr-1"></i> Tambah')
                    .removeClass('btn-warning').addClass('btn-primary');
                $('#categoryMethod').val('POST');
                $('#categoryForm').attr('action', "{{ route('dashboard.categories.store') }}");
            });

            // ── SweetAlert konfirmasi sebelum Delete ──────────────────
            $(document).on('click', '.show_confirm', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');

                Swal.fire({
                    title: 'Hapus Data?',
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

        });
    </script>
@endpush

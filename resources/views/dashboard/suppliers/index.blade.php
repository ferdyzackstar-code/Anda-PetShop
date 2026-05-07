{{-- resources/views/dashboard/suppliers/index.blade.php --}}
@extends('dashboard.layouts.admin')

@section('title', 'Manajemen Supplier — Anda Petshop')

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
                <i class="fas fa-truck mr-2"></i> Manajemen Supplier
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
                @foreach (session()->get('import_failures') as $failure)
                    <li>
                        Baris ke-{{ $failure->row() }}:
                        @foreach ($failure->errors() as $error)
                            {{ $error }}
                        @endforeach
                    </li>
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
                <i class="fas fa-plus-circle mr-1"></i> Tambah Supplier Baru
            </h6>
        </div>
        <div class="card-body">
            <form id="supplierForm" action="{{ route('dashboard.suppliers.store') }}" method="POST">
                @csrf
                {{-- Spoofing method untuk PUT saat edit --}}
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="row">
                    {{-- Kolom Kiri --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">Nama Supplier <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" id="inputName"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Masukkan nama supplier" value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">Email</label>
                            <input type="email" name="email" id="inputEmail"
                                class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan email"
                                value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">Kota</label>
                            <input type="text" name="city" id="inputCity" class="form-control"
                                placeholder="Masukkan kota" value="{{ old('city') }}">
                        </div>
                    </div>

                    {{-- Kolom Kanan --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">No Telepon</label>
                            <input type="text" name="phone" id="inputPhone" class="form-control"
                                placeholder="Masukkan no telepon" value="{{ old('phone') }}">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">Alamat</label>
                            <textarea name="address" id="inputAddress" class="form-control" rows="1" placeholder="Alamat supplier (opsional)">{{ old('address') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">Status <span
                                    class="text-danger">*</span></label>
                            <select name="status" id="inputStatus"
                                class="form-control @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                <a href="{{ route('dashboard.suppliers.downloadImportTemplate') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-file-download mr-1"></i> Template Import
                </a>
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalImport">
                    <i class="fas fa-upload mr-1"></i> Import
                </button>
                <a href="{{ route('dashboard.suppliers.export') }}" class="btn btn-primary btn-sm">
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
                <table class="table table-bordered table-hover" style="min-width: 700px;" id="table-suppliers">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th width="1%">No</th>
                            <th>Nama Supplier</th>
                            <th>Email</th>
                            <th>Kota</th>
                            <th>Telepon</th>
                            <th>Status</th>
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
                        <i class="fas fa-upload mr-1"></i> Import Data Supplier
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="{{ route('dashboard.suppliers.import') }}" method="POST" enctype="multipart/form-data">
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

            var table = $('#table-suppliers').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                ajax: "{{ route('dashboard.suppliers.index') }}",
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
                        data: 'name',
                        name: 'name',
                        className: 'align-middle'
                    },
                    {
                        data: 'email',
                        name: 'email',
                        className: 'align-middle'
                    },
                    {
                        data: 'city',
                        name: 'city',
                        className: 'align-middle'
                    },
                    {
                        data: 'phone',
                        name: 'phone',
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
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle'
                    }
                ]
            });

            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('dashboard.suppliers.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function(supplier) {
                        $('#inputName').val(supplier.name);
                        $('#inputEmail').val(supplier.email);
                        $('#inputCity').val(supplier.city);
                        $('#inputPhone').val(supplier.phone);
                        $('#inputAddress').val(supplier.address);
                        $('#inputStatus').val(supplier.status);

                        $('#cardTitle')
                            .html('<i class="fas fa-edit mr-1"></i> Edit Supplier: <strong>' +
                                supplier.name + '</strong>')
                            .removeClass('text-primary').addClass('text-warning');

                        $('#submitBtn')
                            .html('<i class="fas fa-save mr-1"></i> Update')
                            .removeClass('btn-primary').addClass('btn-warning');

                        $('#formMethod').val('PUT');
                        $('#supplierForm').attr(
                            'action',
                            "{{ route('dashboard.suppliers.update', ':id') }}".replace(
                                ':id', id)
                        );

                        $('html, body').animate({
                            scrollTop: $('#supplierForm').offset().top - 80
                        }, 300);
                    },
                    error: function() {
                        Swal.fire('Gagal', 'Data supplier tidak ditemukan.', 'error');
                    }
                }); 
            });

            $('#resetBtn').on('click', function() {
                $('#inputName, #inputEmail, #inputCity, #inputPhone, #inputAddress').val('');
                $('#inputStatus').val('active');

                $('#cardTitle')
                    .html('<i class="fas fa-plus-circle mr-1"></i> Tambah Supplier Baru')
                    .removeClass('text-warning').addClass('text-primary');

                $('#submitBtn')
                    .html('<i class="fas fa-plus mr-1"></i> Tambah')
                    .removeClass('btn-warning').addClass('btn-primary');

                $('#formMethod').val('POST');
                $('#supplierForm').attr('action', "{{ route('dashboard.suppliers.store') }}");
            });

            $(document).on('click', '.show_confirm', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Hapus Supplier?',
                    text: 'Data yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

        });
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

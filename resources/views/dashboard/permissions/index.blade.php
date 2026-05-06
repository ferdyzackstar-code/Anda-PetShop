@extends('dashboard.layouts.admin')

@section('title', 'Manajemen Hak Akses — Anda Petshop')

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
                <i class="fas fa-key mr-2"></i> Manajemen Hak Akses
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
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- ================================
         FORM TAMBAH / EDIT
    ================================ --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3" id="formCardHeader">
            <h6 class="m-0 font-weight-bold text-primary" id="cardTitle">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Hak Akses Baru
            </h6>
        </div>
        <div class="card-body">
            <form id="permissionForm" action="{{ route('dashboard.permissions.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="form-row align-items-end">
                    <div class="col-md-8">
                        <div class="form-group mb-0">
                            <label for="permissionName" class="font-weight-bold text-gray-700 small">
                                Nama Hak Akses
                            </label>
                            <input type="text" name="name" id="permissionName"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Contoh: product.create atau category.delete" value="{{ old('name') }}">
                        </div>
                    </div>
                    <div class="col-md-4 mt-2 mt-md-0">
                        <button type="submit" class="btn btn-primary btn-sm" id="submitBtn">
                            <i class="fas fa-plus mr-1"></i> Tambah
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" id="resetBtn">
                            <i class="fas fa-undo mr-1"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ================================
         TABEL DATA
    ================================ --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" style="min-width: 500px;" id="table-permissions">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th width="1%" class="text-center">No</th>
                            <th>Nama Hak Akses</th>
                            <th width="15%" class="text-center">Aksi</th>
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

            $('#table-permissions').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                ajax: "{{ route('dashboard.permissions.index') }}",
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
                        className: 'text-center'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            });

            // ── Klik Tombol Edit ──────────────────────────────────────
            $(document).on('click', '.editPermission', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');

                $('#permissionName').val(name).focus();

                // Ganti judul & warna card header ke warning
                $('#cardTitle').html('<i class="fas fa-edit mr-1"></i> Edit Hak Akses: <strong>' + name +
                    '</strong>');
                $('#cardTitle').removeClass('text-primary').addClass('text-warning');

                $('#submitBtn')
                    .html('<i class="fas fa-save mr-1"></i> Update')
                    .removeClass('btn-primary')
                    .addClass('btn-warning');

                var updateUrl = "{{ route('dashboard.permissions.update', ':id') }}".replace(':id', id);
                $('#permissionForm').attr('action', updateUrl);
                $('#formMethod').val('PUT');

                $('html, body').animate({
                    scrollTop: $('#permissionForm').offset().top - 80
                }, 300);
            });

            // ── Tombol Reset ──────────────────────────────────────────
            $('#resetBtn').on('click', function() {
                $('#permissionName').val('').removeClass('is-invalid');

                // Kembalikan judul & warna ke primary
                $('#cardTitle')
                    .html('<i class="fas fa-plus-circle mr-1"></i> Tambah Hak Akses Baru')
                    .removeClass('text-warning')
                    .addClass('text-primary');

                $('#submitBtn')
                    .html('<i class="fas fa-plus mr-1"></i> Tambah')
                    .removeClass('btn-warning')
                    .addClass('btn-primary');

                $('#formMethod').val('POST');
                $('#permissionForm').attr('action', "{{ route('dashboard.permissions.store') }}");
            });

        });
    </script>
@endpush

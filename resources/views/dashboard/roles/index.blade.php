@extends('dashboard.layouts.admin')

@section('title', 'Manajemen Peran — Anda Petshop')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')

    {{-- ================================
         HEADER HALAMAN
    ================================ --}}
    <div class="card w-100 border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-4 bg-primary rounded d-flex flex-wrap align-items-center justify-content-between">
            <h5 class="mb-0 text-white font-weight-bold">
                <i class="fas fa-user-shield mr-2"></i> Manajemen Peran
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
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary" id="cardTitle">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Peran Baru
            </h6>
        </div>
        <div class="card-body">
            <form id="roleForm" action="{{ route('dashboard.roles.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                {{-- Nama Peran --}}
                <div class="form-group">
                    <label for="roleName" class="font-weight-bold text-gray-700 small">
                        Nama Peran
                    </label>
                    <input type="text" name="name" id="roleName"
                        class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Manager atau Kasir"
                        value="{{ old('name') }}">
                </div>

                {{-- Assign Permissions --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="font-weight-bold text-gray-700 small mb-0">
                        <i class="fas fa-key mr-1"></i> Hak Akses
                    </label>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="checkAll">
                        <label class="custom-control-label small font-weight-bold" for="checkAll" style="cursor:pointer;">
                            Pilih Semua
                        </label>
                    </div>
                </div>
                <hr class="mt-1 mb-3">

                <div class="row mb-3">
                    @foreach ($groupedPermissions as $group => $permissions)
                        <div class="col-md-2 mb-3">
                            <h6 class="font-weight-bold text-uppercase border-bottom pb-1 mb-2 text-primary"
                                style="font-size: 0.78rem;">
                                <i class="fas fa-folder-open mr-1"></i> {{ $group }}
                            </h6>
                            @foreach ($permissions as $value)
                                <div class="custom-control custom-checkbox mb-1">
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

                {{-- Tombol Aksi --}}
                <div class="d-flex">
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
         TABEL DATA
    ================================ --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" style="min-width: 600px;" id="table-roles">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th width="1%" class="text-center">No</th>
                            <th width="15%">Nama Peran</th>
                            <th>Hak Akses</th>
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

            // ── DataTable (scroll horizontal) ─────────────────────────
            $('#table-roles').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                ajax: {
                    url: "{{ route('dashboard.roles.index') }}",
                    type: 'GET'
                },
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
                        data: 'permission',
                        name: 'permission',
                        orderable: false,
                        searchable: false
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
            $(document).on('click', '.editRole', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var permissions = $(this).data('permissions');

                // Isi nama
                $('#roleName').val(name).focus();

                // Ganti judul & warna card header ke warning
                $('#cardTitle')
                    .html('<i class="fas fa-edit mr-1"></i> Edit Peran: <strong>' + name + '</strong>')
                    .removeClass('text-primary')
                    .addClass('text-warning');

                // Ganti tombol submit ke warning
                $('#submitBtn')
                    .html('<i class="fas fa-save mr-1"></i> Update')
                    .removeClass('btn-primary')
                    .addClass('btn-warning');

                // Update action form
                $('#formMethod').val('PUT');
                $('#roleForm').attr('action',
                    "{{ route('dashboard.roles.update', ':id') }}".replace(':id', id)
                );

                // Pre-fill checkbox
                $('.perm-check').prop('checked', false);
                if (permissions && permissions.length) {
                    permissions.forEach(function(pid) {
                        $('#perm_' + pid).prop('checked', true);
                    });
                }

                // Sinkron "Pilih Semua"
                $('#checkAll').prop('checked',
                    $('.perm-check').length === $('.perm-check:checked').length
                );

                // Scroll ke form
                $('html, body').animate({
                    scrollTop: $('#roleForm').offset().top - 80
                }, 300);
            });

            // ── Tombol Reset ──────────────────────────────────────────
            $('#resetBtn').on('click', function() {
                $('#roleName').val('').removeClass('is-invalid');

                $('#cardTitle')
                    .html('<i class="fas fa-plus-circle mr-1"></i> Tambah Peran Baru')
                    .removeClass('text-warning')
                    .addClass('text-primary');

                $('#submitBtn')
                    .html('<i class="fas fa-plus mr-1"></i> Tambah')
                    .removeClass('btn-warning')
                    .addClass('btn-primary');

                $('#formMethod').val('POST');
                $('#roleForm').attr('action', "{{ route('dashboard.roles.store') }}");

                $('.perm-check').prop('checked', false);
                $('#checkAll').prop('checked', false);
            });

            // ── Checkbox Pilih Semua ──────────────────────────────────
            $('#checkAll').on('change', function() {
                $('.perm-check').prop('checked', this.checked);
            });

            $(document).on('change', '.perm-check', function() {
                $('#checkAll').prop('checked',
                    $('.perm-check').length === $('.perm-check:checked').length
                );
            });

        });
    </script>
@endpush

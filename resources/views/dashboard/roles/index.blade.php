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
            <button type="button" class="btn btn-light btn-sm mt-1 mt-sm-0" id="btnTambahRole">
                <i class="fas fa-plus mr-1"></i> Buat Peran Baru
            </button>
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

    {{-- ================================
         MODAL TAMBAH / EDIT (UNIFIED)
    ================================ --}}
    <div class="modal fade" id="modalRole" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content shadow">

                <div class="modal-header bg-primary" id="modalRoleHeader">
                    <h5 class="modal-title font-weight-bold text-white" id="modalRoleTitle">
                        <i class="fas fa-plus-circle mr-1"></i> Buat Peran Baru
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <form method="POST" id="formRole" action="{{ route('dashboard.roles.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formRoleMethod" value="POST">

                    <div class="modal-body">

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">
                                <i class="fas fa-user-tag mr-1"></i> Nama Peran
                            </label>
                            <input type="text" name="name" id="inputRoleName" class="form-control"
                                placeholder="Contoh: Manager atau Kasir">
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="font-weight-bold text-gray-700 small mb-0">
                                <i class="fas fa-key mr-1"></i> Hak Akses
                            </label>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkAll">
                                <label class="custom-control-label small font-weight-bold" for="checkAll"
                                    style="cursor:pointer;">
                                    Pilih Semua
                                </label>
                            </div>
                        </div>
                        <hr class="mt-1 mb-3">

                        <div class="row">
                            @foreach ($groupedPermissions as $group => $permissions)
                                <div class="col-md-4 mb-3">
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

                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btnSubmitRole">
                            <i class="fas fa-save mr-1"></i> Simpan
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

            // ── DataTable (scroll horizontal, tanpa responsive collapse) ──
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

            // ── Helper: Reset ke Mode Tambah ──────────────────────────
            function resetModalTambah() {
                $('#modalRoleTitle').html('<i class="fas fa-plus-circle mr-1"></i> Buat Peran Baru');
                $('#modalRoleHeader').removeClass('bg-warning').addClass('bg-primary');
                $('#modalRoleHeader .close').addClass('text-white');
                $('#btnSubmitRole').removeClass('btn-warning').addClass('btn-primary');
                $('#inputRoleName').val('');
                $('#formRoleMethod').val('POST');
                $('#formRole').attr('action', "{{ route('dashboard.roles.store') }}");
                $('.perm-check').prop('checked', false);
                $('#checkAll').prop('checked', false);
            }

            // ── Buka Modal Tambah ─────────────────────────────────────
            $('#btnTambahRole').on('click', function() {
                resetModalTambah();
                $('#modalRole').modal('show');
            });

            // ── Buka Modal Edit ───────────────────────────────────────
            $(document).on('click', '.editRole', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var permissions = $(this).data('permissions');

                $('#modalRoleTitle').html('<i class="fas fa-edit mr-1"></i> Edit Peran: <strong>' + name +
                    '</strong>');
                $('#modalRoleHeader').removeClass('bg-primary').addClass('bg-warning');
                $('#modalRoleHeader .close').removeClass('text-white');
                $('#btnSubmitRole').removeClass('btn-primary').addClass('btn-warning');

                $('#inputRoleName').val(name);
                $('#formRoleMethod').val('PUT');
                $('#formRole').attr('action',
                    "{{ route('dashboard.roles.update', ':id') }}".replace(':id', id)
                );

                $('.perm-check').prop('checked', false);
                if (permissions && permissions.length) {
                    permissions.forEach(function(pid) {
                        $('#perm_' + pid).prop('checked', true);
                    });
                }

                $('#checkAll').prop('checked',
                    $('.perm-check').length === $('.perm-check:checked').length
                );

                $('#modalRole').modal('show');
            });

            // ── Reset saat modal ditutup ──────────────────────────────
            $('#modalRole').on('hidden.bs.modal', function() {
                resetModalTambah();
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

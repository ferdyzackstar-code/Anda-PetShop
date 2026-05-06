@extends('dashboard.layouts.admin')

@section('title', 'Manajemen Pengguna — Anda Petshop')

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
                <i class="fas fa-users mr-2"></i> Manajemen Pengguna
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
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
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
                <i class="fas fa-plus-circle mr-1"></i> Tambah Pengguna Baru
            </h6>
        </div>
        <div class="card-body">
            <form id="userForm" action="{{ route('dashboard.users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="row">

                    {{-- Kolom Kiri: Data Akun --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">Nama Lengkap</label>
                            <input type="text" name="name" id="inputName"
                                class="form-control @error('name') is-invalid @enderror" placeholder="Masukkan nama lengkap"
                                value="{{ old('name') }}">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">Email</label>
                            <input type="email" name="email" id="inputEmail"
                                class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan email"
                                value="{{ old('email') }}">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small" id="labelPassword">
                                Password
                            </label>
                            <input type="password" name="password" id="inputPassword"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Masukkan password">
                            <small class="text-muted d-none" id="hintPassword">
                                Kosongkan jika tidak ingin mengubah password.
                            </small>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">Konfirmasi Password</label>
                            <input type="password" name="confirm-password" id="inputConfirmPassword" class="form-control"
                                placeholder="Ulangi password">
                        </div>
                    </div>

                    {{-- Kolom Kanan: Foto, Bio & Role --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">Foto Profil</label>
                            <div class="d-flex align-items-start">
                                <img id="previewFoto" src="{{ asset('storage/uploads/users/default-user.jpg') }}"
                                    class="img-thumbnail mr-3" style="width:80px; height:80px; object-fit:cover;">
                                <div class="flex-fill">
                                    <input type="file" name="image" id="inputFoto"
                                        class="form-control-file @error('image') is-invalid @enderror"
                                        accept="image/jpeg,image/png,image/jpg"
                                        onchange="previewImage(this, 'previewFoto')">
                                    <small class="text-muted">Format: JPG, PNG. Maks: 2MB.</small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">Bio</label>
                            <textarea name="bio" id="inputBio" class="form-control" rows="3"
                                placeholder="Deskripsi singkat pengguna (opsional)">{{ old('bio') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700 small">Peran</label>
                            <select name="roles" id="inputRoles"
                                class="form-control @error('roles') is-invalid @enderror">
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
                <a href="{{ route('dashboard.users.downloadImportTemplate') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-file-download mr-1"></i> Template Import
                </a>
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalImport">
                    <i class="fas fa-upload mr-1"></i> Import
                </button>
                <a href="{{ route('dashboard.users.export') }}" class="btn btn-primary btn-sm">
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
                <table class="table table-bordered table-hover" style="min-width: 700px;" id="table-users">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th width="1%" class="text-center">No</th>
                            <th width="8%" class="text-center">Foto</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th class="text-center">Peran</th>
                            <th width="15%" class="text-center">Aksi</th>
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
                        <i class="fas fa-upload mr-1"></i> Import Data Pengguna
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="{{ route('dashboard.users.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-gray-700 small">Pilih File</label>
                            <input type="file" name="file" class="form-control-file" required
                                accept=".xlsx,.xls,.csv">
                            <small class="text-muted">Format: XLSX, XLS, CSV.</small>
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

            // ── DataTable (scroll horizontal) ─────────────────────────
            $('#table-users').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                ajax: "{{ route('dashboard.users.index') }}",
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
                        data: 'email',
                        name: 'email',
                        className: 'align-middle'
                    },
                    {
                        data: 'roles',
                        name: 'roles',
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
            $(document).on('click', '.editUser', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var email = $(this).data('email');
                var bio = $(this).data('bio');
                var role = $(this).data('role');
                var image = $(this).data('image');

                // Isi field
                $('#inputName').val(name);
                $('#inputEmail').val(email);
                $('#inputPassword').val('');
                $('#inputConfirmPassword').val('');
                $('#inputBio').val(bio);
                $('#inputRoles').val(role);

                // Preview foto
                if (image) {
                    $('#previewFoto').attr('src', image);
                }

                // Hint password opsional
                $('#hintPassword').removeClass('d-none');

                // Ganti judul & warna ke warning
                $('#cardTitle')
                    .html('<i class="fas fa-edit mr-1"></i> Edit Pengguna: <strong>' + name + '</strong>')
                    .removeClass('text-primary').addClass('text-warning');

                $('#submitBtn')
                    .html('<i class="fas fa-save mr-1"></i> Update')
                    .removeClass('btn-primary').addClass('btn-warning');

                // Update action form
                $('#formMethod').val('PUT');
                $('#userForm').attr('action',
                    "{{ route('dashboard.users.update', ':id') }}".replace(':id', id)
                );

                // Scroll ke form
                $('html, body').animate({
                    scrollTop: $('#userForm').offset().top - 80
                }, 300);
            });

            // ── Tombol Reset ──────────────────────────────────────────
            $('#resetBtn').on('click', function() {
                // Reset semua field
                $('#inputName, #inputEmail, #inputPassword, #inputConfirmPassword, #inputBio').val('');
                $('#inputRoles').val('');
                $('#inputFoto').val('');
                $('#previewFoto').attr('src', "{{ asset('storage/uploads/users/default-user.jpg') }}");
                $('#hintPassword').addClass('d-none');

                // Kembalikan judul & warna ke primary
                $('#cardTitle')
                    .html('<i class="fas fa-plus-circle mr-1"></i> Tambah Pengguna Baru')
                    .removeClass('text-warning').addClass('text-primary');

                $('#submitBtn')
                    .html('<i class="fas fa-plus mr-1"></i> Tambah')
                    .removeClass('btn-warning').addClass('btn-primary');

                $('#formMethod').val('POST');
                $('#userForm').attr('action', "{{ route('dashboard.users.store') }}");
            });

        });

        // ── Preview Foto ──────────────────────────────────────────────
        function previewImage(input, previewId) {
            var preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    {{-- Responsive: tombol import/export full width di mobile --}}
    <style>
        #importExportBtns {
            gap: 0.5rem;
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

@extends('dashboard.layouts.admin')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="text-dark">Management Permissions</h4>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card mb-4 border-left-primary shadow">
        <div class="card-header bg-white">
            <h6 class="m-0 font-weight-bold text-primary" id="cardTitle">Tambah Permission Baru</h6>
        </div>
        <div class="card-body">
            <form id="permissionForm" action="{{ route('dashboard.permissions.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="row align-items-end">
                    <div class="col-md-8">
                        <div class="form-group mb-0">
                            <label>Nama Permission</label>
                            <input type="text" name="name" id="permissionName" class="form-control"
                                placeholder="Contoh: product.create atau category.delete" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="fa fa-plus"></i> Tambah
                        </button>
                        <button type="reset" class="btn btn-secondary" id="resetBtn">
                            <i class="fa fa-sync"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered w-100" id="table-permissions">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th width="5%">No</th>
                            <th>Nama Permission</th>
                            <th width="20%" class="text-center">Aksi</th>
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
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // 1. Inisialisasi DataTable (Fitur Search otomatis ada di sini)
            var table = $('#table-permissions').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('dashboard.permissions.index') }}",
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

            // 2. Logika Klik Edit
            $(document).on('click', '.editPermission', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');

                // Isi kotak input & ubah tampilan UI
                $('#permissionName').val(name).focus();
                $('#cardTitle').text('Edit Permission: ' + name);
                $('#submitBtn').html('<i class="fa fa-save"></i> Update').removeClass('btn-success')
                    .addClass('btn-warning');

                // Ubah form agar mengarah ke route update
                let updateUrl = "{{ route('dashboard.permissions.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', id);

                $('#permissionForm').attr('action', updateUrl);
                $('#formMethod').val('PUT');
            });

            // 3. Tombol Reset
            $('#resetBtn').click(function() {
                $('#cardTitle').text('Tambah Permission Baru');
                $('#submitBtn').html('<i class="fa fa-plus"></i> Tambah').removeClass('btn-warning')
                    .addClass('btn-success');
                $('#formMethod').val('POST');
                $('#permissionForm').attr('action', "{{ route('dashboard.permissions.store') }}");
            });

            // 4. Konfirmasi Hapus SweetAlert
            $(document).on('click', '.show_confirm', function(e) {
                e.preventDefault();
                let form = $(this).closest("form");

                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Menghapus permission dapat mempengaruhi akses user!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush

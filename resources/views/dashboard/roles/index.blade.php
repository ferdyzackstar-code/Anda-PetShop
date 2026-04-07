@extends('dashboard.layouts.admin')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Role Management</h2>
            </div>
            @can('role.create')
                <div class="pull-right">
                    <button type="button" class="btn btn-success btn-sm mb-2" data-toggle="modal" data-target="#modalCreateRole">
                        <i class="fa fa-plus"></i> Create New Role
                    </button>
                </div>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive mt-2">
                <table class="table table-hover table-bordered" id="data-roles">
                    <thead>
                        <tr class="bg-primary">
                            <th width='1px' class="text-center text-white">No</th>
                            <th class="text-center text-white">Name</th>
                            <th class="text-center text-white">Permission</th>
                            <th width="250px" class="text-center text-white">Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('dashboard.roles.modals.create', [
        'permission' => $permission,
        'rolePermissions' => [],
    ])

    @foreach ($roles as $role)
        @include('dashboard.roles.modals.show', [
            'role' => $role,
            'groupedPermissions' => $groupedPermissions,
        ])

        @include('dashboard.roles.modals.edit', [
            'role' => $role,
            'permission' => $permission,
            'groupedPermissions' => $groupedPermissions, 
            'rolePermissions' => $role->permissions->pluck('id')->toArray(),
        ])
    @endforeach
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

    <script type="text/javascript">
        // Gunakan jQuery bawaan Admin Anda Petshop, jangan panggil jquery-3.6.0 lagi jika sudah ada di layout
        $(document).ready(function() {
            // 1. Inisialisasi DataTable
            var table = $('#data-roles').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('dashboard.roles.index') }}",
                    type: "GET",
                    error: function(xhr, error, thrown) {
                        alert('DataTables Error: Cek console F12 atau pastikan Route benar.');
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'permission',
                        name: 'permission'
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

            // 2. Select All Checkbox Logic
            $(document).on('click', '#checkAllCreate', function() {
                $('#modalCreateRole .perm-check').prop('checked', this.checked);
            });

            $(document).on('click', '[id^="checkAllEdit"]', function() {
                $(this).closest('.modal-content').find('.perm-check').prop('checked', this.checked);
            });

            // 3. Konfirmasi Hapus
            $(document).on('click', '.show_confirm', function(event) {
                event.preventDefault();
                var form = $(this).closest("form");
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Data ini akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush

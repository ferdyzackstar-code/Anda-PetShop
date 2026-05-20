@extends('dashboard.layouts.admin')

@section('title', 'Manajemen Peran — Anda Petshop')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')

    <div class="card w-100 border-0 shadow-sm mb-3">
        <div class="card-body py-3 px-4 bg-primary rounded d-flex align-items-center justify-content-between"
            style="flex-wrap: wrap; gap: 1rem;">
            <h5 class="mb-0 text-white font-weight-bold">
                <i class="fas fa-user-shield mr-2"></i> Manajemen Peran
            </h5>
            @can('role.create')
                <a href="{{ route('dashboard.roles.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus mr-1"></i> Tambah Peran
                </a>
            @endcan
        </div>
    </div>

    <div class="card w-100 border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-tachometer-alt text-primary mr-2"></i>
                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted mr-2">Dashboard</a> >
                <span class="font-weight-bold text-primary ml-2">Peran</span>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" style="min-width: 700px;" id="table-roles">
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

            const table = $('#table-roles').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                ajax: "{{ route('dashboard.roles.index') }}",
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
                    },
                ],
            });
        });
    </script>
@endpush

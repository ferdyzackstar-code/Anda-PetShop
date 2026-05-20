@extends('dashboard.layouts.admin')

@section('title', 'Manajemen Supplier — Anda Petshop')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')

    <div class="card w-100 border-0 shadow-sm mb-3">
        <div class="card-body py-3 px-4 bg-primary rounded d-flex align-items-center justify-content-between"
            style="flex-wrap: wrap; gap: 1rem;">
            <h5 class="mb-0 text-white font-weight-bold">
                <i class="fas fa-truck mr-2"></i> Manajemen Supplier
            </h5>
            @can('supplier.create')
                <a href="{{ route('dashboard.suppliers.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus mr-1"></i> Tambah Supplier
                </a>
            @endcan
        </div>
    </div>

    <div class="card w-100 border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-tachometer-alt text-primary mr-2"></i>
                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted mr-2">Dashboard</a> >
                <span class="font-weight-bold text-primary ml-2">Supplier</span>
            </div>
        </div>
    </div>

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

    @error('file')
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-exclamation-triangle mr-1"></i> Validasi File Gagal:</strong>
            <ul class="mb-0 mt-2 pl-3">
                <li><i class="fas fa-times-circle mr-1"></i>{{ $message }}</li>
            </ul>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @enderror

    <div class="card shadow-sm mb-2">
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

        @media (max-width: 575.98px) {
            .card-body[style*="flex-wrap"] {
                flex-direction: column !important;
                align-items: stretch !important;
            }

            .card-body[style*="flex-wrap"] h5 {
                width: 100%;
            }

            .card-body[style*="flex-wrap"] .btn {
                width: 100%;
            }
        }
    </style>
@endpush

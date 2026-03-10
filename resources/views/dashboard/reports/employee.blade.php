@extends('dashboard.layouts.admin')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="text-dark">Laporan Performa Karyawan</h4>
        </div>
    </div>

    <div class="mb-3">
        <a href="{{ route('dashboard.reports.export', ['type' => 'employee']) }}" class="btn btn-danger btn-sm">
            Cetak PDF Karyawan
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="table-employee-report">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th width="5%" class="text-center">No</th>
                            <th>Nama Kasir</th>
                            <th class="text-center">Jml Transaksi</th>
                            <th class="text-right">Total Uang Masuk</th>
                            <th class="text-center">Status</th>
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
    <script type="text/javascript">
        $(document).ready(function() {
            $('#table-employee-report').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('dashboard.reports.employee') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'transactions_count',
                        name: 'transactions_count',
                        className: 'text-center'
                    },
                    {
                        data: 'total_uang',
                        name: 'total_uang',
                        className: 'text-right'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center'
                    },
                ]
            });
        });
    </script>
@endpush

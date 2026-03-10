@extends('dashboard.layouts.admin')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="text-dark">Laporan Penjualan Per Outlet</h4>
        </div>
    </div>

    <div class="mb-3">
        <a href="{{ route('dashboard.reports.export', ['type' => 'outlet']) }}" class="btn btn-danger btn-sm">
            Cetak PDF Outlet
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="table-outlet-report">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th width="5%" class="text-center">No</th>
                            <th>Nama Outlet</th>
                            <th class="text-center">Total Transaksi</th>
                            <th class="text-right">Total Omzet</th>
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
            $('#table-outlet-report').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('dashboard.reports.outlet') }}",
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
                        data: 'total_omzet',
                        name: 'total_omzet',
                        className: 'text-right'
                    },
                ]
            });
        });
    </script>
@endpush

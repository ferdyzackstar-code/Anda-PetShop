@extends('dashboard.layouts.admin')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="text-dark">Data Transaksi Penjualan</h4>
        </div>
    </div>

    <div class="card mb-4 shadow">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label>Tanggal Mulai</label>
                    <input type="date" id="start_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Tanggal Selesai</label>
                    <input type="date" id="end_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Pilih Outlet</label>
                    <select id="outlet_filter" class="form-control">
                        <option value="">Semua Outlet</option>
                        @foreach ($outlets as $o)
                            <option value="{{ $o->id }}">{{ $o->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button id="filter_button" class="btn btn-primary mr-2">Filter</button>
                    <button id="reset_button" class="btn btn-secondary">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="table-transactions">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th width="5%">No</th>
                            <th>Invoice</th>
                            <th>Tanggal</th>
                            <th>Outlet</th>
                            <th>Kasir</th>
                            <th class="text-right">Total Bayar</th>
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
            var table = $('#table-transactions').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('dashboard.reports.index') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.outlet_id = $('#outlet_filter').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center',
                        orderable: false, // TAMBAHKAN INI: Agar tidak bisa disortir
                        searchable: false // TAMBAHKAN INI: Agar tidak dicari
                    },
                    {
                        data: 'invoice_number',
                        name: 'invoice_number'
                    },
                    {
                        data: 'formatted_date',
                        name: 'created_at' // Ini benar, sort berdasarkan tanggal asli di DB
                    },
                    {
                        data: 'outlet_name',
                        name: 'outlet_name' // Sesuaikan dengan key di Controller (addColumn tadi)
                    },
                    {
                        data: 'kasir',
                        name: 'kasir' // Sesuaikan dengan key di Controller
                    },
                    {
                        data: 'total_price',
                        name: 'total_price',
                        className: 'text-right'
                    },
                ]
            });

            $('#filter_button').click(function() {
                table.draw();
            });

            $('#reset_button').click(function() {
                $('#start_date').val('');
                $('#end_date').val('');
                $('#outlet_filter').val('');
                table.draw();
            });
        });
    </script>
@endpush

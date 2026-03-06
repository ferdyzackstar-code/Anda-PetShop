@extends('dashboard.layouts.admin')

@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>

    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Products Management</h2>
            </div>
            <div class="pull-right">
                @can('product-create')
                    <button type="button" class="btn btn-success btn-sm mb-2" data-toggle="modal"
                        data-target="#modalCreateProduct">
                        <i class="fa fa-plus"></i> Create New Product
                    </button>
                @endcan
            </div>
        </div>
    </div>

    @include('dashboard.products.modals.create')

    {{-- <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Cabang</th>
            <th>Detail</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($products as $product)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $product->name }}</td>
                <td><span class="badge badge-primary">{{ $product->category->name ?? 'Tanpa Kategori' }}</span></td>
                <td><span class="badge badge-secondary">{{ $product->branch_name }}</span></td>
                <td>{{ Str::limit($product->detail, 50) }}</td>
                <td>
                    <form action="{{ route('dashboard.products.destroy', $product->id) }}" method="POST">
                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                            data-target="#modalShowProduct{{ $product->id }}">
                            <i class="fa fa-eye"></i> Show
                        </button>

                        @can('product-edit')
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                data-target="#modalEditProduct{{ $product->id }}">
                                <i class="fas fa-info-circle"></i> Edit
                            </button>
                        @endcan

                        @csrf @method('DELETE')
                        @can('product-delete')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin hapus produk ini?')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        @endcan
                    </form>
                </td>
            </tr>
            @include('dashboard.products.modals.show', ['product' => $product])
            @include('dashboard.products.modals.edit', ['product' => $product])
        @endforeach
    </table>

    {!! $products->links() !!} --}}


    <div class="table-responsive text-nowrap">
        <table class="table table-hover table-bordered" id="data-products">
            <thead>
                <tr class="bg-primary">
                    <th width='10px' class="text-center text-white">No</th>
                    <th class="text-center text-white">Name</th>
                    <th class="text-center text-white">Category</th>
                    <th width='50px' class="text-center text-white">Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
            </tbody>
        </table>
    </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>

    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#data-products').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{!! route('dashboard.products.index') !!}",
                columns: [{
                        data: 'nomor',
                        name: 'nomor'
                    },

                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    }
                ],
            });
        });
    </script>
@endsection

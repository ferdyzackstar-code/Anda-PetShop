@extends('dashboard.layouts.admin')

@section('content')
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

    <table class="table table-bordered">
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

    {!! $products->links() !!}
@endsection

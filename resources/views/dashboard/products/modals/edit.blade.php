<div class="modal fade" id="modalEditProduct{{ $product->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product: {{ $product->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('dashboard.products.update', $product->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <div class="form-group">
                        <strong>Name:</strong>
                        <input type="text" name="name" value="{{ $product->name }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <strong>Kategori:</strong>
                        <select name="category_id" class="form-control" required>
                            @foreach ($categories as $cat)
                                <optgroup label="{{ $cat->name }}">
                                    @foreach ($cat->children as $sub)
                                        <option value="{{ $sub->id }}"
                                            {{ $product->category_id == $sub->id ? 'selected' : '' }}>
                                            {{ $sub->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <strong>Cabang:</strong>
                        <select name="branch_name" class="form-control" required>
                            @foreach (['Kemanggisan', 'Cipinang', 'Pengadegan', 'Kalibata', 'Rawa Belong'] as $branch)
                                <option value="{{ $branch }}"
                                    {{ $product->branch_name == $branch ? 'selected' : '' }}>
                                    Anda Petshop {{ $branch }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <strong>Detail:</strong>
                        <textarea name="detail" class="form-control" style="height:100px" required>{{ $product->detail }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

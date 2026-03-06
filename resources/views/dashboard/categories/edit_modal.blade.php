<div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-primary">
                    <i class="fas fa-edit"></i> Edit {{ $item->parent_id ? 'Sub-Kategori' : 'Kategori Utama' }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('dashboard.categories.update', $item->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Nama Kategori</strong></label>
                        <input type="text" name="name" class="form-control" value="{{ $item->name }}" required
                            autofocus>
                    </div>

                    @if ($item->parent_id)
                        {{-- Untuk Sub-Kategori: Tampilkan info Parent-nya tapi di-disable (hanya baca) --}}
                        <div class="form-group">
                            <label>Kategori Utama (Tetap)</label>
                            <input type="text" class="form-control bg-light" value="{{ $item->parent->name }}"
                                readonly>
                            <input type="hidden" name="parent_id" value="{{ $item->parent_id }}">
                        </div>
                    @else
                        {{-- Untuk Kategori Utama: Kunci agar tetap jadi Utama --}}
                        <input type="hidden" name="parent_id" value="">
                        <div class="alert alert-info py-2">
                            <small><i class="fas fa-info-circle"></i> Ini adalah Kategori Utama. Anda hanya dapat
                                mengubah namanya.</small>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 shadow">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

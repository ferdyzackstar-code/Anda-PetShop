<div class="modal fade" id="modalCreateRole" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-left-success shadow">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-success">
                    <i class="fa fa-plus-circle mr-1"></i> Create New Role
                </h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('dashboard.roles.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark"><i class="fa-solid fa-user-shield"></i> Role Name:</label>
                        <input type="text" name="name" placeholder="Contoh: Manager atau Kasir"
                            class="form-control" required autofocus>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="font-weight-bold mb-0 text-dark"><i class="fa-solid fa-key"></i> Assign Permissions:</label>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="checkAllCreate">
                            <label class="custom-control-label text-success font-weight-bold" for="checkAllCreate"
                                style="cursor:pointer">
                                Pilih Semua
                            </label>
                        </div>
                    </div>
                    <hr class="mt-1">

                    <div class="row">
                        @foreach ($groupedPermissions as $group => $permissions)
                            <div class="col-md-4 mt-3">
                                <h6 class="text-success font-weight-bold border-bottom pb-2 text-uppercase"
                                    style="font-size: 0.85rem;">
                                    <i class="fas fa-folder-open mr-1"></i> {{ $group }}
                                </h6>
                                <div class="mt-2">
                                    @foreach ($permissions as $value)
                                        <div class="mb-2">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="permission[]" value="{{ $value->id }}"
                                                    class="custom-control-input perm-check-create"
                                                    id="perm_create_{{ $value->id }}">
                                                <label class="custom-control-label"
                                                    for="perm_create_{{ $value->id }}"
                                                    style="cursor:pointer; font-size: 0.9rem;">
                                                    {{ explode('.', $value->name)[1] ?? $value->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 shadow">Simpan Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('checkAllCreate').addEventListener('click', function() {
        let checkboxes = document.querySelectorAll('.perm-check-create');
        checkboxes.forEach((checkbox) => {
            checkbox.checked = this.checked;
        });
    });
</script>

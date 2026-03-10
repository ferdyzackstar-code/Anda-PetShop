<div class="modal fade" id="modalEditRole{{ $role->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-left-primary">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-primary">Edit Role: {{ $role->name }}</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('dashboard.roles.update', $role->id) }}">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Role Name:</label>
                        <input type="text" name="name" value="{{ $role->name }}" class="form-control" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="font-weight-bold mb-0">Assign Permissions:</label>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="checkAllEdit{{ $role->id }}">
                            <label class="custom-control-label text-primary font-weight-bold"
                                for="checkAllEdit{{ $role->id }}" style="cursor:pointer">
                                Pilih Semua
                            </label>
                        </div>
                    </div>
                    <hr class="mt-1">

                    <div class="row">
                        @foreach ($groupedPermissions as $group => $permissions)
                            <div class="col-12 mt-3">
                                <h6 class="text-primary font-weight-bold">{{ strtoupper($group) }} MANAGEMENT</h6>
                            </div>
                            @foreach ($permissions as $value)
                                <div class="col-md-4 mb-2">
                                    <div class="custom-control custom-checkbox text-capitalize">
                                        @php
                                            $checked = $role->hasPermissionTo($value->name) ? 'checked' : '';
                                        @endphp
                                        <input type="checkbox" name="permission[]" value="{{ $value->id }}"
                                            class="custom-control-input perm-check"
                                            id="perm_edit_{{ $role->id }}_{{ $value->id }}" {{ $checked }}>
                                        <label class="custom-control-label"
                                            for="perm_edit_{{ $role->id }}_{{ $value->id }}">
                                            {{ str_replace('-', ' ', $value->name) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 shadow">Update Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

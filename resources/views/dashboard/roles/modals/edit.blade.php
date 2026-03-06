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
                        <label>Role Name:</label>
                        <input type="text" name="name" value="{{ $role->name }}" class="form-control" required>
                    </div>
                    <label class="font-weight-bold text-dark">Update Permissions:</label>
                    <div class="row">
                        @php
                            $currentRolePerms = $role->permissions->pluck('id')->toArray();
                        @endphp
                        @foreach ($permission as $value)
                            <div class="col-md-4 mb-2">
                                <div class="custom-control custom-checkbox text-capitalize">
                                    <input type="checkbox" name="permission[{{ $value->id }}]"
                                        value="{{ $value->id }}" class="custom-control-input"
                                        id="perm_edit_{{ $role->id }}_{{ $value->id }}"
                                        {{ in_array($value->id, $currentRolePerms) ? 'checked' : '' }}>
                                    <label class="custom-control-label"
                                        for="perm_edit_{{ $role->id }}_{{ $value->id }}">
                                        {{ str_replace('-', ' ', $value->name) }}
                                    </label>
                                </div>
                            </div>
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

<div class="modal fade" id="modalEditUser{{ $user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User: {{ $user->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <form method="POST" action="{{ route('dashboard.users.update', $user->id) }}">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <strong>Name:</strong>
                        <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
                    </div>
                    <div class="form-group mb-2">
                        <strong>Email:</strong>
                        <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
                    </div>
                    <div class="form-group mb-2">
                        <strong>Password:</strong>
                        <input type="password" name="password" class="form-control"
                            placeholder="Kosongkan jika tidak ganti">
                    </div>
                    <div class="form-group mb-2">
                        <strong>Confirm Password:</strong>
                        <input type="password" name="confirm-password" class="form-control"
                            placeholder="Kosongkan jika tidak ganti">
                    </div>
                    <div class="form-group">
                        <strong>Role:</strong>
                        <select name="roles[]" class="form-control" multiple required>
                            @foreach ($roles as $value => $label)
                                <option value="{{ $value }}"
                                    {{ in_array($value, $user->roles->pluck('name')->toArray()) ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

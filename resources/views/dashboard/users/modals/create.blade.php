<div class="modal fade" id="modalCreateUser" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <form method="POST" action="{{ route('dashboard.users.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <strong>Name:</strong>
                        <input type="text" name="name" placeholder="Full Name" class="form-control" required>
                    </div>
                    <div class="form-group mb-2">
                        <strong>Email:</strong>
                        <input type="email" name="email" placeholder="Email Address" class="form-control" required>
                    </div>
                    <div class="form-group mb-2">
                        <strong>Password:</strong>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group mb-2">
                        <strong>Confirm Password:</strong>
                        <input type="password" name="confirm-password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <strong>Role:</strong>
                        <select name="roles[]" class="form-control" multiple required>
                            @foreach ($roles as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted text-italic">*Tahan Ctrl untuk memilih lebih dari satu</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>

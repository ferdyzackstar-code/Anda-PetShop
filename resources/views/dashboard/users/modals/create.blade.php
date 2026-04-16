<div class="modal fade" id="modalCreateUser" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <form method="POST" action="{{ route('dashboard.users.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <strong><i class="fa-solid fa-address-card"></i> Name:</strong>
                        <input type="text" name="name" placeholder="Full Name" class="form-control" required>
                    </div>
                    <div class="form-group mb-2">
                        <strong><i class="fa-solid fa-envelope"></i> Email:</strong>
                        <input type="email" name="email" placeholder="Email Address" class="form-control" required>
                    </div>
                    <div class="form-group mb-2">
                        <strong><i class="fa-solid fa-lock"></i> Password:</strong>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group mb-2">
                        <strong><i class="fa-solid fa-unlock-keyhole"></i> Confirm Password:</strong>
                        <input type="password" name="confirm-password" class="form-control" required>
                    </div>
                    <div class="form-group mb-2">
                        <strong><i class="fa-solid fa-image"></i> Foto Profil:</strong>
                        <input type="file" name="image" class="form-control mb-2" id="imageCreate"
                            onchange="previewImage(this, 'previewCreate')">

                        <div
                            class="d-flex flex-column align-items-center justify-content-center border rounded p-3 bg-light">
                            <img id="previewCreate" src="" class="img-thumbnail shadow-sm mb-2 d-none"
                                style="width: 150px; height: 150px; object-fit: cover; object-position: center;">

                            <small class="text-muted italic">Pratinjau Foto</small>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <strong>
                            <i class="fas fa-pen mr-1"></i> Bio:
                        </strong>
                        <textarea name="bio" class="form-control bg-light border-0 shadow-none @error('bio') is-invalid @enderror"
                            rows="4" placeholder="Masukkan deskripsi singkat user..." style="border-radius: 10px; resize: none;">{{ old('bio') }}</textarea>
                        @error('bio')
                            <small class="text-danger font-weight-bold">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <strong><i class="fa-solid fa-user-shield"></i> Role:</strong>
                        <div class="border rounded p-3 mt-2" style="max-height: 180px; overflow-y: auto;">
                            @foreach ($roles as $value => $label)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="roles[]"
                                        id="createRole{{ \Illuminate\Support\Str::slug($value, '-') }}"
                                        value="{{ $value }}">
                                    <label class="form-check-label"
                                        for="createRole{{ \Illuminate\Support\Str::slug($value, '-') }}">
                                        {{ $label }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted text-italic">*Pilih minimal satu role</small>
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

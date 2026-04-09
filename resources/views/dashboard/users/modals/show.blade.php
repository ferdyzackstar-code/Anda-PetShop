<div class="modal fade" id="modalShowUser{{ $user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-primary pt-4 pb-5 border-0 text-center d-block">
                <h5 class="modal-title text-white font-weight-bold w-100">Informasi Pengguna</h5>
                <button type="button" class="close text-white position-absolute" data-dismiss="modal"
                    style="right: 20px; top: 20px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-0">
                <div class="text-center" style="margin-top: -40px; margin-bottom: 20px;">
                    @php
                        $imagePath = 'storage/uploads/users/' . $user->image;
                        $url =
                            $user->image && file_exists(public_path($imagePath))
                                ? asset($imagePath)
                                : asset('storage/uploads/users/default-user.jpg');
                    @endphp
                    <img src="{{ $url }}" class="rounded-circle border border-white shadow"
                        style="width: 110px; height: 110px; object-fit: cover; border-width: 4px !important;">
                </div>

                <div class="px-4 py-2 text-center">
                    <h4 class="font-weight-bold text-dark mb-1">{{ $user->name }}</h4>
                    <p class="text-muted small mb-4"><i class="fas fa-envelope mr-1"></i> {{ $user->email }}</p>

                    <div class="bg-light p-3 mb-4" style="border-radius: 15px;">
                        <label class="text-muted small font-weight-bold d-block text-uppercase mb-2">Bio</label>
                        <p class="text-dark mb-0">{{ $user->bio ?? 'User belum menuliskan bio apapun.' }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small font-weight-bold d-block text-uppercase mb-2">Peran Akses</label>
                        @foreach ($user->getRoleNames() as $v)
                            <span class="badge badge-pill badge-primary px-3 py-2 shadow-sm"
                                style="font-size: 0.75rem;">
                                <i class="fas fa-user-shield mr-1"></i> {{ $v }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pb-4 justify-content-center">
                <button type="button" class="btn btn-light px-5 font-weight-bold text-muted" data-dismiss="modal"
                    style="border-radius: 10px;">Tutup</button>
            </div>
        </div>
    </div>
</div>

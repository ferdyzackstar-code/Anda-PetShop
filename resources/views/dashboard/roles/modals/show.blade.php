<div class="modal fade" id="modalShowRole{{ $role->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fa fa-shield-alt"></i> Role Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 text-center">
                    <small class="text-muted d-block uppercase">ROLE NAME</small>
                    <h3 class="font-weight-bold text-dark">{{ $role->name }}</h3>
                </div>
                <hr>
                <label class="font-weight-bold"><i class="fa fa-key"></i> Authorized Permissions:</label>
                <div class="mt-2">
                    @if ($role->permissions->count() > 0)
                        @foreach ($role->permissions as $v)
                            <span class="badge badge-pill badge-info py-2 px-3 mb-2">{{ $v->name }}</span>
                        @endforeach
                    @else
                        <p class="text-muted italic">No permissions assigned.</p>
                    @endif
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light border" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

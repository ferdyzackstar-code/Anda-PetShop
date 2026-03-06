<div class="modal fade" id="modalCreateRole" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fa fa-plus"></i> Create New Role</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('dashboard.roles.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Role Name:</label>
                        <input type="text" name="name" placeholder="Contoh: Manager" class="form-control"
                            required>
                    </div>
                    <label class="font-weight-bold">Assign Permissions:</label>
                    <div class="row">
                        @foreach ($permission as $value)
                            <div class="col-md-4 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="permission[{{ $value->id }}]"
                                        value="{{ $value->id }}" class="custom-control-input"
                                        id="perm_create_{{ $value->id }}">
                                    <label class="custom-control-label"
                                        for="perm_create_{{ $value->id }}">{{ $value->name }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success px-4 shadow">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

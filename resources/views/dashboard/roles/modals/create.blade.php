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

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="font-weight-bold mb-0">Assign Permissions:</label>
                        {{-- Checkbox Select All --}}
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="checkAllCreate">
                            <label class="custom-control-label text-primary font-weight-bold" for="checkAllCreate"
                                style="cursor:pointer">
                                Pilih Semua
                            </label>
                        </div>
                    </div>
                    <hr class="mt-1">

                    <div class="row">
                        @foreach ($groupedPermissions as $group => $permissions)
                            <div class="col-12 mt-3">
                                <h6 class="text-primary font-weight-bold text-uppercase border-bottom pb-1">
                                    <i class="fas fa-folder-open mr-1"></i> {{ $group }} Management
                                </h6>
                            </div>
                            @foreach ($permissions as $value)
                                <div class="col-md-4 mb-2">
                                    <div class="custom-control custom-checkbox text-capitalize">
                                        <input type="checkbox" name="permission[{{ $value->id }}]"
                                            value="{{ $value->id }}" class="custom-control-input perm-check"
                                            id="perm_create_{{ $value->id }}">
                                        <label class="custom-control-label" for="perm_create_{{ $value->id }}">
                                            {{ str_replace('-', ' ', $value->name) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
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

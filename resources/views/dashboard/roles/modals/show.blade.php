<div class="modal fade" id="modalShowRole{{ $role->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
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
                <label class="font-weight-bold mb-3"><i class="fa fa-key"></i> Authorized Permissions:</label>

                <div class="row">
                    @foreach ($groupedPermissions as $group => $permissions)
                        @php
                            $hasAnyInGroup = $permissions
                                ->toQuery()
                                ->whereIn('id', $role->permissions->pluck('id'))
                                ->exists();
                        @endphp

                        @if ($hasAnyInGroup)
                            <div class="col-md-4 mt-3">
                                <h6 class="text-primary font-weight-bold text-uppercase border-bottom pb-2">
                                    <i class="fas fa-folder-open mr-1"></i> {{ $group }}
                                </h6>
                                <div class="mt-2">
                                    @foreach ($permissions as $value)
                                        @if ($role->hasPermissionTo($value->name))
                                            <div class="mb-1">
                                                <i class="fa fa-check-circle text-success mr-1"></i>
                                                <span
                                                    class="text-capitalize">{{ explode('.', $value->name)[1] ?? $value->name }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light border" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

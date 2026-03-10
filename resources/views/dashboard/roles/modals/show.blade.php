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
                    @if ($role->permissions->count() > 0)
                        @foreach ($groupedPermissions as $group => $permissions)
                            {{-- Hanya tampilkan grup jika ada minimal satu permission yang dimiliki role ini di grup tersebut --}}
                            @php
                                $hasAnyInGroup = $permissions
                                    ->toQuery()
                                    ->whereIn('id', $role->permissions->pluck('id'))
                                    ->exists();
                            @endphp

                            @if ($hasAnyInGroup)
                                <div class="col-12 mt-2">
                                    <h6 class="text-primary font-weight-bold text-uppercase border-bottom pb-1">
                                        <i class="fas fa-folder-open mr-1"></i> {{ $group }} Management
                                    </h6>
                                </div>
                                @foreach ($permissions as $value)
                                    @if ($role->hasPermissionTo($value->name))
                                        <div class="col-md-4 mb-2">
                                            <div class="custom-control custom-checkbox text-capitalize">
                                                {{-- Atribut checked membuat checkbox nyala, onclick="return false" mencegah perubahan --}}
                                                <input type="checkbox" class="custom-control-input"
                                                    id="show_perm_{{ $role->id }}_{{ $value->id }}" checked
                                                    onclick="return false;">
                                                <label class="custom-control-label text-dark"
                                                    for="show_perm_{{ $role->id }}_{{ $value->id }}"
                                                    style="cursor: default;">
                                                    {{ str_replace('-', ' ', $value->name) }}
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    @else
                        <div class="col-12 text-center py-3">
                            <p class="text-muted italic">No permissions assigned to this role.</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light border" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

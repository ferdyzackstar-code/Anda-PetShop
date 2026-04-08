<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 sticky-top shadow">

    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <div class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-store fa-sm"></i> Outlet:
                <strong>
                    @if (request('outlet_id'))
                        {{ \App\Models\Outlet::find(request('outlet_id'))->name ?? 'Semua Cabang' }}
                    @else
                        Semua Cabang
                    @endif
                </strong>
            </button>

            <div class="dropdown-menu shadow animated--fade-in" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item {{ !request('outlet_id') ? 'active' : '' }}"
                    href="{{ request()->fullUrlWithQuery(['outlet_id' => null]) }}">
                    Semua Cabang
                </a>
                <div class="dropdown-divider"></div>

                @foreach (\App\Models\Outlet::all() as $outlet)
                    <a class="dropdown-item {{ request('outlet_id') == $outlet->id ? 'active' : '' }}"
                        href="{{ request()->fullUrlWithQuery(['outlet_id' => $outlet->id]) }}">
                        {{ $outlet->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <ul class="navbar-nav ml-auto">
        @auth
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>

                    @php
                    $userPhoto = Auth::user()->image && file_exists(public_path('storage/uploads/users/' .
                    Auth::user()->image))
                    ? asset('storage/uploads/users/' . Auth::user()->image)
                    : asset('storage/uploads/users/default-user.jpg');
                    @endphp

                    <img class="img-profile rounded-circle shadow-sm" src="{{ $userPhoto }}"
                        style="width: 2rem; height: 2rem; object-fit: cover; border: 1px solid #e3e6f0;">
                </a>

                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="{{ route('profile.index') }}">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item border-0 bg-transparent text-left w-100">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </li>
        @endauth
    </ul>

</nav>

<script>
    function previewImage(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>

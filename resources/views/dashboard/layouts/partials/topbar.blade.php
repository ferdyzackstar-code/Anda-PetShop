{{-- resources/views/dashboard/layouts/topbar.blade.php --}}

@php
    $appName = \App\Models\SettingApp::get('app_name', 'Anda Petshop');
    $appImage = \App\Models\SettingApp::get('app_image', '');
    $topbarTitle = 'MANAJEMEN SISTEM INFORMASI RETAIL ' . strtoupper($appName);

    $user = Auth::user();
    $userPhoto =
        $user->image && file_exists(public_path('storage/uploads/users/' . $user->image))
            ? asset('storage/uploads/users/' . $user->image)
            : asset('storage/uploads/users/default-user.jpg');

    // app_image di DB sudah menyimpan path relatif, misal: uploads/settings/default-logo.jpg
    // sehingga full path di disk = storage/app/public/{app_image}  →  public_path('storage/' . $appImage)
    $appLogoUrl = $appImage && file_exists(public_path('storage/' . $appImage)) ? asset('storage/' . $appImage) : null;
@endphp

<style>
    /* ══════════════════════════════════════════
   TOPBAR — Floating Card Style
   ══════════════════════════════════════════ */
    .topbar-main {
        position: sticky;
        top: 12px;
        z-index: 1040;
        margin: 12px 16px 0 16px;
        padding: 0;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, .10), 0 1px 4px rgba(0, 0, 0, .06);
        border: 1px solid rgba(232, 234, 240, .8);
        height: 62px;
        display: flex;
        align-items: center;
        transition: margin-left .28s ease, width .28s ease;
    }

    .topbar-inner {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 0 1.25rem;
        gap: .75rem;
    }

    /* ── App Title (desktop) ── */
    .topbar-title {
        flex: 1;
        font-size: .82rem;
        font-weight: 800;
        letter-spacing: .08em;
        color: #3d4466;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        min-width: 0;
    }

    @media (max-width: 380px) {
        .topbar-title {
            font-size: .68rem;
            letter-spacing: .04em;
        }
    }

    /* ── Mobile Logo (center) ── */
    .topbar-logo-mobile {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }

    /* ── Burger ── */
    #sidebarToggleTop {
        display: none;
        background: none;
        border: none;
        font-size: 1.15rem;
        color: #5a5c69;
        padding: 0 .6rem 0 0;
        cursor: pointer;
        flex-shrink: 0;
        line-height: 1;
    }

    @media (max-width: 767.98px) {
        #sidebarToggleTop {
            display: flex;
            align-items: center;
        }

        .topbar-title {
            display: none !important;
        }
    }

    /* ── User button ── */
    .topbar-user-btn {
        display: flex;
        align-items: center;
        gap: .5rem;
        background: none;
        border: none;
        padding: 0 .4rem;
        cursor: pointer;
        border-radius: 10px;
        height: 62px;
        transition: background .15s;
        flex-shrink: 0;
    }

    .topbar-user-btn::after {
        display: none !important;
    }

    .topbar-user-btn:hover {
        background: #f4f6fb;
    }

    .topbar-user-btn:focus {
        outline: none;
        box-shadow: none;
    }

    .topbar-user-name {
        font-size: .82rem;
        font-weight: 600;
        color: #3d4466;
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: none;
    }

    @media (min-width: 576px) {
        .topbar-user-name {
            display: block;
        }
    }

    .topbar-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e3e6f0;
        flex-shrink: 0;
    }

    .topbar-chevron {
        font-size: .58rem;
        color: #9da3b4;
        transition: transform .2s;
        display: none;
    }

    @media (min-width: 576px) {
        .topbar-chevron {
            display: inline-block;
        }
    }

    .topbar-user-btn[aria-expanded="true"] .topbar-chevron {
        transform: rotate(180deg);
    }

    /* ── Dropdown ── */
    .topbar-dd {
        border: none !important;
        border-radius: 14px !important;
        box-shadow: 0 12px 40px rgba(0, 0, 0, .14), 0 2px 8px rgba(0, 0, 0, .07) !important;
        min-width: 252px;
        padding: 0 !important;
        overflow: hidden;
        margin-top: 6px !important;
    }

    .dd-head {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: 1rem;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }

    .dd-head-avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, .45);
        flex-shrink: 0;
    }

    .dd-head-info {
        min-width: 0;
    }

    .dd-head-name {
        font-size: .88rem;
        font-weight: 700;
        color: #fff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dd-head-email {
        font-size: .72rem;
        color: rgba(255, 255, 255, .72);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 1px;
    }

    .dd-head-role {
        display: inline-block;
        margin-top: 4px;
        font-size: .63rem;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, .65);
        background: rgba(255, 255, 255, .16);
        border-radius: 20px;
        padding: 1px 8px;
    }

    .dd-body {
        padding: .35rem 0;
    }

    .dd-item {
        display: flex;
        align-items: center;
        gap: .65rem;
        padding: .58rem 1rem;
        font-size: .83rem;
        color: #3d4466;
        text-decoration: none !important;
        transition: background .12s, color .12s;
        background: none;
        border: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }

    .dd-item:hover {
        background: #f4f6fb;
        color: #4e73df;
    }

    .dd-item i {
        width: 15px;
        text-align: center;
        color: #adb5bd;
        font-size: .8rem;
        transition: color .12s;
    }

    .dd-item:hover i {
        color: #4e73df;
    }

    .dd-sep {
        border: 0;
        border-top: 1px solid #edf0f7;
        margin: .25rem 0;
    }

    .dd-item.dd-logout {
        color: #e74a3b;
    }

    .dd-item.dd-logout i {
        color: #e74a3b;
    }

    .dd-item.dd-logout:hover {
        background: #fff5f5;
        color: #c0392b;
    }

    .dd-item.dd-logout:hover i {
        color: #c0392b;
    }
</style>

<nav class="topbar-main">
    <div class="topbar-inner">

        {{-- Burger: mobile only --}}
        <button id="sidebarToggleTop" aria-label="Toggle Sidebar">
            <i class="fas fa-bars"></i>
        </button>

        {{-- Desktop: App Title --}}
        <span class="topbar-title">{{ $topbarTitle }}</span>

        {{-- Mobile: Logo / App name di tengah --}}
        @if ($appLogoUrl)
            <img src="{{ $appLogoUrl }}" alt="{{ $appName }}" class="d-md-none topbar-logo-mobile"
                style="height:36px; object-fit:contain;">
        @else
            <span class="d-md-none topbar-logo-mobile font-weight-bold text-primary"
                style="font-size:.82rem; letter-spacing:.04em;">
                {{ $appName }}
            </span>
        @endif

        {{-- Spacer agar user btn tetap di kanan saat mobile --}}
        <span class="flex-grow-1 d-md-none"></span>

        {{-- User Dropdown --}}
        <div class="dropdown" style="flex-shrink:0;">
            <button class="topbar-user-btn" id="userDropdown" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <span class="topbar-user-name">{{ $user->name }}</span>
                <img class="topbar-avatar" src="{{ $userPhoto }}" alt="{{ $user->name }}">
                <i class="fas fa-chevron-down topbar-chevron"></i>
            </button>

            <div class="dropdown-menu dropdown-menu-right topbar-dd" aria-labelledby="userDropdown">

                {{-- Header --}}
                <div class="dd-head">
                    <img class="dd-head-avatar" src="{{ $userPhoto }}" alt="{{ $user->name }}">
                    <div class="dd-head-info">
                        <div class="dd-head-name">{{ $user->name }}</div>
                        <div class="dd-head-email">{{ $user->email }}</div>
                        @if ($user->roles && $user->roles->isNotEmpty())
                            @foreach ($user->roles as $role)
                                <span class="dd-head-role">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- Menu --}}
                <div class="dd-body">
                    <a class="dd-item" href="{{ route('profile.index') }}">
                        <i class="fas fa-user-circle"></i> Edit Profil
                    </a>
                    <hr class="dd-sep">
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="dd-item dd-logout">
                            <i class="fas fa-sign-out-alt"></i> Keluar
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>
</nav>

{{-- Global helpers --}}
<script>
    window.previewImage = function(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (input && input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                if (preview) preview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    };
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $ !== 'undefined') {
            $('.custom-file-input').on('change', function() {
                $(this).next('.custom-file-label').addClass('selected').html(
                    $(this).val().split('\\').pop()
                );
            });
        }
    });
</script>

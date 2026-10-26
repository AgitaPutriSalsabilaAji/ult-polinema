@php
    $items = app('adminlte')->menu('sidebar');
    $sidebarTheme = config('adminlte.sidebar_theme', 'dark');
    $sidebarClasses = config('adminlte.classes_sidebar', 'bg-body-secondary shadow');
    $admin = auth('admin')->user();
@endphp
<aside class="app-sidebar {{ $sidebarClasses }}" @if ($sidebarTheme === 'dark') data-bs-theme="dark" @endif>
    <div class="sidebar-brand {{ config('adminlte.classes_brand') }}">
        <a href="{{ url('/') }}" class="brand-link">
            @if (config('adminlte.logo_img'))
                <img src="{{ asset(config('adminlte.logo_img')) }}"
                     alt="{{ config('adminlte.logo_img_alt', 'Logo') }}"
                     class="{{ config('adminlte.logo_img_class', 'brand-image opacity-75 shadow') }}">
            @endif
            <span class="brand-text {{ config('adminlte.classes_brand_text', 'fw-light') }}">
                {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
            </span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav aria-label="{{ __('Main navigation') }}">
            <ul class="nav sidebar-menu flex-column {{ config('adminlte.classes_sidebar_nav') }}"
                data-lte-toggle="treeview"
                data-accordion="false"
                role="menu"
                id="navigation">
                @foreach ($items as $item)
                    @include('adminlte::partials.menu-item', ['item' => $item])
                @endforeach
            </ul>
        </nav>
    </div>

    @if ($admin)
        @php
            $role = $admin->peran === 'admin_ult' ? 'Super Admin (ULT)' : 'Admin Unit';
        @endphp
        <div class="sidebar-account">
            <a href="{{ route('admin.profil') }}" class="sidebar-account__profile">
                @if ($admin->avatar)
                    <img src="{{ asset('storage/' . $admin->avatar) }}"
                         alt=""
                         class="sidebar-account__avatar">
                @else
                    <span class="sidebar-account__avatar" aria-hidden="true">
                        <i class="bi bi-person-fill"></i>
                    </span>
                @endif
                <span class="sidebar-account__details">
                    <span class="sidebar-account__name">{{ $admin->nama_lengkap_gelar }}</span>
                    <span class="sidebar-account__role">{{ $role }}</span>
                </span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="sidebar-account__logout"
                        aria-label="{{ __('Log out') }}"
                        title="{{ __('Log out') }}">
                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    @endif
</aside>

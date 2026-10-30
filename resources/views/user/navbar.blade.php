@php
    $theme = $theme ?? 'purple';
    $theme = in_array($theme, ['light', 'purple'], true) ? $theme : 'purple';
    $active = $active ?? '';
    $sticky = $sticky ?? false;
    $navId = $active === 'home' ? 'mainNav' : ($active === 'ticket' ? 'statusNav' : 'userNavbarMenu');
@endphp

<nav class="navbar navbar-expand-lg {{ $theme === 'light' ? 'navbar-light' : 'navbar-dark' }} ult-navbar ult-navbar--{{ $theme }} {{ $sticky ? 'sticky-top' : '' }}"
     aria-label="Navigasi utama">
    <div class="container-xl">
        <a class="navbar-brand ult-navbar__brand" href="{{ route('beranda') }}" aria-label="Beranda ULT Polinema">
            <img src="{{ asset('images/logo polinema.png') }}" alt="Logo Politeknik Negeri Malang">
            <span>
                <strong>UNIT LAYANAN TERPADU</strong>
                <small>POLITEKNIK NEGERI MALANG</small>
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $navId }}"
                aria-controls="{{ $navId }}" aria-expanded="false" aria-label="Buka navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="{{ $navId }}">
            <ul class="navbar-nav ms-lg-auto">
                <li class="nav-item">
                    <a class="nav-link {{ $active === 'home' ? 'active' : '' }}"
                       href="{{ route('beranda') }}#beranda"
                       @if ($active === 'home') aria-current="page" @endif>Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $active === 'services' ? 'active' : '' }}"
                       href="{{ route('layanan.index') }}"
                       @if ($active === 'services') aria-current="page" @endif>Layanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('beranda') }}#faq">FAQ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $active === 'ticket' ? 'active' : '' }}"
                       href="{{ route('cek-status') }}"
                       @if ($active === 'ticket') aria-current="page" @endif>E-Tiket</a>
                </li>
                @auth('web')
                    @php($authenticatedUser = auth('web')->user())
                    @php($avatarPath = $authenticatedUser->getAttribute('avatar'))
                    <li class="nav-item ult-profile-item">
                        <details class="ult-profile">
                            <summary class="ult-profile__toggle" aria-label="Buka profil {{ $authenticatedUser->name }}">
                                <span class="ult-profile__avatar" aria-hidden="true">
                                    @if (filled($avatarPath))
                                        <img src="{{ asset('storage/'.$avatarPath) }}" alt="">
                                    @else
                                        {{ mb_strtoupper(mb_substr(trim($authenticatedUser->name), 0, 1)) }}
                                    @endif
                                </span>
                            </summary>
                            <div class="ult-profile__menu">
                                <strong>{{ $authenticatedUser->name }}</strong>
                                <span>{{ $authenticatedUser->email }}</span>
                                <button type="button" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                                    Logout
                                </button>
                            </div>
                        </details>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

@include('auth.user-modals')
@vite('resources/js/auth.js')

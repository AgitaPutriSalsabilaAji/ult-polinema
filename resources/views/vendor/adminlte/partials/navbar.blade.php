@php
    $pageTitle = match (true) {
        request()->routeIs('admin.dashboard') => 'Dashboard',
        request()->routeIs('admin.pengajuan*', 'admin.detail-pengajuan') => 'Data Pengajuan',
        request()->routeIs('admin.tracking') => 'Tracking E-Ticket',
        request()->routeIs('admin.layanan*') => 'Informasi Layanan',
        request()->routeIs('admin.faq*') => 'FAQ',
        request()->routeIs('admin.rating') => 'Rating Layanan',
        request()->routeIs('admin.profil') => 'Profil & Akun',
        request()->routeIs('admin.daftar-admin') => 'Daftar Admin',
        request()->routeIs('login', 'login.submit') => 'Login Admin',
        default => 'Admin ULT Polinema',
    };
@endphp

<header class="app-header page-title-header">
    <div class="container-fluid">
        <h1 class="page-title-header__title">{{ $pageTitle }}</h1>
    </div>
</header>

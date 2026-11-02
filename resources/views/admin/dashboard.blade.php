@extends('adminlte::page')

@section('title', 'Dashboard ULT Polinema')

@section('content_header')
    <h3 class="admin-page-title mb-1">Dashboard</h3>
    <p class="text-secondary mb-0">
        Selamat datang di halaman Admin ULT Polinema.
    </p>
@stop

@section('content')
<div class="container-fluid px-0 dashboard-page">

    {{-- Statistik Dashboard --}}
    <div class="row g-3">

        <div class="col-md-4">
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="dashboard-label mb-2">Unit Layanan</p>
                            <h2 class="dashboard-number">5</h2>
                            <p class="dashboard-description mb-0">
                                Unit layanan utama
                            </p>
                        </div>

                        <div class="dashboard-icon icon-blue">
                            <i class="bi bi-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="dashboard-label mb-2">Informasi Layanan</p>
                            <h2 class="dashboard-number">{{ $jumlahLayanan }}</h2>
                            <p class="dashboard-description mb-0">
                                Informasi yang dikelola
                            </p>
                        </div>

                        <div class="dashboard-icon icon-green">
                            <i class="bi bi-journal-text"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="dashboard-label mb-2">FAQ</p>
                            <h2 class="dashboard-number">{{ $jumlahFaq }}</h2>
                            <p class="dashboard-description mb-0">
                                Pertanyaan yang tersedia
                            </p>
                        </div>

                        <div class="dashboard-icon icon-orange">
                            <i class="bi bi-question-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Informasi Sistem --}}
    <div class="card dashboard-info border-0 shadow-sm mt-3">
        <div class="card-header bg-white">
            <h6 class="mb-0 fw-bold">Informasi Sistem</h6>
        </div>

        <div class="card-body">
            <p>
                Website Unit Layanan Terpadu Politeknik Negeri Malang.
            </p>

            <p class="mb-0">
                Gunakan menu navigasi untuk mengelola data dan informasi layanan.
            </p>
        </div>
    </div>

</div>
@stop

@section('css')
<style>
    /* HALAMAN DASHBOARD */
    .dashboard-page {
        font-size: 12px;
    }

    /* Judul halaman */
    .admin-page-title {
        font-size: 20px;
        font-weight: 700;
    }

    .content-header p {
        font-size: 12px;
    }

    /* Card statistik */
    .dashboard-card {
        border-radius: 8px;
        min-height: 125px;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .dashboard-card .card-body {
        padding: 17px 18px;
    }

    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,.08) !important;
    }

    /* Judul statistik */
    .dashboard-label {
        color: #475569;
        font-size: 12px;
        font-weight: 600;
    }

    /* Angka statistik */
    .dashboard-number {
        color: #1e293b;
        font-size: 27px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    /* Keterangan */
    .dashboard-description {
        color: #8491a3;
        font-size: 11px;
    }

    /* Icon statistik */
    .dashboard-icon {
        width: 43px;
        height: 43px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        font-size: 20px;
    }

    .icon-blue {
        color: #2563eb;
        background: #eff6ff;
    }

    .icon-green {
        color: #059669;
        background: #ecfdf5;
    }

    .icon-orange {
        color: #d97706;
        background: #fffbeb;
    }

    /* Informasi sistem */
    .dashboard-info {
        border-radius: 8px;
    }

    .dashboard-info .card-header {
        padding: 13px 16px;
        border-bottom: 1px solid #edf0f4;
    }

    .dashboard-info .card-header h6 {
        font-size: 13px;
    }

    .dashboard-info .card-body {
        padding: 15px 16px;
    }

    .dashboard-info p {
        color: #64748b;
        font-size: 12px;
        line-height: 1.6;
    }

    /* Responsif */
    @media (max-width: 767.98px) {
        .dashboard-card {
            min-height: 110px;
        }

        .dashboard-card .card-body {
            padding: 14px;
        }
    }
</style>
@stop
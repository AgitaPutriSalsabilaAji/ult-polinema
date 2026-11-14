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

    {{-- Statistik Pengajuan, E-Ticket, dan Rating --}}
    <div class="dashboard-stats-grid">
        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="dashboard-stat-content">
                        <p class="dashboard-label mb-2">Total Pengajuan</p>
                        <h2 class="dashboard-number">{{ number_format($statistikPengajuan->total_pengajuan ?? 0) }}</h2>
                        <p class="dashboard-description mb-0">Total pengajuan masuk</p>
                    </div>
                    <div class="dashboard-icon icon-blue"><i class="bi bi-inboxes"></i></div>
                </div>
            </div>
        </div>

        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="dashboard-stat-content">
                        <p class="dashboard-label mb-2">Pengajuan Baru</p>
                        <h2 class="dashboard-number">{{ number_format($statistikPengajuan->pengajuan_baru ?? 0) }}</h2>
                        <p class="dashboard-description mb-0">Perlu verifikasi</p>
                    </div>
                    <div class="dashboard-icon icon-orange"><i class="bi bi-envelope-exclamation"></i></div>
                </div>
            </div>
        </div>

        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="dashboard-stat-content">
                        <p class="dashboard-label mb-2">Sedang Diproses</p>
                        <h2 class="dashboard-number">{{ number_format($statistikPengajuan->pengajuan_diproses ?? 0) }}</h2>
                        <p class="dashboard-description mb-0">Pengajuan dalam proses</p>
                    </div>
                    <div class="dashboard-icon icon-purple"><i class="bi bi-gear"></i></div>
                </div>
            </div>
        </div>

        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="dashboard-stat-content">
                        <p class="dashboard-label mb-2">Pengajuan Selesai</p>
                        <h2 class="dashboard-number">{{ number_format($statistikPengajuan->pengajuan_selesai ?? 0) }}</h2>
                        <p class="dashboard-description mb-0">Pengajuan telah diselesaikan</p>
                    </div>
                    <div class="dashboard-icon icon-green"><i class="bi bi-check2-circle"></i></div>
                </div>
            </div>
        </div>

        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="dashboard-stat-content">
                        <p class="dashboard-label mb-2">Total E-Ticket</p>
                        <h2 class="dashboard-number">{{ number_format($statistikPengajuan->total_pengajuan ?? 0) }}</h2>
                        <p class="dashboard-description mb-0">Tercatat di sistem</p>
                    </div>
                    <div class="dashboard-icon icon-yellow"><i class="bi bi-ticket-perforated"></i></div>
                </div>
            </div>
        </div>

        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="dashboard-stat-content">
                        <p class="dashboard-label mb-2">E-Ticket Aktif</p>
                        <h2 class="dashboard-number">{{ number_format($statistikPengajuan->eticket_aktif ?? 0) }}</h2>
                        <p class="dashboard-description mb-0">Masih dalam penanganan</p>
                    </div>
                    <div class="dashboard-icon icon-teal"><i class="bi bi-hourglass-split"></i></div>
                </div>
            </div>
        </div>

        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="dashboard-stat-content">
                        <p class="dashboard-label mb-2">Rating Layanan</p>
                        <h2 class="dashboard-number">{{ number_format((float) ($statistikRating->rata_rata_rating ?? 0), 2) }}</h2>
                        <p class="dashboard-description mb-0">Dari {{ number_format($statistikRating->jumlah_ulasan ?? 0) }} ulasan</p>
                    </div>
                    <div class="dashboard-icon icon-rating"><i class="bi bi-star-fill"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik Layanan --}}
    <div class="row g-3 mt-0">

        <div class="col-md-4">
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="dashboard-label mb-2">Unit Layanan</p>
                            <h2 class="dashboard-number">{{ number_format($jumlahUnit) }}</h2>
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

    .dashboard-stats-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(7, minmax(0, 1fr));
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
        min-height: 112px;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .dashboard-card .card-body {
        padding: 13px 12px;
    }

    .dashboard-stat-content {
        min-width: 0;
    }

    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,.08) !important;
    }

    /* Judul statistik */
    .dashboard-label {
        color: #475569;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.35;
    }

    /* Angka statistik */
    .dashboard-number {
        color: #1e293b;
        font-size: clamp(18px, 1.6vw, 23px);
        font-weight: 700;
        margin-bottom: 4px;
        overflow-wrap: anywhere;
    }

    /* Keterangan */
    .dashboard-description {
        color: #8491a3;
        font-size: 10px;
        line-height: 1.35;
    }

    /* Icon statistik */
    .dashboard-icon {
        flex: 0 0 32px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 16px;
        margin-left: 6px;
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

    .icon-purple {
        color: #7c3aed;
        background: #f5f3ff;
    }

    .icon-yellow {
        color: #ca8a04;
        background: #fefce8;
    }

    .icon-teal {
        color: #0f766e;
        background: #f0fdfa;
    }

    .icon-rating {
        color: #d97706;
        background: #fff7ed;
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
    @media (max-width: 1199.98px) {
        .dashboard-stats-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .dashboard-stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dashboard-card {
            min-height: 105px;
        }

        .dashboard-card .card-body {
            padding: 12px;
        }
    }
</style>
@stop
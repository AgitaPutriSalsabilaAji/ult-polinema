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

    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="status">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Filter Statistik --}}
    <div class="card dashboard-filter border-0 shadow-sm mb-3">
        <div class="card-body">
            <form action="{{ route('admin.dashboard') }}" method="GET">
                <div class="row g-3 align-items-end">
                    @if ($isAdminUlt)
                        <div class="col-12 col-md-4">
                            <label for="id_unit" class="form-label">Unit Layanan</label>
                            <select class="form-select" name="id_unit" id="id_unit">
                                <option value="">Semua Unit</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}"
                                        @selected(($filters['id_unit'] ?? null) == $unit->id)>
                                        {{ $unit->nama_unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="col-12 col-md-4">
                            <label class="form-label">Unit Layanan</label>
                            <input class="form-control" value="{{ $units->first()?->nama_unit ?? 'Unit tidak ditemukan' }}" disabled>
                            <input type="hidden" name="id_unit" value="{{ $filters['id_unit'] }}">
                        </div>
                    @endif

                    <div class="col-12 col-md-2">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai"
                            value="{{ $filters['tanggal_mulai'] }}">
                    </div>

                    <div class="col-12 col-md-2">
                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai"
                            value="{{ $filters['tanggal_selesai'] }}">
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">Terapkan</button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Reset Filter</a>
                            <button type="submit" class="btn btn-outline-danger"
                                formaction="{{ route('admin.dashboard.export') }}" formmethod="GET">
                                <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Statistik Pengajuan, E-Ticket, dan Rating --}}
    <div class="dashboard-stats-grid">
        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="dashboard-stat-content">
                        <p class="dashboard-label mb-2">Total Pengajuan</p>
                        <h2 class="dashboard-number">{{ number_format($statistikPengajuan['total_pengajuan']) }}</h2>
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
                        <h2 class="dashboard-number">{{ number_format($statistikPengajuan['pengajuan_baru']) }}</h2>
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
                        <h2 class="dashboard-number">{{ number_format($statistikPengajuan['pengajuan_diproses']) }}</h2>
                        <p class="dashboard-description mb-0">Dalam proses penanganan</p>
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
                        <h2 class="dashboard-number">{{ number_format($statistikPengajuan['pengajuan_selesai']) }}</h2>
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
                        <p class="dashboard-label mb-2">Pengajuan Ditolak</p>
                        <h2 class="dashboard-number">{{ number_format($statistikPengajuan['pengajuan_ditolak']) }}</h2>
                        <p class="dashboard-description mb-0">Pengajuan tidak disetujui</p>
                    </div>
                    <div class="dashboard-icon icon-red"><i class="bi bi-x-circle"></i></div>
                </div>
            </div>
        </div>

        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="dashboard-stat-content">
                        <p class="dashboard-label mb-2">Informasi Layanan</p>
                        <h2 class="dashboard-number">{{ number_format($informasiLayananAktif) }}</h2>
                        <p class="dashboard-description mb-0">Informasi layanan aktif</p>
                    </div>
                    <div class="dashboard-icon icon-teal"><i class="bi bi-journal-check"></i></div>
                </div>
            </div>
        </div>

        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="dashboard-stat-content">
                        <p class="dashboard-label mb-2">Rating Layanan</p>
                        <h2 class="dashboard-number">{{ number_format($statistikRating['rata_rata_rating'], 2) }} / 5</h2>
                        <p class="dashboard-description mb-0">{{ number_format($statistikRating['jumlah_ulasan']) }} ulasan</p>
                    </div>
                    <div class="dashboard-icon icon-rating"><i class="bi bi-star-fill"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Statistik --}}
    <div class="dashboard-charts-grid mt-3">
        <div class="card dashboard-chart-card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pb-0">
                <h6 class="dashboard-chart-title mb-1">Statistik Pengajuan per Unit</h6>
                <p class="dashboard-description mb-0">Perbandingan jumlah pengajuan menurut status.</p>
            </div>
            <div class="card-body">
                <div class="dashboard-chart-wrap">
                    <div id="dashboard-chart-data"
                        data-unit-statistics="{{ json_encode($unitApplicationStatistics->values(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
                        data-status-statistics="{{ json_encode($pieChart->values(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
                        hidden></div>
                    <canvas id="applicationUnitChart" role="img"
                        aria-label="Grafik pengajuan baru, diproses, selesai, dan ditolak per unit layanan"></canvas>
                </div>
            </div>
        </div>

        <div class="card dashboard-chart-card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pb-0">
                <h6 class="dashboard-chart-title mb-1">Distribusi Status Pengajuan</h6>
                <p class="dashboard-description mb-0">Persentase dan jumlah seluruh pengajuan pada filter aktif.</p>
            </div>
            <div class="card-body">
                <div class="dashboard-chart-wrap dashboard-pie-chart-wrap">
                    <canvas id="applicationStatusChart" role="img"
                        aria-label="Grafik distribusi status pengajuan"></canvas>
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
        gap: 10px;
        grid-template-columns: repeat(7, minmax(0, 1fr));
    }

    .dashboard-charts-grid {
        display: grid;
        gap: 14px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
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

    .icon-red {
        color: #dc2626;
        background: #fef2f2;
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

    .dashboard-filter,
    .dashboard-chart-card {
        border-radius: 8px;
    }

    .dashboard-filter .card-body {
        padding: 14px;
    }

    .dashboard-filter .form-label {
        color: #475569;
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .dashboard-filter .form-control,
    .dashboard-filter .form-select,
    .dashboard-filter .btn {
        font-size: 12px;
        min-height: 34px;
    }

    .dashboard-chart-card .card-header {
        padding: 15px 16px 0;
    }

    .dashboard-chart-card .card-body {
        padding: 10px 16px 16px;
    }

    .dashboard-chart-title {
        color: #334155;
        font-size: 13px;
        font-weight: 700;
    }

    .dashboard-chart-wrap {
        height: 300px;
        position: relative;
    }

    .dashboard-pie-chart-wrap {
        height: 300px;
        max-width: 500px;
        margin-inline: auto;
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

        .dashboard-charts-grid {
            grid-template-columns: minmax(0, 1fr);
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

@section('js')
@vite('resources/js/dashboard.js')
@stop
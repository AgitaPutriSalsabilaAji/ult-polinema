@extends('adminlte::page')

@section('title', 'Rating & Ulasan Layanan ULT')

@section('content_header')
    <h3 class="profile-page-title mb-0">Rating & Ulasan Layanan ULT</h3>
        <p class="text-muted mb-0">
        Lihat penilaian dan feedback langsung dari pemohon layanan ULT Polinema.
    </p>
@stop

@section('content')
<div class="container-fluid px-3 py-4 rating-page">

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                aria-label="Tutup"></button>
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="status">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                aria-label="Tutup"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- Statistik --}}
    <div class="row g-2 mb-3">

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Rata-rata Rating</p>
                            <h2 class="fw-bold mb-1">
                                {{ number_format($rataRataRating ?? 0, 2) }}
                                <small class="text-muted">/ 5</small>
                            </h2>
                            <div class="text-warning rating-stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    {{ $i <= round($rataRataRating ?? 0) ? '★' : '☆' }}
                                @endfor
                            </div>
                        </div>

                        <div class="stat-icon rating-icon">
                            ★
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total Rating</p>
                            <h2 class="fw-bold mb-1">
                                {{ number_format($totalRating ?? 0) }}
                            </h2>
                            <p class="text-muted mb-0">Total ulasan masuk</p>
                        </div>

                        <div class="stat-icon total-icon">
                            ▤
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-4 filter-card">
        <div class="card-body p-4">
            <form action="{{ route('admin.rating-ulasan.index') }}" method="GET">
                <div class="row g-3 align-items-end">

                    {{-- Filter Unit --}}
                    <div class="col-12 col-md-3">
                        <label for="id_unit" class="form-label fw-semibold">Unit Layanan</label>
                        <select name="id_unit" id="id_unit" class="form-select">
                            <option value="">Semua Unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}"
                                    {{ request('id_unit') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->nama_unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Rating --}}
                    <div class="col-12 col-md-2">
                        <label for="rating" class="form-label fw-semibold">Rating</label>
                        <select name="rating" id="rating" class="form-select">
                            <option value="">Semua Rating</option>
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}"
                                    {{ request('rating') == $i ? 'selected' : '' }}>
                                    {{ $i }} Bintang
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Filter Tanggal --}}
                    <div class="col-12 col-md-2">
                        <label for="tanggal_mulai" class="form-label fw-semibold">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                            class="form-control" value="{{ request('tanggal_mulai') }}">
                    </div>

                    <div class="col-12 col-md-2">
                        <label for="tanggal_selesai" class="form-label fw-semibold">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                            class="form-control" value="{{ request('tanggal_selesai') }}">
                    </div>

                    {{-- Tombol --}}
                    <div class="col-12 col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                Terapkan
                            </button>
                            <button type="submit" class="btn btn-outline-primary flex-fill"
                                formaction="{{ route('admin.rating-ulasan.export') }}" formmethod="GET">
                                Export PDF
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Rating & Ulasan --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-0 rating-card-header">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="fw-bold mb-1">Daftar Rating & Ulasan</h5>
                    <p class="text-muted small mb-0">
                        Penilaian yang diberikan oleh pemohon layanan.
                    </p>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                    {{ $ulasan->total() }} ulasan
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3">Pemohon</th>
                        <th class="py-3">Layanan</th>
                        <th class="py-3">Unit</th>
                        <th class="py-3">Rating</th>
                        <th class="py-3">Komentar / Feedback</th>
                        <th class="py-3 pe-4">Tanggal</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($ulasan as $item)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="fw-semibold">
                                    {{ $item->pengguna?->nama_lengkap ?? 'Pengguna tidak ditemukan' }}
                                </div>
                                <small class="text-muted">
                                    {{ $item->pengguna?->nim_nip ?? '-' }}
                                </small>
                            </td>

                            <td class="py-3">
                                {{ $item->layanan?->nama_layanan ?? 'Layanan tidak ditemukan' }}
                            </td>

                            <td class="py-3">
                                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary">
                                    {{ $item->layanan?->unit?->nama_unit ?? '-' }}
                                </span>
                            </td>

                            <td class="py-3">
                                <div class="text-warning text-nowrap rating-stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        {{ $i <= ($item->rating ?? 0) ? '★' : '☆' }}
                                    @endfor
                                </div>
                                <small class="text-muted">
                                    {{ $item->rating ?? 0 }}/5
                                </small>
                            </td>

                            <td class="py-3" style="min-width: 250px; max-width: 400px;">
                                <div class="text-wrap comment-text">
                                    {{ $item->komentar ?: 'Tidak ada komentar.' }}
                                </div>
                            </td>

                            <td class="py-3 pe-4 text-nowrap">
                                {{ $item->dibuat_pada?->format('d M Y') ?? '-' }}
                                <div class="text-muted small">
                                    {{ $item->dibuat_pada?->format('H:i') ?? '' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="mb-2 fs-1 text-muted">☆</div>
                                <h6 class="fw-semibold">Belum ada rating dan ulasan</h6>
                                <p class="text-muted small mb-0">
                                    Belum ada data yang sesuai dengan filter yang dipilih.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($ulasan->hasPages())
            <div class="card-footer bg-white border-0 rating-card-footer">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <small class="text-muted">
                        Menampilkan {{ $ulasan->firstItem() ?? 0 }}
                        sampai {{ $ulasan->lastItem() ?? 0 }}
                        dari {{ $ulasan->total() }} data
                    </small>

                    {{ $ulasan->links() }}
                </div>
            </div>
        @endif

    </div>
</div>
@endsection


@push('css')
<style>
    /* ===== HALAMAN RATING & ULASAN ===== */

/* ===== CARD STATISTIK COMPACT ===== */

    .rating-page .stat-card {
        border-radius: 8px;
        min-height: 105px;
    }

    .rating-page .stat-card .card-body {
        padding: 12px 14px !important;
    }

    .rating-page .stat-card p {
        font-size: 11px;
        margin-bottom: 4px !important;
    }

    .rating-page .stat-card h2 {
        font-size: 21px;
        line-height: 1.2;
        margin-bottom: 4px !important;
    }

    .rating-page .stat-card h2 small {
        font-size: 11px !important;
    }

    .rating-page .rating-stars {
        font-size: 13px;
        letter-spacing: 1px;
        line-height: 1.2;
    }

    .rating-page .stat-icon {
        width: 38px;
        height: 38px;
        min-width: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 19px;
    }

    .rating-page .rating-icon {
        color: #f5ae00;
        background: rgba(255, 193, 7, 0.12);
    }

    .rating-page .total-icon {
        color: #0d6efd;
        background: rgba(13, 110, 253, 0.10);
    }

    @media (max-width: 767.98px) {
        .rating-page .stat-card {
            min-height: 95px;
        }
    }


    /* Tombol */
    .rating-page .btn {
        font-size: 11px;
        padding: 6px 11px;
        border-radius: 5px;
    }

    /* Header tabel */
    .rating-page .rating-card-header {
        padding: 14px 16px !important;
    }

    .rating-page .rating-card-header h5 {
        font-size: 15px;
    }

    .rating-page .rating-card-header p {
        font-size: 11px;
    }

    .rating-page .rating-card-header .badge {
        font-size: 10px;
        padding: 5px 9px !important;
    }

    /* Tabel */
    .rating-page .table {
        font-size: 12px;
        margin-bottom: 0;
    }

    .rating-page .table thead th {
        font-size: 11.5px;
        font-weight: 700;
        padding: 9px 10px !important;
        white-space: nowrap;
    }

    .rating-page .table tbody td {
        padding: 9px 10px !important;
        vertical-align: middle;
        line-height: 1.4;
    }

    .rating-page .table tbody td .fw-semibold {
        font-size: 12px;
    }

    .rating-page .table tbody td small {
        font-size: 10.5px;
    }

    .rating-page .table .badge {
        font-size: 10px;
        padding: 5px 8px;
    }

    /* Rating dan komentar */
    .rating-page .rating-stars {
        font-size: 13px;
    }

    .rating-page .comment-text {
        font-size: 11.5px;
        line-height: 1.5;
    }

    /* Pagination */
    .rating-page .rating-card-footer {
        padding: 12px 16px !important;
    }

    .rating-page .pagination {
        font-size: 11px;
    }

    .rating-page .pagination .page-link {
        padding: 5px 9px;
    }

    @media (max-width: 767.98px) {
        .rating-page .stat-card .card-body {
            padding: 12px !important;
        }
    }

    /* Ukuran font dropdown filter */
    .rating-page .filter-card .form-select {
        font-size: 12px !important;
        font-weight: 400;
        min-height: 32px;
        padding: 5px 10px;
    }

    .rating-page .filter-card .form-control {
        font-size: 12px !important;
        min-height: 32px;
        padding: 5px 10px;
    }

    /* Ukuran label Unit Layanan dan Rating */
    .rating-page .filter-card .form-label, fw-bold {
        font-size: 12px !important;
        font-weight: 600;
        margin-bottom: 5px;
    }

    /* Ukuran teks pilihan dropdown */
    .rating-page .filter-card select option {
        font-size: 11px;
    }
</style>
@endpush

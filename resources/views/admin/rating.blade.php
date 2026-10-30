@extends('adminlte::page')

@section('title', 'Rating & Ulasan Layanan ULT')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="mb-4">
        <h4 class="admin-page-title mb-1">Rating & Ulasan Layanan ULT</h4>
        <p class="text-muted mb-0">
            Lihat penilaian dan feedback langsung dari pemohon layanan ULT Polinema.
        </p>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
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
    <div class="row g-3 mb-4">

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small mb-2">Rata-rata Rating</p>
                            <h2 class="fw-bold mb-2">
                                {{ number_format($rataRataRating ?? 0, 2) }}
                                <small class="text-muted fs-6">/ 5</small>
                            </h2>
                            <div class="text-warning fs-5">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span>{{ $i <= round($rataRataRating ?? 0) ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                        </div>
                        <div class="rounded-3 bg-warning bg-opacity-10 p-3">
                            <span class="fs-2 text-warning">★</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small mb-2">Total Rating</p>
                            <h2 class="fw-bold mb-2">
                                {{ number_format($totalRating ?? 0) }}
                            </h2>
                            <p class="text-muted small mb-0">Total ulasan masuk</p>
                        </div>
                        <div class="rounded-3 bg-primary bg-opacity-10 p-3">
                            <span class="fs-2 text-primary">▤</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.rating-ulasan.index') }}"
                  method="GET">
                <div class="row g-3 align-items-end">

                    <div class="col-12 col-md-5">
                        <label for="id_unit" class="form-label fw-semibold">
                            Unit Layanan
                        </label>
                        <select name="id_unit" id="id_unit" class="form-select">
                            <option value="">Semua Unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id_unit }}"
                                    @selected((string) request('id_unit') === (string) $unit->id_unit)>
                                    {{ $unit->nama_unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="rating" class="form-label fw-semibold">
                            Rating
                        </label>
                        <select name="rating" id="rating" class="form-select">
                            <option value="">Semua Rating</option>
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}"
                                    @selected((string) request('rating') === (string) $i)>
                                    {{ $i }} Bintang
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            Terapkan
                        </button>
                        <a href="{{ route('admin.rating-ulasan.index') }}"
                           class="btn btn-outline-secondary">
                            Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Rating & Ulasan --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-0 p-4">
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
                                <div class="text-warning text-nowrap">
                                    @for ($i = 1; $i <= 5; $i++)
                                        {{ $i <= ($item->rating ?? 0) ? '★' : '☆' }}
                                    @endfor
                                </div>
                                <small class="text-muted">
                                    {{ $item->rating ?? 0 }}/5
                                </small>
                            </td>

                            <td class="py-3" style="min-width: 250px; max-width: 400px;">
                                <div class="text-wrap">
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
            <div class="card-footer bg-white border-0 p-4">
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
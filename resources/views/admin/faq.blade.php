@extends('adminlte::page')

@section('title', 'Manajemen FAQ & Pusat Bantuan')

@section('content_header')
    <h3 class="admin-page-title mb-1">Manajemen FAQ & Pusat Bantuan</h3>
    <p class="text-muted mb-0">Kelola pertanyaan dan jawaban yang ditampilkan kepada pengguna.</p>
@stop

@section('content')
<div class="container-fluid px-0 faq-page">

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Data belum berhasil disimpan.</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- Pencarian dan filter --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.faq.index') }}" method="GET">
                <div class="row g-2 align-items-center">
    <div class="col">
        <input type="text"
               name="search"
               class="form-control"
               placeholder="Cari pertanyaan atau jawaban FAQ..."
               value="{{ request('search') }}">
    </div>

    <div class="col-auto">
        <select name="unit_id" class="form-select">
            <option value="">Pilih Unit</option>
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}"
                    @selected(request('unit_id') == $unit->id)>
                    {{ $unit->nama_unit }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-auto">
        <button type="submit" class="btn btn-outline-primary">
            <i class="bi bi-search"></i> Cari
        </button>
    </div>

    <div class="col-auto">
        <button type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#modalTambahFaq">
            <i class="bi bi-plus-lg"></i>
            Tambah FAQ Baru
        </button>
    </div>
</div>
            </form>
        </div>
    </div>

    {{-- Daftar FAQ --}}
    @forelse ($faqs as $faq)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-3 p-md-4">

                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    <div>
                        @if ($faq->status === 'aktif')
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                AKTIF
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border">
                                NONAKTIF
                            </span>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                    {{-- Tombol Edit --}}
                    <button type="button"
                        class="btn btn-sm btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditFaq{{ $faq->id }}">
                        Edit
                    </button>

    {{-- Tombol Hapus --}}
                    <form action="{{ route('admin.faq.destroy', $faq->id) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('Yakin ingin menghapus FAQ ini?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-sm btn-danger">
                            Hapus
                        </button>
                    </form>
                    </div>
                </div>

                <h6 class="fw-bold mb-2">{{ $faq->pertanyaan }}</h6>

                <p class="text-secondary small mb-3">
                    {{ $faq->jawaban }}
                </p>

                <div class="d-flex flex-wrap justify-content-between gap-2">
                    <small class="text-muted">
                        Unit: <strong>{{ $faq->unit->nama_unit ?? 'Umum' }}</strong>
                    </small>
                </div>
            </div>
        </div>

        {{-- Modal Edit --}}
        <div class="modal fade" id="modalEditFaq{{ $faq->id }}"
            tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('admin.faq.update', $faq->id) }}"
                        method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Edit FAQ</h5>
                            <button type="button" class="btn-close"
                                data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Pertanyaan</label>
                                <input type="text" name="pertanyaan"
                                    class="form-control" required maxlength="255"
                                    value="{{ $faq->pertanyaan }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jawaban</label>
                                <textarea name="jawaban" class="form-control"
                                    rows="5" required>{{ $faq->jawaban }}</textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label for="id_unit">Unit Penanggung Jawab</label>

                                <select name="id_unit" id="id_unit" class="form-control" required>
                                    <option value="">-- Pilih Unit --</option>

                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}"
                                            {{ old('id_unit') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->nama_unit }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('id_unit')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="aktif"
                                        @selected($faq->status === 'aktif')>
                                        Aktif
                                    </option>
                                    <option value="nonaktif"
                                        @selected($faq->status === 'nonaktif')>
                                        Nonaktif
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-question-circle fa-3x text-secondary mb-3"></i>
                <h6 class="fw-bold">Belum ada FAQ</h6>
                <p class="text-muted mb-0">
                    FAQ tidak ditemukan. Coba ubah pencarian atau tambahkan FAQ baru.
                </p>
            </div>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if ($faqs->hasPages())
        <div class="d-flex justify-content-end mt-3">
            {{ $faqs->withQueryString()->links() }}
        </div>
    @endif
</div>

{{-- Modal Tambah FAQ --}}
<div class="modal fade" id="modalTambahFaq" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.faq.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah FAQ Baru</h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pertanyaan</label>
                        <input type="text" name="pertanyaan"
                            class="form-control" required maxlength="255"
                            placeholder="Masukkan pertanyaan FAQ">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jawaban</label>
                        <textarea name="jawaban" class="form-control"
                            rows="5" required
                            placeholder="Masukkan jawaban FAQ"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Unit / Kategori</label>
                        <select name="id_unit" class="form-select" required>
                            <option value="">-- Pilih Unit --</option>

                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}"
                                    @selected(old('id_unit') == $unit->id)>
                                    {{ $unit->nama_unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="aktif" selected>Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan FAQ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop


@push('css')
<style>
    /* ===== HALAMAN MANAJEMEN FAQ ===== */

    .faq-page {
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

    /* Card pencarian dan filter */
    .faq-page > .card .card-body {
        padding: 14px !important;
    }

    .faq-page .form-control,
    .faq-page .form-select {
        font-size: 11px;
        min-height: 33px;
        padding: 6px 10px;
        border-radius: 5px;
    }

    .faq-page .form-control::placeholder {
        font-size: 11px;
    }

    /* Tombol */
    .faq-page .btn {
        font-size: 11px;
        padding: 6px 11px;
        border-radius: 5px;
    }

    /* Tombol tambah FAQ */
    .faq-page .btn-primary {
        font-weight: 500;
    }

    /* Card daftar FAQ */
    .faq-page > .card.mb-3 {
        border-radius: 8px;
    }

    .faq-page > .card.mb-3 .card-body {
        padding: 13px 16px !important;
    }

    /* Badge status */
    .faq-page .badge {
        font-size: 10px;
        padding: 4px 7px;
        font-weight: 600;
    }

    /* Pertanyaan FAQ */
    .faq-page h6 {
        font-size: 13px;
        font-weight: 600;
        line-height: 1.4;
    }

    /* Jawaban FAQ */
    .faq-page .card-body p.small {
        font-size: 11.5px;
        line-height: 1.5;
    }

    /* Informasi unit dan tanggal */
    .faq-page .card-body small {
        font-size: 10.5px;
    }

    /* Jarak tombol edit dan hapus */
    .faq-page .d-flex.gap-2 {
        gap: 5px !important;
    }

    /* Modal tambah dan edit */
    .faq-page .modal-content {
        border: 0;
        border-radius: 8px;
    }

    .faq-page .modal-header {
        padding: 14px 18px;
    }

    .faq-page .modal-title {
        font-size: 16px;
    }

    .faq-page .modal-body {
        padding: 14px 18px;
    }

    .faq-page .modal-footer {
        padding: 10px 18px 14px;
    }

    .faq-page .modal-body .form-label,
    .faq-page .modal-body label {
        font-size: 11.5px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .faq-page .modal-body textarea {
        font-size: 11.5px;
    }

    /* Pagination */
    .faq-page .pagination {
        font-size: 11px;
    }

    .faq-page .pagination .page-link {
        padding: 5px 9px;
    }

    .faq-page .btn-primary {
    color: #fff !important;
    background-color: #0d6efd;
    border-color: #0d6efd;
    }

    .faq-page .btn-primary i,
    .faq-page .btn-primary span {
        color: #fff !important;
    }

    /* Responsif */
    @media (max-width: 767.98px) {
        .faq-page > .card.mb-3 .card-body {
            padding: 12px !important;
        }

        .faq-page .modal-dialog {
            margin: 12px;
        }
    }
</style>
@endpush


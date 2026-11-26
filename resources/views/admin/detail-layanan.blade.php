@extends('adminlte::page')

@section('title', 'Detail Layanan')

@section('content_header')
    <h3 class="admin-page-title mb-1">Manajemen Informasi Layanan</h3>
    <p class="text-muted mb-0">
        Kelola informasi layanan yang ditampilkan kepada pengguna.
    </p>
@stop

@section('content')
<div class="container-fluid px-0 detail-layanan-page">

    {{-- Tombol Kembali --}}
    <div class="mb-3">
        <a href="{{ route('admin.layanan.index') }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke daftar layanan
        </a>
    </div>

    {{-- Informasi Dasar Layanan --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex align-items-center">
            <h5 class="card-title fw-bold mb-0">
                {{ $layanan->nama_layanan }}
            </h5>

            <a href="{{ route('admin.layanan.edit', $layanan) }}"
            class="btn btn-primary btn-sm ms-auto">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
        </div>
        <div class="card-body">
            <h6 class="section-title mb-3">Informasi Dasar</h6>

            <div class="table-responsive">
                <table class="table table-bordered table-hover detail-table mb-0">
                    <tbody>
                        <tr>
                            <th>Nama Layanan</th>
                            <td>{{ $layanan->nama_layanan }}</td>
                        </tr>

                        <tr>
                            <th>Unit Penanggung Jawab</th>
                            <td>{{ $layanan->unit?->nama_unit ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Estimasi Waktu</th>
                            <td>{{ $layanan->estimasi_waktu ?: '-' }}</td>
                        </tr>

                        <tr>
                            <th>Tarif</th>
                            <td>{{ $layanan->tarif ?: '-' }}</td>
                        </tr>

                        <tr>
                            <th>Jam Operasional</th>
                            <td>{{ $layanan->jam_operasional ?: '-' }}</td>
                        </tr>

                        <tr>
                            <th>Status</th>
                            <td>
                                @if($layanan->status === 'aktif')
                                    <span class="badge text-bg-success">
                                        AKTIF
                                    </span>
                                @else
                                    <span class="badge text-bg-secondary">
                                        NONAKTIF
                                    </span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Deskripsi Singkat</th>
                            <td>{{ $layanan->deskripsi_singkat ?: '-' }}</td>
                        </tr>

                        <tr>
                            <th>Tautan Eksternal</th>
                            <td>
                                @if($layanan->tautan_eksternal)
                                    <a href="{{ $layanan->tautan_eksternal }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="external-link">
                                        {{ $layanan->tautan_eksternal }}
                                        <i class="bi bi-box-arrow-up-right ms-1"></i>
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Bagian Informasi Layanan --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Bagian Informasi Layanan</h5>
            <p class="text-muted mb-0 section-description">
                Rincian informasi yang ditampilkan kepada pengguna.
            </p>
        </div>

        <span class="badge bg-light text-secondary border">
            {{ $layanan->bagianLayanan->count() }} Bagian
        </span>
    </div>

    @forelse($layanan->bagianLayanan as $bagian)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0">
                    <span class="section-number">{{ $loop->iteration }}</span>
                    {{ $bagian->judul_bagian }}
                </h6>
            </div>

            <div class="card-body">
                <div class="section-content">{{ $bagian->konten }}</div>

                @if($bagian->gambar_pendukung)
                    <div class="mt-3">
                        <img
                            src="{{ asset('storage/'.$bagian->gambar_pendukung) }}"
                            alt="{{ $bagian->judul_bagian }}"
                            class="img-fluid rounded supporting-image">
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="alert alert-info border-0 shadow-sm">
            <i class="bi bi-info-circle me-1"></i>
            Belum ada bagian informasi untuk layanan ini.
        </div>
    @endforelse

</div>
@stop

@section('css')
<style>
    /* =====================================
       HALAMAN DETAIL LAYANAN
    ===================================== */

    .detail-layanan-page {
        font-size: 12px;
        color: #243247;
    }

    /* Judul halaman */
    .admin-page-title {
        font-size: 20px;
        font-weight: 700;
    }

    .content-header p {
        font-size: 12px;
    }

    /* Card */
    .detail-layanan-page .card {
        border-radius: 8px;
        overflow: hidden;
    }

    .detail-layanan-page .card-header {
        padding: 13px 16px;
        border-bottom: 1px solid #e5eaf1;
    }

    .detail-layanan-page .card-body {
        padding: 16px;
    }

    /* Judul card */
    .detail-layanan-page .card-title {
        float: none;
        font-size: 14px;
        line-height: 1.5;
    }

    .detail-layanan-page .section-title {
        font-size: 13px;
        font-weight: 700;
        color: #243247;
    }

    /* Tombol */
    .detail-layanan-page .btn {
        font-size: 11px;
        padding: 6px 10px;
        border-radius: 5px;
    }

    .detail-layanan-page .btn i {
        font-size: 11px;
    }

    /* Tabel informasi dasar */
    .detail-layanan-page .detail-table {
        font-size: 12px;
        border-color: #e0e6ee;
    }

    .detail-layanan-page .detail-table th,
    .detail-layanan-page .detail-table td {
        padding: 10px 13px;
        vertical-align: middle;
        line-height: 1.5;
    }

    .detail-layanan-page .detail-table th {
        width: 230px;
        font-weight: 600;
        color: #34445a;
        background: #f8fafc;
    }

    .detail-layanan-page .detail-table td {
        color: #475569;
        overflow-wrap: anywhere;
    }

    /* Badge status */
    .detail-layanan-page .badge {
        font-size: 10px;
        font-weight: 600;
        padding: 5px 8px;
        border-radius: 4px;
    }

    /* Tautan eksternal */
    .detail-layanan-page .external-link {
        color: #2563eb;
        text-decoration: none;
        overflow-wrap: anywhere;
    }

    .detail-layanan-page .external-link:hover {
        text-decoration: underline;
    }

    /* Judul bagian informasi */
    .detail-layanan-page h5.fw-bold {
        font-size: 15px;
    }

    .detail-layanan-page .section-description {
        font-size: 11px;
    }

    .detail-layanan-page .section-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 23px;
        height: 23px;
        margin-right: 5px;
        color: #2452a4;
        background: #eaf1ff;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 700;
    }

    /* Konten bagian informasi */
    .detail-layanan-page .section-content {
        font-size: 12px;
        line-height: 1.7;
        color: #475569;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    /* Gambar pendukung */
    .detail-layanan-page .supporting-image {
        max-width: 100%;
        max-height: 350px;
        object-fit: contain;
        border: 1px solid #e2e8f0;
        padding: 3px;
    }

    /* Responsif */
    @media (max-width: 767.98px) {
        .detail-layanan-page .card-header {
            padding: 12px;
        }

        .detail-layanan-page .card-body {
            padding: 12px;
        }

        .detail-layanan-page .detail-table th,
        .detail-layanan-page .detail-table td {
            padding: 9px 10px;
        }

        .detail-layanan-page .detail-table th {
            width: 150px;
            min-width: 150px;
        }

        .detail-layanan-page .supporting-image {
            max-height: 250px;
        }
    }
</style>
@stop
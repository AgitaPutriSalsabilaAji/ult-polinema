@extends('adminlte::page')

@section('title', 'Informasi Layanan')

@section('content_header')
    <h3 class="profile-page-title mb-0">Manajemen Informasi Layanan</h3>
        <p class="text-muted mb-0">
        Kelola informasi layanan yang ditampilkan kepada pengguna.
    </p>
@stop

@section('content')
<div class="container-fluid layanan-page">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="GET"
                  action="{{ route('admin.layanan.index') }}"
                  class="row g-2 align-items-center">

                <div class="col-lg">
                    <input type="search"
                           name="q"
                           class="form-control"
                           value="{{ request('q') }}"
                           placeholder="Cari nama layanan atau kata kunci...">
                </div>

                <div class="col-md-3">
                    <select name="unit" class="form-select">
                        <option value="">Semua Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}"
                                @selected(request('unit') == $unit->id)>
                                {{ $unit->nama_unit }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif"
                            @selected(request('status') === 'aktif')>
                            Aktif
                        </option>
                        <option value="nonaktif"
                            @selected(request('status') === 'nonaktif')>
                            Nonaktif
                        </option>
                    </select>
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>

                <div class="col-auto">
                    <a href="{{ route('admin.layanan.create') }}"
                       class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i>
                        Tambah Layanan Baru
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Layanan</th>
                            <th>Unit Penanggung Jawab</th>
                            <th>Estimasi Waktu</th>
                            <th>Status</th>
                            <th>Diperbarui</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($layanans as $layanan)
                        <tr>
                            <td>
                                <strong>{{ $layanan->nama_layanan }}</strong>
                                <div class="text-secondary small">
                                    {{ \Illuminate\Support\Str::limit(
                                        $layanan->deskripsi_singkat, 75
                                    ) }}
                                </div>
                            </td>

                            <td>
                                {{ $layanan->unit?->nama_unit ?? '-' }}
                            </td>

                            <td>{{ $layanan->estimasi_waktu }}</td>

                            {{-- Toggle Status --}}
                            <td>
                                <form method="POST"
                                      action="{{ route('admin.layanan.status', $layanan) }}"
                                      class="form-status-toggle">
                                    @csrf
                                    @method('PATCH')

                                    <div class="status-control">
                                        <input
                                            type="checkbox"
                                            class="status-toggle"
                                            role="switch"
                                            aria-label="Ubah status {{ $layanan->nama_layanan }}"
                                            {{ $layanan->status === 'aktif' ? 'checked' : '' }}
                                            onchange="this.form.submit()"
                                        >

                                        <span class="status-label {{ $layanan->status === 'aktif' ? 'aktif' : 'nonaktif' }}">
                                            {{ $layanan->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </div>
                                </form>
                            </td>

                            <td>
                                {{ $layanan->diperbarui_pada
                                    ? \Illuminate\Support\Carbon::parse(
                                        $layanan->diperbarui_pada
                                    )->format('d M Y')
                                    : '-' }}
                            </td>

                            {{-- Tombol Aksi --}}
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.layanan.show', $layanan) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        Lihat
                                    </a>

                                    <a href="{{ route('admin.layanan.edit', $layanan) }}"
                                       class="btn btn-sm btn-primary">
                                        Edit
                                    </a>

                                    <form method="POST"
                                          action="{{ route('admin.layanan.destroy', $layanan) }}"
                                          onsubmit="return confirm('Hapus layanan ini beserta semua bagian informasinya?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-4">
                                Belum ada data layanan.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($layanans->hasPages())
            <div class="card-footer">
                {{ $layanans->links() }}
            </div>
        @endif
    </div>
</div>
@stop


@push('css')
<style>
    /* Ukuran dasar halaman */
    .layanan-page {
        font-size: 12px;
    }

    .admin-page-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 12px;
    }

    /* Card */
    .layanan-page .card {
        margin-bottom: 12px;
        border-radius: 6px;
    }

    .layanan-page .card-body {
        padding: 12px;
    }

    .layanan-page .card-header {
        padding: 9px 12px;
    }

    /* Input dan dropdown */
    .layanan-page .form-control,
    .layanan-page .form-select {
        height: 32px;
        min-height: 32px;
        padding: 5px 9px;
        font-size: 11px;
        border-radius: 4px;
    }

    /* Tombol */
    .layanan-page .btn {
        font-size: 11px;
        padding: 6px 10px;
        border-radius: 4px;
        line-height: 1.3;
    }

    .layanan-page .btn-sm {
        font-size: 10.5px;
        padding: 4px 8px;
    }

    .layanan-page .btn i {
        font-size: 11px;
    }

    /* Tabel */
    .layanan-page .table {
        font-size: 11px;
    }

    .layanan-page .table th {
        font-size: 12px;
        font-weight: 600;
        padding: 11px 16px !important;
        white-space: nowrap;
    }

    .layanan-page .table td {
        padding: 11px 16px !important;
        vertical-align: middle;
    }

    .layanan-page .table td strong {
        font-size: 12px;
        font-weight: 600;
    }

    .layanan-page .table td .small {
        font-size: 10px;
        margin-top: 3px;
    }

    /* Toggle Status */
    .layanan-page .form-status-toggle {
        display: inline-block;
        margin: 0;
    }

    .layanan-page .status-control {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }

    .layanan-page .status-toggle {
        appearance: none;
        -webkit-appearance: none;
        position: relative;
        width: 34px;
        height: 18px;
        margin: 0;
        border: 1px solid #cbd5e1;
        border-radius: 20px;
        background-color: #cbd5e1;
        cursor: pointer;
        transition: background-color 0.2s, border-color 0.2s;
    }

    .layanan-page .status-toggle::before {
        content: '';
        position: absolute;
        width: 12px;
        height: 12px;
        top: 2px;
        left: 2px;
        border-radius: 50%;
        background-color: #fff;
        transition: transform 0.2s;
    }

    .layanan-page .status-toggle:checked {
        background-color: #16a34a;
        border-color: #16a34a;
    }

    .layanan-page .status-toggle:checked::before {
        transform: translateX(16px);
    }

    .layanan-page .status-toggle:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.15);
    }

    .layanan-page .status-label {
        font-size: 9px;
        font-weight: 600;
    }

    .layanan-page .status-label.aktif {
        color: #16a34a;
    }

    .layanan-page .status-label.nonaktif {
        color: #64748b;
    }

    /* Kolom Status */
    .layanan-page .table th:nth-child(4),
    .layanan-page .table td:nth-child(4) {
        min-width: 85px;
        text-align: center;
    }

    /* Kolom Aksi */
    .layanan-page .table th:last-child {
        min-width: 180px;
        width: 180px;
    }

    .layanan-page .action-buttons {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 4px;
    }

    .layanan-page .action-buttons form {
        display: inline-flex;
        margin: 0;
        flex-shrink: 0;
    }

    .layanan-page .action-buttons .btn {
        white-space: nowrap;
        flex-shrink: 0;
    }

    /* Footer dan pagination */
    .layanan-page .card-footer {
        padding: 8px 12px;
    }

    .layanan-page .pagination {
        margin-bottom: 0;
        font-size: 11px;
    }

    .layanan-page .page-link {
        padding: 4px 8px;
        font-size: 10.5px;
    }

    /* Notifikasi */
    .layanan-page .alert {
        padding: 9px 12px;
        font-size: 11px;
        margin-bottom: 12px;
    }
</style>
@endpush
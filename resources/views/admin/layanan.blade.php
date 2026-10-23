@extends('adminlte::page')

@section('title', 'Informasi Layanan')

@section('content_header')
    <h1>Manajemen Informasi Layanan</h1>
@stop

@section('content')
<div class="container-fluid">

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
        <div class="card-header">
            <h3 class="card-title">Daftar Layanan</h3>
        </div>

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
                            <th style="min-width: 240px">Aksi</th>
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

                            <td>
                                {{ $layanan->diperbarui_pada
                                    ? \Illuminate\Support\Carbon::parse(
                                        $layanan->diperbarui_pada
                                    )->format('d M Y')
                                    : '-' }}
                            </td>

                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <a href="{{ route(
                                        'admin.layanan.show', $layanan
                                    ) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        Lihat
                                    </a>

                                    <a href="{{ route(
                                        'admin.layanan.edit', $layanan
                                    ) }}"
                                       class="btn btn-sm btn-primary">
                                        Edit
                                    </a>

                                    <form method="POST"
                                          action="{{ route(
                                              'admin.layanan.status', $layanan
                                          ) }}">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                                class="btn btn-sm {{ $layanan->status === 'aktif' ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                            {{ $layanan->status === 'aktif'
                                                ? 'Nonaktifkan'
                                                : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <form method="POST"
                                          action="{{ route(
                                              'admin.layanan.destroy', $layanan
                                          ) }}"
                                          onsubmit="return confirm('Hapus layanan ini beserta semua bagian informasinya?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-danger">
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

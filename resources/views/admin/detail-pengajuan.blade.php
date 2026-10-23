@extends('adminlte::page')

@section('title', 'Detail Pengajuan')

@section('content_header')
    <h1 class="h4 font-weight-bold">Detail Pengajuan Pelayanan</h1>
@stop

@section('content')
<div class="container-fluid">

    <div class="mb-3">
        <a href="{{ route('admin.pengajuan.index') }}"
           class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <strong>{{ $pengajuan->nomor_tiket }}</strong>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="font-weight-bold">Informasi Pemohon</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th>Nama</th>
                            <td>
                                {{ $pemohon->nama_lengkap
                                    ?? $pemohon->nama
                                    ?? $pemohon->nama_pengguna
                                    ?? $pemohon->name
                                    ?? 'Pemohon #' . $pengajuan->id_pengguna }}
                            </td>
                        </tr>
                        <tr>
                            <th>ID Pengguna</th>
                            <td>{{ $pengajuan->id_pengguna }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $pemohon->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>NIP</th>
                            <td>{{ $pemohon->nip ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h6 class="font-weight-bold">Informasi Pengajuan</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th>Layanan</th>
                            <td>{{ $pengajuan->layanan?->nama_layanan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Unit</th>
                            <td>{{ $pengajuan->unit?->nama_unit ?? 'Belum ditentukan' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Masuk</th>
                            <td>{{ $pengajuan->dibuat_pada?->format('d/m/Y H:i') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ strtoupper($pengajuan->status) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <h6 class="font-weight-bold mt-3">Subjek Pengajuan</h6>
            <p>{{ $pengajuan->subjek }}</p>

            <h6 class="font-weight-bold">Deskripsi</h6>
            <div class="border rounded p-3" style="white-space:pre-wrap">{{ $pengajuan->deskripsi }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <strong>Riwayat Disposisi</strong>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Unit Tujuan</th>
                        <th>Catatan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pengajuan->disposisi->sortByDesc('tanggal_disposisi') as $item)
                    <tr>
                        <td>{{ $item->tanggal_disposisi?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>{{ $item->unitTujuan?->nama_unit ?? '-' }}</td>
                        <td>{{ $item->catatan_disposisi ?: '-' }}</td>
                        <td>{{ strtoupper($item->status_disposisi) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center p-3">
                            Belum ada riwayat disposisi.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <strong>Riwayat Status Pengajuan</strong>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Aktivitas</th>
                        <th>Status Sebelumnya</th>
                        <th>Status Baru</th>
                        <th>Aktor</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pengajuan->riwayat->sortByDesc('dibuat_pada') as $item)
                    <tr>
                        <td>{{ $item->dibuat_pada?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>{{ $item->judul_status }}</td>
                        <td>{{ $item->status_sebelumnya ?? '-' }}</td>
                        <td>{{ $item->status_baru ?? '-' }}</td>
                        <td>{{ $item->nama_aktor }}</td>
                        <td>{{ $item->deskripsi }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center p-3">
                            Belum ada riwayat status.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

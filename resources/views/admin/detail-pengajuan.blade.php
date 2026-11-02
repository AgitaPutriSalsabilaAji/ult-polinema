@extends('adminlte::page')

@section('title', 'Detail Pengajuan')

@section('content_header')
    <h1 class="h4 font-weight-bold">Detail Pengajuan Pelayanan</h1>
@stop

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                            <td>@include('admin.partials.status-badge', ['status' => $pengajuan->status])</td>
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
                        <th>Admin Tujuan</th>
                        <th>Catatan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pengajuan->disposisi->sortByDesc('tanggal_disposisi') as $item)
                    <tr>
                        <td>{{ $item->tanggal_disposisi?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>{{ $item->unitTujuan?->nama_unit ?? '-' }}</td>
                        <td>{{ $item->adminTujuan?->nama_lengkap_gelar ?? '-' }}</td>
                        <td>{{ $item->catatan_disposisi ?: '-' }}</td>
                        <td>@include('admin.partials.status-badge', ['status' => $item->status_disposisi])</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center p-3">
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
                        <td>
                            @if($item->status_sebelumnya)
                                @include('admin.partials.status-badge', ['status' => $item->status_sebelumnya])
                            @else
                                -
                            @endif
                        </td>
                        <td>@include('admin.partials.status-badge', ['status' => $item->status_baru])</td>
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

    @if(auth('admin')->user()?->peran === 'admin_unit'
        && $pengajuan->status === 'diproses')
        <div class="card">
            <div class="card-header"><strong>Tindakan Pengajuan</strong></div>
            <div class="card-body d-flex flex-wrap gap-2">
                <form method="POST" action="{{ route('admin.pengajuan.selesai', $pengajuan) }}">
                    @csrf
                    <button class="btn btn-success" type="submit">
                        <i class="fas fa-check"></i> Selesai
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.pengajuan.tolak', $pengajuan) }}">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="alasan" class="form-control"
                               placeholder="Alasan penolakan (opsional)" maxlength="5000">
                        <button class="btn btn-outline-danger" type="submit">
                            <i class="fas fa-times"></i> Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@stop

@section('css')
<style>
    .ticket-status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.2;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .ticket-status-badge--baru { color: #1d4ed8; background: #dbeafe; }
    .ticket-status-badge--didisposisikan { color: #7e22ce; background: #f3e8ff; }
    .ticket-status-badge--diproses { color: #b45309; background: #fef3c7; }
    .ticket-status-badge--selesai { color: #047857; background: #d1fae5; }
    .ticket-status-badge--ditolak { color: #be123c; background: #ffe4e6; }
    .ticket-status-badge--default { color: #475569; background: #e2e8f0; }
</style>
@stop


@extends('adminlte::page')

@section('title', 'Detail Layanan')

@section('content_header')
    <h1>Detail Informasi Layanan</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="mb-3">
        <a href="{{ route('admin.layanan.index') }}">
            <i class="bi bi-arrow-left"></i> Kembali ke daftar layanan
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title fw-bold">
                {{ $layanan->nama_layanan }}
            </h3>

            <div class="card-tools">
                <a href="{{ route('admin.layanan.edit', $layanan) }}"
                   class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            </div>
        </div>

        <div class="card-body">
            <h5>Informasi Dasar</h5>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th style="width:230px">Nama Layanan</th>
                        <td>{{ $layanan->nama_layanan }}</td>
                    </tr>
                    <tr>
                        <th>Unit Penanggung Jawab</th>
                        <td>{{ $layanan->unit?->nama_unit ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Estimasi Waktu</th>
                        <td>{{ $layanan->estimasi_waktu }}</td>
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
                                <span class="badge text-bg-success">AKTIF</span>
                            @else
                                <span class="badge text-bg-secondary">NONAKTIF</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Deskripsi Singkat</th>
                        <td>{{ $layanan->deskripsi_singkat }}</td>
                    </tr>
                    <tr>
                        <th>Tautan Eksternal</th>
                        <td>
                            @if($layanan->tautan_eksternal)
                                <a href="{{ $layanan->tautan_eksternal }}"
                                   target="_blank" rel="noopener noreferrer">
                                    {{ $layanan->tautan_eksternal }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <h4 class="mb-3">Bagian Informasi Layanan</h4>

    @forelse($layanan->bagianLayanan as $bagian)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title fw-bold">
                    {{ $loop->iteration }}. {{ $bagian->judul_bagian }}
                </h3>
            </div>

            <div class="card-body">
                <div style="white-space:pre-wrap;line-height:1.8">{{ $bagian->konten }}</div>

                @if($bagian->gambar_pendukung)
                    <div class="mt-3">
                        <img
                            src="{{ asset(
                                'storage/'.$bagian->gambar_pendukung
                            ) }}"
                            alt="{{ $bagian->judul_bagian }}"
                            class="img-fluid rounded"
                            style="max-height:400px">
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            Belum ada bagian informasi untuk layanan ini.
        </div>
    @endforelse
</div>
@stop

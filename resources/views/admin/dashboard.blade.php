
@extends('adminlte::page')

@section('title', 'Dashboard ULT Polinema')

@section('content_header')
    <h3 class="admin-page-title">Dashboard</h3>
    <p class="text-secondary">
        Selamat datang di halaman Admin ULT Polinema.
    </p>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Unit Layanan</h5>
                    <h2>5</h2>
                    <p class="text-secondary mb-0">
                        Unit layanan utama
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Informasi Layanan</h5>
                    <h2>{{ $jumlahLayanan }}</h2>
                    <p class="text-secondary mb-0">
                        Informasi yang dikelola
                    </p>
                </div>
            </div>
        </div>

        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>FAQ</h5>
                    <h2>{{ $jumlahFaq }}</h2>
                    <p class="text-secondary mb-0">
                        Pertanyaan yang tersedia
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-0">Informasi Sistem</h5>
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
@stop

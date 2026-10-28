@extends('adminlte::page')

@section('title', 'Search Results')

@section('content_header')
    <h1>Hasil Pencarian</h1>
@stop

@section('content')
    <div class="container-fluid">
        @if ($query === '')
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-search fs-2 text-secondary" aria-hidden="true"></i>
                    <p class="mb-0 mt-3">Masukkan kata kunci untuk mencari layanan, FAQ, atau pengajuan.</p>
                </div>
            </div>
        @elseif ($results->isEmpty())
            <div class="card" role="status" aria-live="polite">
                <div class="card-body text-center py-5">
                    <i class="bi bi-search fs-2 text-secondary" aria-hidden="true"></i>
                    <h2 class="h5 mt-3">Tidak ada hasil ditemukan</h2>
                    <p class="text-body-secondary mb-0">
                        Tidak ditemukan konten yang cocok dengan “{{ $query }}”.
                    </p>
                </div>
            </div>
        @else
            <p class="text-body-secondary" role="status" aria-live="polite">
                Menampilkan {{ $results->count() }} hasil teratas untuk “{{ $query }}”.
            </p>

            <div class="list-group shadow-sm">
                @foreach ($results as $result)
                    <a class="list-group-item list-group-item-action p-3"
                       href="{{ $result['url'] }}">
                        <div class="d-flex flex-column flex-sm-row justify-content-between gap-2">
                            <h2 class="h6 mb-0">{{ $result['title'] }}</h2>
                            <span class="badge text-bg-primary align-self-start">{{ $result['type'] }}</span>
                        </div>
                        @if ($result['excerpt'])
                            <p class="text-body-secondary small mt-2 mb-0">
                                {{ \Illuminate\Support\Str::limit($result['excerpt'], 240) }}
                            </p>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@stop

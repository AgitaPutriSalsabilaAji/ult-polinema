<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan ULT Polinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/layanan.css', 'resources/js/app.js'])
</head>
<body>
<div class="layanan-page">
    @include('user.navbar', ['theme' => 'purple', 'active' => 'services'])

    <section class="intro"><div class="content-width">
        <div class="breadcrumb"><a href="{{ route('beranda') }}">Beranda</a> / <strong>Layanan</strong></div>
        <h1>LAYANAN ULT POLINEMA</h1>
        <p>Pilih layanan sesuai kebutuhan Anda. Temukan informasi persyaratan, estimasi waktu penyelesaian, dan prosedur resmi berbagai layanan Politeknik Negeri Malang.</p>
    </div></section>

    <section class="filters"><div class="content-width">
        <form class="search-form" action="{{ route('layanan.index') }}" method="GET">
            <input type="search" name="q" placeholder="Cari layanan..." value="{{ $keyword ?? request('q') }}">
            @if ($kategori !== 'semua') <input type="hidden" name="kategori" value="{{ $kategori }}"> @endif
            <button type="submit">Cari</button>
        </form>
        <nav class="category-tabs">
            @php
                $daftarKategori = [
                    'semua' => 'SEMUA',
                    'akademik' => 'AKADEMIK',
                    'keuangan' => 'KEUANGAN',
                    'kemahasiswaan' => 'KEMAHASISWAAN',
                    'humas' => 'KERJA SAMA & HUMAS',
                    'kepegawaian' => 'KEPEGAWAIAN',
                ];
            @endphp
            @foreach ($daftarKategori as $key => $label)
                <a href="{{ route('layanan.index', array_filter(['kategori' => $key === 'semua' ? null : $key], fn ($value) => $value !== null && $value !== '')) }}" class="{{ $kategori === $key ? 'active' : '' }}">
                    {{ $label }} ({{ $jumlahKategori[$key] ?? 0 }})
                </a>
            @endforeach
        </nav>

        <div class="result-line">
            <span>
                Menampilkan {{ $services->total() }} dari {{ $totalLayanan }} Layanan Tersedia
            </span>
        </div>
    </div></section>

    <main class="listing"><div class="content-width">
        <div class="service-grid">
            @forelse ($services as $index => $service)
                <article class="service-card">
                    <div class="service-main">
                        <span class="service-number">{{ sprintf('%02d', ($services->firstItem() ?? 1) + $index) }}</span>
                        <div><h2>{{ $service['nama'] }}</h2><p>{{ $service['deskripsi'] }}</p></div>
                    </div>
                    <div class="service-bottom">
                        <span class="unit-tag">Unit: {{ $service['unit'] }}</span>
                        @php
                            $slugHumasAktif = [
                                'kunjungan-sekolah',
                                'edufair',
                                'liputan-kegiatan',
                                'informasi-spmb',
                                'informasi-pengaduan',
                            ];
                            $detailAktif = in_array($service['slug'], $slugHumasAktif, true);
                        @endphp
                        @if ($detailAktif)
                            <a class="detail-link" href="{{ route('layanan.detail', ['slug' => $service['slug']]) }}">→ Detail Layanan</a>
                        @else
                            <span class="detail-link detail-disabled" aria-disabled="true" title="Detail layanan belum tersedia">Detail Belum Tersedia</span>
                        @endif
                    </div>
                </article>
            @empty
                <div class="empty-state">Layanan tidak ditemukan. Coba kata kunci lain.</div>
            @endforelse
        </div>
        @if ($services->hasPages())
            <div class="pagination">
                <div>
                    @if ($services->onFirstPage()) <span class="page-button disabled">‹ Sebelumnya</span>
                    @else <a class="page-button" href="{{ $services->previousPageUrl() }}">‹ Sebelumnya</a> @endif
                </div>
                <div class="page-numbers">
                    @foreach ($services->getUrlRange(1, $services->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="{{ $services->currentPage() === $page ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach
                </div>
                <div>
                    @if ($services->hasMorePages()) <a class="page-button" href="{{ $services->nextPageUrl() }}">› Selanjutnya</a>
                    @else <span class="page-button disabled">› Selanjutnya</span> @endif
                </div>
            </div>
        @endif
    </div></main>

    @include('user.footer')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

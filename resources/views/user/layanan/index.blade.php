
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan ULT Polinema</title>

    @vite(['resources/js/app.js'])
</head>
<body>
<div class="layanan-page">

    <header class="topbar">
        <a href="/" class="brand">
            <img src="{{ asset('images/logo-polinema.png') }}" alt="Logo Polinema">
            <span>
                <span class="brand-name">UNIT LAYANAN TERPADU</span><br>
                <span class="brand-subtitle">POLITEKNIK NEGERI MALANG</span>
            </span>
        </a>

        <nav class="nav-links">
            <a href="/">Beranda</a>
            <a href="{{ route('layanan.index') }}" class="active">Layanan</a>
            <a href="/faq">FAQ</a>
            <a href="/e-tiket">E-Tiket</a>
        </nav>

        <a href="{{ route('layanan.index') }}" class="check-button">
            ⌕ Cek Layanan
        </a>
    </header>

    <section class="intro">
        <div class="content-width">
            <div class="breadcrumb">Beranda / <strong>Layanan</strong></div>
            <h1>LAYANAN ULT POLINEMA</h1>
            <p>
                Pilih layanan sesuai kebutuhan Anda. Temukan informasi persyaratan,
                estimasi waktu penyelesaian, dan prosedur resmi berbagai layanan
                Politeknik Negeri Malang.
            </p>
        </div>
    </section>

    <section class="filters">
        <div class="content-width">
            <form class="search-form" action="{{ route('layanan.index') }}" method="GET">
                <input type="search" name="q" placeholder="Cari layanan..."
                       value="{{ request('q') }}">

                @if ($kategori !== 'semua')
                    <input type="hidden" name="kategori" value="{{ $kategori }}">
                @endif

                <button type="submit">Cari</button>
            </form>

            <nav class="category-tabs">
                @foreach ([
                    'semua' => 'SEMUA',
                    'akademik' => 'AKADEMIK',
                    'keuangan' => 'KEUANGAN',
                    'kemahasiswaan' => 'KEMAHASISWAAN',
                    'humas' => 'KERJA SAMA & HUMAS',
                    'kepegawaian' => 'KEPEGAWAIAN'
                ] as $key => $label)
                    <a
                        href="{{ route('layanan.index', $key === 'semua' ? [] : ['kategori' => $key]) }}"
                        class="{{ $kategori === $key ? 'active' : '' }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            <div class="result-line">
                <span>Menampilkan {{ count($services) }} layanan</span>
                <span class="muted">Urutan: Populer &amp; Terbanyak Diajukan</span>
            </div>
        </div>
    </section>

    <main class="listing">
        <div class="content-width">
            <div class="service-grid">
                @forelse ($services as $index => $service)
                    <article class="service-card">
                        <div class="service-main">
                            <span class="service-number">
                                {{ sprintf('%02d', $index + 1) }}
                            </span>

                            <div>
                                <h2>{{ $service[0] }}</h2>
                                <p>{{ $service[1] }}</p>
                            </div>
                        </div>

                        <div class="service-bottom">
                            <span class="unit-tag">Unit: {{ $service[2] }}</span>
                            <a class="detail-link"
                            href="{{ route('layanan.detail', [
                                'slug' => \Illuminate\Support\Str::slug($service[0])
                            ]) }}">
                                → Detail Layanan
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">
                        Layanan tidak ditemukan. Coba kata kunci lain.
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    <footer class="site-footer">
        <div class="footer-grid">
            <div>
                <div class="footer-title">UNIT LAYANAN TERPADU</div>
                <p class="footer-copy">
                    Portal layanan publik terintegrasi untuk memudahkan akses
                    informasi dan pengajuan layanan Polinema.
                </p>
            </div>

            <div>
                <div class="footer-title">Tautan Cepat</div>
                <div class="footer-links">
                    <a href="/">Beranda</a>
                    <a href="{{ route('layanan.index') }}">Informasi Layanan</a>
                    <a href="/faq">FAQ</a>
                    <a href="#">Profil ULT</a>
                </div>
            </div>

            <div>
                <div class="footer-title">Unit Layanan</div>
                <div class="footer-links">
                    <a href="{{ route('layanan.index', ['kategori' => 'akademik']) }}">Akademik</a>
                    <a href="{{ route('layanan.index', ['kategori' => 'keuangan']) }}">Keuangan</a>
                    <a href="{{ route('layanan.index', ['kategori' => 'kemahasiswaan']) }}">Kemahasiswaan</a>
                    <a href="{{ route('layanan.index', ['kategori' => 'kepegawaian']) }}">Kepegawaian</a>
                    <a href="{{ route('layanan.index', ['kategori' => 'humas']) }}">Humas &amp; Kerja Sama</a>
                </div>
            </div>

            <div>
                <div class="footer-title">Kontak</div>
                <div class="contact-line">Jl. Soekarno Hatta No. 9, Malang</div>
                <div class="contact-line">ult@polinema.ac.id</div>
                <div class="contact-line">(0341) 404424</div>
            </div>
        </div>

        <div class="copyright">
            © 2026 Politeknik Negeri Malang — Unit Layanan Terpadu.
            All Rights Reserved.
        </div>
    </footer>

</div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda | Unit Layanan Terpadu Polinema</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/beranda.css', 'resources/js/beranda.js'])
</head>
<body>

@include('user.navbar', ['theme' => 'light', 'active' => 'home', 'sticky' => true])

<main>
    {{-- HERO --}}
    <section class="hero-section" id="beranda">
        <div class="hero-overlay" aria-hidden="true"></div>

        <div class="container-xl hero-content">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">

                    <h1>Layanan Polinema<br>Dalam Satu Pintu</h1>

                    <p class="hero-description">
                        Satu pintu layanan informasi dan administrasi Politeknik Negeri Malang
                        yang mudah, cepat, transparan, dan terpadu untuk civitas akademika
                        dan masyarakat luas tanpa perlu login.
                    </p>

                    <div class="d-flex flex-wrap gap-2 hero-actions">
                        <a href="{{ route('layanan.index') }}" class="btn btn-orange">
                            <i class="bi bi-grid-fill me-2"></i>Jelajahi Layanan
                        </a>
                        <a href="{{ route('cek-status') }}" class="btn btn-ghost">
                            <i class="bi bi-ticket-perforated me-2"></i>Cek Status E-Ticket
                        </a>
                    </div>
                </div>

                <div class="col-lg-6 d-flex justify-content-lg-end">
                    <figure class="hero-illustration mb-0">
                        <img src="{{ asset('images/ilustrasi layanan.png') }}"
                             alt="Ilustrasi Layanan ULT Polinema"
                             class="img-fluid">
                    </figure>
                </div>
            </div>
        </div>
    </section>

    {{-- PENCARIAN LAYANAN --}}
    <section class="service-search-section">
        <div class="container-xl text-center">
            <h2 class="section-heading">APA YANG ANDA BUTUHKAN?</h2>
            <p class="section-subtitle">
                Temukan layanan yang sesuai dengan kebutuhan administrasi atau informasi Anda.
            </p>

            <form class="search-form mx-auto" id="serviceSearchForm" role="search">
                <i class="bi bi-search search-icon"></i>
                <input type="search" id="serviceSearch" aria-label="Cari layanan"
                       aria-autocomplete="list" aria-controls="serviceSearchResults"
                       aria-expanded="false"
                       placeholder="Cari layanan yang Anda butuhkan...">
                <button class="btn btn-orange" type="submit">Cari Layanan</button>
                <div class="service-search-dropdown d-none" id="serviceSearchResults"
                     role="listbox" aria-label="Rekomendasi layanan">
                    @foreach ($searchServices as $service)
                        <a class="service-search-option" role="option"
                           href="{{ route('layanan.detail', ['slug' => $service['slug']]) }}"
                           data-service-option>
                            {{ $service['nama'] }}
                        </a>
                    @endforeach
                    <p class="service-search-empty d-none" role="status">Layanan tidak ditemukan</p>
                </div>
            </form>

            <p class="search-hint">
                Belum tahu layanan yang tepat?
                <a href="#kategori">＋ Cek Semua Kategori Layanan</a>
            </p>
        </div>
    </section>

    {{-- AJUKAN PERMOHONAN --}}
    <section class="quick-apply-section">
        <div class="container-xl">
            <div class="quick-apply">
                <div>
                    <span class="mini-label">Akses Langsung Tanpa Akun</span>
                    <h2>AJUKAN PERMOHONAN LAYANAN</h2>
                    <p>Pilih layanan yang Anda butuhkan dan buat e-ticket resmi tanpa perlu mendaftar akun.</p>
                </div>
                <a href="{{ route('layanan.index') }}" class="btn btn-orange">
                    <i class="bi bi-arrow-right me-2"></i>PILIH LAYANAN
                </a>
            </div>
        </div>
    </section>

    {{-- DAFTAR LAYANAN --}}
    <section class="services-section section-space" id="layanan">
        <div class="container-xl">
            <div class="section-title-row">
                <div>
                    <h2 class="section-heading text-start">Layanan</h2>
                    <p class="section-subtitle text-start">
                        Temukan layanan Politeknik Negeri Malang sesuai kebutuhan Anda.
                    </p>
                </div>
                <a href="{{ route('layanan.index') }}" class="see-all">LIHAT SEMUA LAYANAN <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="row g-3 service-list" id="serviceList">
                @forelse ($services as $index => $service)
                    <div class="col-md-6 service-item" data-search="{{ $service['nama'] }} {{ $service['deskripsi'] }} {{ $service['unit'] }}">
                        <a href="{{ in_array($service['slug'], $slugsAktif, true) ? route('layanan.detail', ['slug' => $service['slug']]) : route('layanan.index', ['kategori' => $kategori]) }}" class="service-link">
                            <span class="service-number">{{ sprintf('%02d', $index + 1) }}</span>
                            <span class="service-copy">
                                <strong>{{ $service['nama'] }}</strong>
                                <small>{{ $service['deskripsi'] }}</small>
                                <em>Unit: {{ $service['unit'] }}</em>
                            </span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                @empty
                    <div class="col-12 no-results" id="noResults">
                        Belum ada layanan dengan halaman detail untuk kategori ini.
                    </div>
                @endforelse

                @if ($services->isNotEmpty())
                    <div class="col-12 no-results d-none" id="noResults">
                        Layanan tidak ditemukan. Coba gunakan kata kunci lain.
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- KATEGORI --}}
    <section class="category-section section-space" id="kategori">
        <div class="container-xl text-center">
            <h2 class="section-heading">KATEGORI LAYANAN</h2>
            <p class="section-subtitle">Pilih rumpun unit kerja penanggung jawab layanan di Polinema.</p>

            <div class="row g-3 justify-content-center category-grid">
                <div class="col-6 col-md">
                    <a class="category-card" href="{{ route('beranda', ['kategori' => 'akademik']) }}#layanan">
                        <span class="category-icon"><i class="bi bi-journal-text"></i></span>
                        <strong>AKADEMIK</strong><small>{{ $jumlahKategori['akademik'] }} Layanan Tersedia</small>
                    </a>
                </div>
                <div class="col-6 col-md">
                    <a class="category-card" href="{{ route('beranda', ['kategori' => 'keuangan']) }}#layanan">
                        <span class="category-icon"><i class="bi bi-cash-coin"></i></span>
                        <strong>KEUANGAN</strong><small>{{ $jumlahKategori['keuangan'] }} Layanan Tersedia</small>
                    </a>
                </div>
                <div class="col-6 col-md">
                    <a class="category-card" href="{{ route('beranda', ['kategori' => 'kemahasiswaan']) }}#layanan">
                        <span class="category-icon"><i class="bi bi-people-fill"></i></span>
                        <strong>KEMAHASISWAAN</strong><small>{{ $jumlahKategori['kemahasiswaan'] }} Layanan Tersedia</small>
                    </a>
                </div>
                <div class="col-6 col-md">
                    <a class="category-card" href="{{ route('beranda', ['kategori' => 'humas']) }}#layanan">
                        <span class="category-icon"><i class="bi bi-megaphone-fill"></i></span>
                        <strong>KERJA SAMA &amp; HUMAS</strong><small>{{ $jumlahKategori['humas'] }} Layanan Tersedia</small>
                    </a>
                </div>
                <div class="col-6 col-md">
                    <a class="category-card" href="{{ route('beranda', ['kategori' => 'kepegawaian']) }}#layanan">
                        <span class="category-icon"><i class="bi bi-briefcase-fill"></i></span>
                        <strong>KEPEGAWAIAN</strong><small>{{ $jumlahKategori['kepegawaian'] }} Layanan Tersedia</small>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ALUR PELAYANAN --}}
    <section class="steps-section section-space">
        <div class="container-xl">
            <div class="text-center mb-4">
                <span class="mini-label orange-label">Prosedur Pengajuan</span>
                <h2 class="section-heading">ALUR PELAYANAN ULT</h2>
                <p class="section-subtitle">Gunakan layanan ULT Politeknik Negeri Malang melalui empat langkah sederhana.</p>
            </div>

            <div class="row g-3">
                <div class="col-6 col-lg-3"><article class="step-card"><span>01</span><h3>Pilih Layanan</h3><p>Cari dan pilih jenis layanan sesuai dengan keperluan administrasi Anda.</p></article></div>
                <div class="col-6 col-lg-3"><article class="step-card"><span>02</span><h3>Isi Form Pengajuan</h3><p>Lengkapi identitas pemohon, deskripsi kebutuhan, dan unggah dokumen pendukung.</p></article></div>
                <div class="col-6 col-lg-3"><article class="step-card"><span>03</span><h3>Dapatkan E-Ticket</h3><p>Nomor e-ticket dibuat otomatis oleh sistem sebagai bukti tanda terima resmi.</p></article></div>
                <div class="col-6 col-lg-3"><article class="step-card"><span>04</span><h3>Pantau Status</h3><p>Gunakan nomor e-ticket untuk melihat perkembangan verifikasi dan penyelesaian berkas.</p></article></div>
            </div>
        </div>
    </section>

    {{-- CEK STATUS --}}
    <section class="status-section section-space" id="cek-status">
        <div class="container-xl text-center">
            <h2 class="section-heading">CEK STATUS PENGAJUAN</h2>
            <p class="section-subtitle">Sudah mengajukan layanan? Pantau proses pengajuan berkas Anda menggunakan nomor e-ticket.</p>

            <form class="status-form mx-auto" id="statusForm" action="{{ route('cek-status') }}" method="GET">
                <input type="text" id="ticketNumber" name="ticket" placeholder="ULT-2026-000123" aria-label="Nomor e-ticket" required>
                <button type="submit" class="btn btn-purple">Cek Status</button>
            </form>
            <p id="statusFeedback" class="status-feedback" aria-live="polite"></p>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="faq-section section-space" id="faq">
        <div class="container">
            <div class="text-center mb-4">
                <span class="mini-label orange-label">Bantuan &amp; Panduan</span>
                <h2 class="section-heading">PERTANYAAN YANG SERING DIAJUKAN</h2>
            </div>

            <div class="accordion faq-accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne" aria-expanded="true" aria-controls="faqOne">
                            Apakah saya harus login untuk mengajukan layanan?
                        </button>
                    </h3>
                    <div id="faqOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">Tidak. Portal ULT Polinema dirancang sebagai portal publik yang dapat diakses tanpa membuat akun.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqTwo" aria-expanded="false" aria-controls="faqTwo">
                            Bagaimana cara mendapatkan e-ticket?
                        </button>
                    </h3>
                    <div id="faqTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">Setelah formulir pengajuan diisi dan dikonfirmasi, sistem akan menerbitkan nomor e-ticket apabila fitur pengajuan sudah terhubung ke backend.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqThree" aria-expanded="false" aria-controls="faqThree">
                            Bagaimana cara mengecek status pengajuan?
                        </button>
                    </h3>
                    <div id="faqThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">Masukkan nomor e-ticket pada kolom Cek Status. Status ditampilkan setelah fitur terhubung dengan database Laravel.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqFour" aria-expanded="false" aria-controls="faqFour">
                            Bagaimana jika saya tidak tahu harus memilih layanan apa?
                        </button>
                    </h3>
                    <div id="faqFour" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">Gunakan kolom pencarian atau pilih kategori layanan yang sesuai, lalu hubungi unit terkait jika membutuhkan informasi tambahan.</div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a class="btn btn-light btn-sm all-faq" href="#faqAccordion">LIHAT SEMUA FAQ</a>
            </div>
        </div>
    </section>

    {{-- KONTAK --}}
    <section class="contact-section section-space">
        <div class="container-xl text-center">
            <h2 class="section-heading">INFORMASI KONTAK</h2>
            <p class="section-subtitle">Hubungi kami untuk informasi lebih lanjut.</p>

            <div class="row g-3 contact-grid">
                <div class="col-6 col-lg-3"><div class="contact-card"><span><i class="bi bi-geo-alt"></i></span><strong>Alamat</strong><p>Jl. Soekarno Hatta No. 9, Malang,<br>Jawa Timur 65141</p></div></div>
                <div class="col-6 col-lg-3"><div class="contact-card"><span><i class="bi bi-telephone"></i></span><strong>Telepon</strong><p>(0341) 404424</p></div></div>
                <div class="col-6 col-lg-3"><div class="contact-card"><span><i class="bi bi-envelope"></i></span><strong>Email</strong><p>ult@polinema.ac.id</p></div></div>
                <div class="col-6 col-lg-3"><div class="contact-card"><span><i class="bi bi-clock"></i></span><strong>Jam Operasional</strong><p>Senin - Jumat<br>08.00 - 16.00 WIB</p></div></div>
            </div>

            <a class="btn whatsapp-btn mt-4" href="https://wa.me/6285117479099" target="_blank" rel="noopener noreferrer">
                <i class="bi bi-whatsapp me-2"></i>Hubungi via WhatsApp
            </a>
        </div>
    </section>
</main>

@include('user.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
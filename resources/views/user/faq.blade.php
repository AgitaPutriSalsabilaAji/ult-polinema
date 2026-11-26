<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pertanyaan dan jawaban seputar layanan ULT Polinema.">
    <title>FAQ | Unit Layanan Terpadu Polinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite('resources/css/faq.css')
</head>
<body class="faq-page">
@include('user.navbar', ['theme' => 'light', 'active' => 'faq', 'sticky' => true])

<main>
    <section class="faq-hero">
        <div class="container-xl faq-hero__content">
            <p class="faq-hero__eyebrow">PUSAT INFORMASI ULT POLINEMA</p>
            <h1>Pertanyaan yang Sering Diajukan</h1>
            <p class="faq-hero__description">
                Temukan jawaban atas pertanyaan umum seputar layanan, pengajuan permohonan,
                e-ticket, dan pengecekan status layanan Unit Layanan Terpadu POLINEMA.
            </p>

            <form class="faq-search" action="{{ route('faq.index') }}#pertanyaan-umum" method="GET" role="search">
                <label class="visually-hidden" for="faq-search-input">Cari pertanyaan FAQ</label>
                <i class="bi bi-search" aria-hidden="true"></i>
                <input id="faq-search-input" type="search" name="search" value="{{ $search }}"
                       placeholder="Cari pertanyaan yang Anda cari..." maxlength="100">
                @if ($selectedCategory)
                    <input type="hidden" name="kategori" value="{{ $selectedCategory }}">
                @endif
                <button type="submit">Cari</button>
            </form>
        </div>
    </section>

    <section class="faq-content">
        <div class="container-xl">
            <nav class="faq-categories" aria-label="Filter kategori FAQ">
                <a class="faq-category {{ $selectedCategory === null ? 'is-active' : '' }}"
                   href="{{ route('faq.index', array_filter(['search' => $search ?: null])) }}"
                   @if ($selectedCategory === null) aria-current="true" @endif>Semua</a>
                @foreach ($categories as $category)
                    <a class="faq-category {{ $selectedCategory === $category->id ? 'is-active' : '' }}"
                       href="{{ route('faq.index', array_filter(['search' => $search ?: null, 'kategori' => $category->id])) }}"
                       @if ($selectedCategory === $category->id) aria-current="true" @endif>
                        {{ $category->nama_unit }}
                    </a>
                @endforeach
            </nav>

            <section class="faq-list" id="pertanyaan-umum" aria-labelledby="faq-list-title">
                <div class="faq-list__heading">
                    <p class="faq-list__eyebrow">DAFTAR JAWABAN</p>
                    <h2 id="faq-list-title">PERTANYAAN UMUM</h2>
                </div>

                <div class="accordion faq-accordion" id="faqPageAccordion">
                    @forelse ($faqs as $faq)
                        <article class="accordion-item faq-card">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#faq-answer-{{ $faq->id }}"
                                        aria-expanded="false"
                                        aria-controls="faq-answer-{{ $faq->id }}">
                                    <span>{{ $faq->pertanyaan }}</span>
                                    <i class="bi bi-chevron-down faq-card__icon" aria-hidden="true"></i>
                                </button>
                            </h3>
                            <div id="faq-answer-{{ $faq->id }}" class="accordion-collapse collapse"
                                 data-bs-parent="#faqPageAccordion">
                                <div class="accordion-body">
                                    <p>{{ $faq->jawaban }}</p>
                                    @if ($faq->unit)
                                        <span class="faq-card__unit">{{ $faq->unit->nama_unit }}</span>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="faq-empty" role="status">
                            <i class="bi {{ $search !== '' ? 'bi-search' : 'bi-chat-square-text' }}" aria-hidden="true"></i>
                            <p>
                                {{ $search !== ''
                                    ? 'Pertanyaan tidak ditemukan'
                                    : 'Belum ada pertanyaan yang tersedia.' }}
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </section>

    <section class="faq-help">
        <div class="container-xl">
            <div class="faq-help__content">
                <div class="faq-help__copy">
                    <p class="faq-help__eyebrow">BUTUH BANTUAN LEBIH LANJUT?</p>
                    <h2>Masih Punya Pertanyaan?</h2>
                    <p>
                        Jika Anda tidak menemukan jawaban yang dicari dalam daftar FAQ di atas,
                        silakan hubungi tim dukungan kami secara langsung atau buat tiket baru.
                    </p>
                </div>
                <div class="faq-help__actions">
                    <a class="faq-help__button faq-help__button--contact"
                       href="https://wa.me/6285117479099" target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-whatsapp" aria-hidden="true"></i>
                        Hubungi Kami
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

@include('user.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

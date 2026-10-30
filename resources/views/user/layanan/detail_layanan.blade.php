<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $layanan['nama'] }} | ULT Polinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/layanan.css', 'resources/js/app.js'])
</head>
<body>
<div class="layanan-page detail-page">
    @include('user.navbar', ['theme' => 'purple', 'active' => 'services'])
    <section class="intro"><div class="container-xl">
        <h1>{{ strtoupper($layanan['nama']) }}</h1><p>{{ $layanan['deskripsi'] }}</p>
    </div></section>
    <main class="detail-content container-xl">
        <div class="detail-left">
            @if ($bagian->isNotEmpty())
                <div class="accordion service-information-accordion" id="serviceInformationAccordion">
                    @foreach ($bagian as $section)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="serviceSectionHeading{{ $section->id }}">
                                <button class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#serviceSectionCollapse{{ $section->id }}"
                                        aria-expanded="false"
                                        aria-controls="serviceSectionCollapse{{ $section->id }}">
                                    {{ $section->judul_bagian }}
                                </button>
                            </h2>
                            <div id="serviceSectionCollapse{{ $section->id }}"
                                 class="accordion-collapse collapse"
                                 aria-labelledby="serviceSectionHeading{{ $section->id }}"
                                 data-bs-parent="#serviceInformationAccordion">
                                <div class="accordion-body">
                                    @if (filled($section->gambar_pendukung) && \Illuminate\Support\Facades\Storage::disk('public')->exists($section->gambar_pendukung))
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($section->gambar_pendukung) }}"
                                             alt="{{ $section->judul_bagian }}"
                                             class="service-section-image img-fluid rounded mb-3"
                                             loading="lazy">
                                    @endif
                                    <div class="service-section-content">{{ $section->konten }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <aside class="detail-right">
            <div class="information-card"><div class="information-label">Unit Pengampu</div><strong>{{ $layanan['unit'] }}</strong><h2>▣ &nbsp; INFORMASI LAYANAN</h2>
                <div class="information-row"><span>Jenis Layanan</span><strong>{{ $layanan['nama'] }}</strong></div>
                @if (!empty($layanan['estimasi']))
                    <div class="information-row"><span>Estimasi Waktu</span><strong class="time-value">{{ $layanan['estimasi'] }}</strong></div>
                @endif
                @if (!empty($layanan['tarif']))
                    <div class="information-row"><span>Biaya / Tarif</span><strong>{{ $layanan['tarif'] }}</strong></div>
                @endif
                @if (!empty($layanan['jam']))
                    <div class="information-row"><span>Jam Operasional Layanan</span><strong>{{ $layanan['jam'] }}</strong></div>
                @endif
                <a href="{{ route('pengajuan.form') }}?layanan={{ $layanan['slug'] }}" class="apply-button">✎ &nbsp; AJUKAN LAYANAN</a>
                <a href="{{ route('layanan.index') }}" class="back-button">‹ &nbsp; Kembali ke Daftar Layanan</a>
            </div>
            <div class="help-card"><strong><span class="section-icon">⊙</span> Butuh Bantuan?</strong><p>Silakan hubungi helpdesk Bagian Kerja Sama &amp; Humas Polinema.</p><a href="https://wa.me/6285117479099" target="_blank" rel="noopener noreferrer" class="whatsapp-button">◉ &nbsp; Hubungi via WhatsApp</a></div>
        </aside>
    </main>
    @include('user.footer')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

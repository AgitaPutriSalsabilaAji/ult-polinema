<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $layanan['nama'] }} | ULT Polinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/js/app.js'])
</head>
<body>
<div class="layanan-page detail-page">
    @include('user.navbar', ['theme' => 'purple', 'active' => 'services'])
    <section class="intro"><div class="content-width">
        <div class="breadcrumb"><a href="{{ route('beranda') }}">Beranda</a> / <a href="{{ route('layanan.index') }}">Layanan</a> / <strong>{{ $layanan['nama'] }}</strong></div>
        <h1>{{ strtoupper($layanan['nama']) }}</h1><p>{{ $layanan['deskripsi'] }}</p>
    </div></section>
    <main class="detail-content content-width">
        <div class="detail-left">
            <section class="detail-section"><h2><span class="section-icon">●</span> TENTANG LAYANAN</h2><p>{{ $layanan['tentang'] ?? $layanan['deskripsi'] }}</p></section>
            @if (!empty($layanan['persyaratan']))
                <section class="detail-section"><h2><span class="section-icon">✓</span> PERSYARATAN PENGAJUAN</h2><div class="requirement-list">
                    @foreach ($layanan['persyaratan'] as $item)
                        <div class="requirement-item"><span class="requirement-check">●</span><div><strong>{{ $item['judul'] }}</strong><p>{{ $item['deskripsi'] }}</p></div></div>
                    @endforeach
                </div></section>
            @endif
            @if (!empty($layanan['prosedur']))
                <section class="detail-section"><h2><span class="section-icon">♧</span> PROSEDUR PELAYANAN</h2><div class="procedure-grid">
                    @foreach ($layanan['prosedur'] as $index => $step)
                        <div class="procedure-item"><span class="procedure-number">{{ sprintf('%02d', $index + 1) }}</span><p>{{ $step }}</p></div>
                    @endforeach
                </div></section>
            @endif
            @if (!empty($layanan['dokumen']))
                <section class="detail-section"><h2><span class="section-icon">▤</span> DOKUMEN YANG DIBUTUHKAN</h2><div class="document-table-wrap"><table class="document-table"><thead><tr><th>Nama Dokumen</th><th>Format / Tipe</th><th>Ketentuan</th></tr></thead><tbody>
                    @foreach ($layanan['dokumen'] as $dokumen)
                        <tr><td>{{ $dokumen['nama'] }}</td><td>{{ $dokumen['format'] }}</td><td><span class="document-status {{ $dokumen['ketentuan'] === 'Wajib' ? 'required' : '' }}">{{ $dokumen['ketentuan'] }}</span></td></tr>
                    @endforeach
                </tbody></table></div></section>
            @endif
            <section class="related-section"><h2>LAYANAN TERKAIT</h2><div class="related-grid">
                @foreach ($terkait as $item)
                    <a href="{{ route('layanan.detail', ['slug' => $item['slug']]) }}" class="related-item"><strong>{{ $item['nama'] }}</strong>@if (!empty($item['estimasi']))<span>{{ $item['estimasi'] }}</span>@endif</a>
                @endforeach
            </div></section>
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
                <a href="/e-tiket" class="apply-button">✎ &nbsp; AJUKAN LAYANAN</a>
                <a href="{{ route('layanan.index') }}" class="back-button">‹ &nbsp; Kembali ke Daftar Layanan</a>
            </div>
            <div class="help-card"><strong><span class="section-icon">⊙</span> Butuh Bantuan?</strong><p>Silakan hubungi helpdesk Bagian Kerja Sama &amp; Humas Polinema.</p><a href="https://wa.me/62341404424" target="_blank" rel="noopener noreferrer" class="whatsapp-button">◉ &nbsp; Hubungi via WhatsApp</a></div>
        </aside>
    </main>
    @include('user.footer')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

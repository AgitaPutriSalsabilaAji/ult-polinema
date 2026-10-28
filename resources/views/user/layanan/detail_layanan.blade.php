<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $layanan['nama'] }} | ULT Polinema</title>
    @vite(['resources/js/app.js'])
</head>
<body>
<div class="layanan-page detail-page">
    <header class="topbar">
        <a href="{{ route('beranda') }}" class="brand"><img src="{{ asset('images/logo polinema.png') }}" alt="Logo Polinema"><span><span class="brand-name">UNIT LAYANAN TERPADU</span><br><span class="brand-subtitle">POLITEKNIK NEGERI MALANG</span></span></a>
        <nav class="nav-links"><a href="{{ route('beranda') }}">Beranda</a><a href="{{ route('layanan.index') }}" class="active">Layanan</a><a href="/faq">FAQ</a><a href="/e-tiket">E-Tiket</a></nav>
        <a href="{{ route('layanan.index') }}" class="check-button">⌕ Cek Layanan</a>
    </header>
    <section class="intro"><div class="content-width">
        <div class="breadcrumb"><a href="{{ route('beranda') }}">Beranda</a> / <a href="{{ route('layanan.index') }}">Layanan</a> / <strong>{{ $layanan['nama'] }}</strong></div>
        <h1>{{ strtoupper($layanan['nama']) }}</h1><p>{{ $layanan['deskripsi'] }}</p>
    </div></section>
    <main class="detail-content content-width">
        <div class="detail-left">
            <section class="detail-section"><h2><span class="section-icon">●</span> TENTANG LAYANAN</h2><p>{{ $layanan['tentang'] ?? $layanan['deskripsi'] }}</p></section>
            <section class="detail-section"><h2><span class="section-icon">✓</span> PERSYARATAN PENGAJUAN</h2><div class="requirement-list">
                @foreach ($layanan['persyaratan'] as $item)
                    <div class="requirement-item"><span class="requirement-check">●</span><div><strong>{{ $item['judul'] }}</strong><p>{{ $item['deskripsi'] }}</p></div></div>
                @endforeach
            </div></section>
            <section class="detail-section"><h2><span class="section-icon">♧</span> PROSEDUR PELAYANAN</h2><div class="procedure-grid">
                @foreach ($layanan['prosedur'] as $index => $step)
                    <div class="procedure-item"><span class="procedure-number">{{ sprintf('%02d', $index + 1) }}</span><p>{{ $step }}</p></div>
                @endforeach
            </div></section>
            <section class="detail-section"><h2><span class="section-icon">▤</span> DOKUMEN YANG DIBUTUHKAN</h2><div class="document-table-wrap"><table class="document-table"><thead><tr><th>Nama Dokumen</th><th>Format / Tipe</th><th>Ketentuan</th></tr></thead><tbody>
                @foreach ($layanan['dokumen'] as $dokumen)
                    <tr><td>{{ $dokumen['nama'] }}</td><td>{{ $dokumen['format'] }}</td><td><span class="document-status {{ $dokumen['ketentuan'] === 'Wajib' ? 'required' : '' }}">{{ $dokumen['ketentuan'] }}</span></td></tr>
                @endforeach
            </tbody></table></div></section>
            <section class="related-section"><h2>LAYANAN TERKAIT</h2><div class="related-grid">
                @foreach ($terkait as $item)
                    <a href="{{ route('layanan.detail', ['slug' => $item['slug']]) }}" class="related-item"><strong>{{ $item['nama'] }}</strong><span>{{ $item['estimasi'] }}</span></a>
                @endforeach
            </div></section>
        </div>
        <aside class="detail-right">
            <div class="information-card"><div class="information-label">Unit Pengampu</div><strong>{{ $layanan['unit'] }}</strong><h2>▣ &nbsp; INFORMASI LAYANAN</h2>
                <div class="information-row"><span>Jenis Layanan</span><strong>{{ $layanan['nama'] }}</strong></div>
                <div class="information-row"><span>Estimasi Waktu</span><strong class="time-value">{{ $layanan['estimasi'] }}</strong></div>
                <div class="information-row"><span>Biaya / Tarif</span><strong>{{ $layanan['tarif'] }}</strong></div>
                <div class="information-row"><span>Jam Operasional Layanan</span><strong>{{ $layanan['jam'] }}</strong></div>
                <a href="/e-tiket" class="apply-button">✎ &nbsp; AJUKAN LAYANAN</a>
                <a href="{{ route('layanan.index') }}" class="back-button">‹ &nbsp; Kembali ke Daftar Layanan</a>
            </div>
            <div class="help-card"><strong><span class="section-icon">⊙</span> Butuh Bantuan?</strong><p>Silakan hubungi helpdesk Bagian Kerja Sama &amp; Humas Polinema.</p><a href="https://wa.me/62341404424" target="_blank" rel="noopener noreferrer" class="whatsapp-button">◉ &nbsp; Hubungi via WhatsApp</a></div>
        </aside>
    </main>
    <footer class="site-footer"><div class="footer-grid">
        <div><div class="footer-brand"><img src="{{ asset('images/logo polinema.png') }}" alt="Logo Politeknik Negeri Malang" class="logo-polinema"><div><div class="footer-title">UNIT LAYANAN TERPADU</div><div class="footer-subtitle">Politeknik Negeri Malang</div></div></div><p class="footer-copy">Portal layanan publik terintegrasi untuk memudahkan akses informasi dan pengajuan layanan bagi mahasiswa, alumni, dosen, tenaga kependidikan, dan masyarakat umum.</p></div>
        <div><div class="footer-title">Tautan Cepat</div><div class="footer-links"><a href="{{ route('beranda') }}">Beranda</a><a href="{{ route('layanan.index') }}">Informasi Layanan</a><a href="/faq">FAQ</a><a href="#">Profil ULT</a></div></div>
        <div><div class="footer-title">Unit Layanan</div><div class="footer-links"><a href="{{ route('layanan.index',['kategori'=>'akademik']) }}">Akademik</a><a href="{{ route('layanan.index',['kategori'=>'keuangan']) }}">Keuangan</a><a href="{{ route('layanan.index',['kategori'=>'kemahasiswaan']) }}">Kemahasiswaan</a><a href="{{ route('layanan.index',['kategori'=>'kepegawaian']) }}">Kepegawaian</a><a href="{{ route('layanan.index',['kategori'=>'humas']) }}">Humas &amp; Kerja Sama</a></div></div>
        <div><div class="footer-title">Kontak</div><div class="contact-line">⌖ &nbsp; Jl. Soekarno Hatta No. 9, Malang</div><div class="contact-line">✉ &nbsp; ult@polinema.ac.id</div><div class="contact-line">☎ &nbsp; (0341) 404424</div><div class="socials"><a href="#">◎</a><a href="#">▶</a><a href="#">f</a></div></div>
    </div><div class="copyright">© 2026 Politeknik Negeri Malang — Unit Layanan Terpadu. All Rights Reserved.</div></footer>
</div>
</body>
</html>

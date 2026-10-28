<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Pengajuan | Unit Layanan Terpadu Polinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/cek-status.css', 'resources/js/cek-status.js'])
</head>
<body class="status-page">
<header class="status-header">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-xl">
            <a class="navbar-brand status-brand" href="{{ route('beranda') }}">
                <img src="{{ asset('images/logo polinema.png') }}" width="35" height="35" alt="Logo Polinema">
                <span><strong>UNIT LAYANAN TERPADU</strong><small>POLITEKNIK NEGERI MALANG</small></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#statusNav" aria-controls="statusNav" aria-expanded="false" aria-label="Buka navigasi"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="statusNav">
                <ul class="navbar-nav mx-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="{{ route('beranda') }}">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('beranda') }}#layanan">Layanan</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('beranda') }}#faq">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="{{ route('cek-status') }}">E-Tiket</a></li>
                </ul>
                <a class="btn status-button" href="#form-cek-status"><i class="bi bi-search me-2"></i>Cek Layanan</a>
            </div>
        </div>
    </nav>
</header>

<main class="status-main">
    <section class="status-intro container-xl" id="form-cek-status">
        <h1>Cek Status Pengajuan</h1>
        <p>Masukkan nomor e-ticket untuk melihat perkembangan pengajuan layanan Anda.</p>
        <form class="ticket-search" action="{{ route('cek-status') }}" method="GET" id="cekStatusForm">
            <i class="bi bi-search ticket-search-icon" aria-hidden="true"></i>
            <label class="visually-hidden" for="ticket">Nomor e-ticket</label>
            <div class="ticket-input-wrap">
                <small>NOMOR E-TICKET</small>
                <input id="ticket" name="ticket" type="text" value="{{ $ticket }}" placeholder="Contoh: ULT-2026-000123" autocomplete="off" required maxlength="50">
            </div>
            <button class="btn status-button" type="submit"><i class="bi bi-search me-1"></i> Cek Status</button>
        </form>
        @if ($error)
            <div class="status-alert" role="alert"><i class="bi bi-exclamation-circle me-2"></i>{{ $error }}</div>
        @endif
    </section>

    @if ($pengajuan)
        @php
            $statusLabels = [
                'diajukan' => 'Pengajuan Diterima',
                'diverifikasi' => 'Verifikasi Data',
                'diproses' => 'Sedang Diproses',
                'didisposisikan' => 'Diteruskan ke Unit Tujuan',
                'selesai' => 'Selesai',
                'ditolak' => 'Pengajuan Ditolak',
            ];
            $statusKey = strtolower((string) $pengajuan->status);
            $statusLabel = $statusLabels[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey));
            $statusOrder = ['diajukan', 'diverifikasi', 'diproses', 'didisposisikan', 'selesai'];
            $currentStep = array_search($statusKey, $statusOrder, true);
            if ($currentStep === false) $currentStep = in_array($statusKey, ['ditolak'], true) ? 1 : 0;
        @endphp
        <section class="status-results container-xl" aria-live="polite">
            <article class="info-card">
                <h2><i class="bi bi-file-earmark-text"></i> Informasi Pengajuan</h2>
                <dl class="application-details">
                    <div><dt>Nomor Ticket</dt><dd class="ticket-code">{{ $pengajuan->nomor_tiket }}</dd></div>
                    <div><dt>Nama Pemohon</dt><dd>{{ $pengajuan->nama_pemohon ?? 'Pemohon' }}</dd></div>
                    <div><dt>Layanan</dt><dd>{{ $pengajuan->nama_layanan ?? 'Layanan' }}</dd></div>
                    <div><dt>Unit Tujuan</dt><dd>{{ $pengajuan->nama_unit ?? 'Belum ditentukan' }}</dd></div>
                    <div><dt>Subjek Pengajuan</dt><dd>{{ $pengajuan->subjek }}</dd></div>
                    <div><dt>Tanggal Pengajuan</dt><dd>{{ $pengajuan->dibuat_pada ? \Carbon\Carbon::parse($pengajuan->dibuat_pada)->translatedFormat('d F Y • H:i') . ' WIB' : '-' }}</dd></div>
                    @if ($pengajuan->tanggal_selesai)
                        <div><dt>Tanggal Selesai</dt><dd>{{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->translatedFormat('d F Y • H:i') }} WIB</dd></div>
                    @endif
                </dl>
                <div class="current-status"><i class="bi bi-circle-fill"></i>{{ $statusLabel }}</div>
            </article>

            <article class="history-card">
                <h2><i class="bi bi-clock-history"></i> Riwayat Proses</h2>
                @if ($riwayat->isNotEmpty())
                    <ol class="timeline">
                        @foreach ($riwayat as $item)
                            <li class="timeline-item {{ $loop->last ? 'is-current' : 'is-done' }}">
                                <span class="timeline-marker">@if (!$loop->last)<i class="bi bi-check-lg"></i>@else<i class="bi bi-circle-fill"></i>@endif</span>
                                <div class="timeline-content">
                                    <h3>{{ $item->judul_status }}</h3>
                                    <p>{{ $item->deskripsi }}</p>
                                    <small>{{ $item->dibuat_pada ? \Carbon\Carbon::parse($item->dibuat_pada)->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}@if($item->nama_aktor) · {{ $item->nama_aktor }}@endif</small>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <ol class="timeline">
                        <li class="timeline-item is-current"><span class="timeline-marker"><i class="bi bi-circle-fill"></i></span><div class="timeline-content"><h3>{{ $statusLabel }}</h3><p>Pengajuan tercatat di sistem. Riwayat detail akan tampil setelah petugas memperbarui proses pengajuan.</p><small>{{ $pengajuan->dibuat_pada ? \Carbon\Carbon::parse($pengajuan->dibuat_pada)->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}</small></div></li>
                    </ol>
                @endif
            </article>
        </section>

        @if ($pesan->isNotEmpty())
            <section class="messages-card container-xl">
                <h2><i class="bi bi-chat-left-text"></i> Pesan dan Tanggapan</h2>
                <p class="messages-help">Tanggapan petugas terkait pengajuan ini ditampilkan di sini.</p>
                @foreach ($pesan as $item)
                    <div class="message-item">
                        <div class="message-meta"><strong>{{ strtolower($item->pengirim_tipe ?? '') === 'admin' ? 'Petugas ULT' : 'Pemohon' }}</strong><small>{{ $item->dibuat_pada ? \Carbon\Carbon::parse($item->dibuat_pada)->translatedFormat('d M Y, H:i') . ' WIB' : '' }}</small></div>
                        <p>{{ $item->pesan }}</p>
                    </div>
                @endforeach
            </section>
        @endif
    @elseif (!$ticket)
        <section class="status-empty container-xl"><i class="bi bi-ticket-perforated"></i><h2>Periksa perkembangan pengajuan Anda</h2><p>Masukkan nomor e-ticket yang Anda terima setelah mengajukan layanan. Informasi status dan riwayat proses akan muncul di halaman ini.</p></section>
    @endif
</main>

<footer class="status-footer">
    <div class="container-xl">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 footer-about"><a class="status-brand footer-brand" href="{{ route('beranda') }}"><img src="{{ asset('images/logo polinema.png') }}" width="32" height="32" alt=""><span><strong>UNIT LAYANAN TERPADU</strong><small>Politeknik Negeri Malang</small></span></a><p>Portal layanan publik terintegrasi untuk memudahkan akses informasi dan pengajuan layanan bagi mahasiswa, alumni, dosen, tenaga kependidikan, dan masyarakat umum.</p></div>
            <div class="col-6 col-lg-3"><h3>Tautan Cepat</h3><a href="{{ route('beranda') }}">Beranda</a><a href="{{ route('beranda') }}#layanan">Informasi Layanan</a><a href="{{ route('beranda') }}#faq">FAQ</a><a href="{{ route('cek-status') }}">Cek E-Tiket</a></div>
            <div class="col-6 col-lg-2"><h3>Unit Layanan</h3><span>Akademik</span><span>Keuangan</span><span>Kemahasiswaan</span><span>Kepegawaian</span><span>Humas &amp; Kerja Sama</span></div>
            <div class="col-lg-3 col-md-6"><h3>Kontak</h3><p><i class="bi bi-geo-alt-fill"></i> Jl. Soekarno Hatta No. 9, Malang</p><p><i class="bi bi-envelope-fill"></i> ult@polinema.ac.id</p><p><i class="bi bi-telephone-fill"></i> (0341) 404424</p></div>
        </div>
        <div class="footer-bottom">© {{ date('Y') }} Politeknik Negeri Malang — Unit Layanan Terpadu. All Rights Reserved.</div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
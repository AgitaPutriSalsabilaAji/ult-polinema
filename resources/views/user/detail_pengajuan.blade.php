<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengajuan {{ $pengajuan->nomor_tiket }} | ULT Polinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/detail_pengajuan.css', 'resources/js/detail_pengajuan.js'])
</head>
<body>
    @include('user.navbar', ['theme' => 'purple', 'active' => 'ticket'])

    <main class="detail-pengajuan-page">
        @if (session('success'))
            <div class="container-xl mt-4">
                <div class="alert alert-success" role="alert">
                    <strong>Berhasil!</strong> {{ session('success') }}
                </div>
            </div>
        @endif

        <section class="detail-pengajuan-header">
            <div class="container-xl">
                <div class="breadcrumb">
                    <a href="{{ route('beranda') }}">Beranda</a>
                    <span>/</span>
                    <a href="{{ route('cek-status') }}">E-Tiket</a>
                    <span>/</span>
                    <span>{{ $pengajuan->nomor_tiket }}</span>
                </div>
                <div class="header-row">
                    <div>
                        <span class="eyebrow">Detail Pengajuan</span>
                        <h1>{{ $pengajuan->nomor_tiket }}</h1>
                    </div>
                    <span class="status-status status-{{ Str::slug($pengajuan->status) }}">{{ ucfirst(str_replace('_', ' ', $pengajuan->status)) }}</span>
                </div>
            </div>
        </section>

        <section class="detail-pengajuan-shell">
            <div class="container-xl">
                <div class="detail-grid">
                    <div class="detail-card main-card">
                        <div class="card-section">
                            <h2>Informasi Pengajuan</h2>
                            <div class="info-table">
                                <div class="info-row"><span>Subjek</span><strong>{{ $pengajuan->subjek }}</strong></div>
                                <div class="info-row"><span>Layanan</span><strong>{{ $pengajuan->layanan?->nama_layanan ?? '-' }}</strong></div>
                                <div class="info-row"><span>Unit</span><strong>{{ $pengajuan->layanan?->unit?->nama_unit ?? '-' }}</strong></div>
                                <div class="info-row"><span>Nama Pemohon</span><strong>{{ $pemohon?->nama_lengkap ?? '-' }}</strong></div>
                                <div class="info-row"><span>Email</span><strong>{{ $pemohon?->email ?? '-' }}</strong></div>
                                <div class="info-row"><span>Telepon</span><strong>{{ $pemohon?->nomor_telepon ?? '-' }}</strong></div>
                                <div class="info-row"><span>Tanggal Pengajuan</span><strong>{{ $pengajuan->dibuat_pada?->translatedFormat('d F Y, H:i') ?? '-' }}</strong></div>
                            </div>
                        </div>

                        <div class="card-section">
                            <h2>Deskripsi Permohonan</h2>
                            <p class="deskripsi-box">{{ $pengajuan->deskripsi }}</p>
                        </div>
                    </div>

                    <aside class="detail-card side-card">
                        <h2>Ringkasan</h2>
                        <div class="summary-box">
                            <div class="summary-item">
                                <span>Status</span>
                                <strong>{{ ucfirst(str_replace('_', ' ', $pengajuan->status)) }}</strong>
                            </div>
                            <div class="summary-item">
                                <span>Nomor E-Tiket</span>
                                <strong>{{ $pengajuan->nomor_tiket }}</strong>
                            </div>
                            <div class="summary-item">
                                <span>Estimasi</span>
                                <strong>{{ $pengajuan->layanan?->estimasi_waktu ?? '1–3 Hari Kerja' }}</strong>
                            </div>
                        </div>

                        <div class="actions">
                            <a href="{{ route('cek-status') }}" class="btn btn-secondary">Cek Status Lainnya</a>
                            <a href="{{ route('layanan.index') }}" class="btn btn-primary">Kembali ke Layanan</a>
                        </div>
                    </aside>
                </div>

                @if ($pengajuan->riwayat->isNotEmpty())
                    <div class="timeline-card detail-card">
                        <h2>Riwayat Status</h2>
                        <div class="timeline">
                            @foreach ($pengajuan->riwayat->sortByDesc('dibuat_pada') as $history)
                                <div class="timeline-item">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <strong>{{ $history->judul_status }}</strong>
                                        <small>{{ $history->dibuat_pada?->translatedFormat('d F Y, H:i') ?? '-' }}</small>
                                        <p>{{ $history->deskripsi }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </main>

    @include('user.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

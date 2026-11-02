<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Berhasil | ULT Polinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/pengajuan_berhasil.css'])
</head>
<body>
    <div class="layanan-page">
        @include('user.navbar', ['theme' => 'purple', 'active' => 'ticket'])

        @php
            $namaLayanan = $pengajuan->layanan?->nama_layanan ?? 'Layanan';
            $namaUnit = $pengajuan->layanan?->unit?->nama_unit ?? 'Unit Layanan';
            $statusLabel = [
                'diajukan' => 'Diajukan',
                'diverifikasi' => 'Diverifikasi',
                'diproses' => 'Diproses',
                'didisposisikan' => 'Didisposisikan',
                'selesai' => 'Selesai',
                'ditolak' => 'Ditolak',
            ][$pengajuan->status ?? 'diajukan'] ?? ucfirst(str_replace('_', ' ', (string) ($pengajuan->status ?? 'diajukan')));
        @endphp

        <main class="success-page form-pengajuan-page">
            <section class="form-pengajuan-header success-header">
                <div class="container-xl">
                    <h1>FORM PENGAJUAN LAYANAN</h1>
                    <p>Lengkapi formulir berikut dengan data yang valid untuk mengajukan permohonan ke Unit Bagian {{ $namaUnit }} Polinema.</p>
                </div>
            </section>

            <section class="success-notification container-xl">
                <h2>PENGAJUAN BERHASIL DIKIRIM</h2>
                <div class="success-badge" aria-hidden="true">
                    <svg viewBox="0 0 24 24" role="img" aria-label="Sukses">
                        <path d="M9.55 15.6 5.8 11.85l-1.4 1.4 5.15 5.15 9.8-9.8-1.4-1.4z" fill="currentColor"></path>
                    </svg>
                </div>
            </section>

            <section class="ticket-card-wrap container-xl">
                <article class="ticket-card">
                    <header class="ticket-card__header">
                        <div class="ticket-card__brand">
                            <div class="ticket-card__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <path d="M6 4h12a2 2 0 0 1 2 2v3.5a2.5 2.5 0 0 0-2.5 2.5A2.5 2.5 0 0 0 20 12.5V16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-3.5A2.5 2.5 0 0 0 6.5 9 2.5 2.5 0 0 0 4 6.5V6a2 2 0 0 1 2-2zm14 6.5h-2.5a4.5 4.5 0 0 1-9 0H4v3.5a.5.5 0 0 0 .5.5h15a.5.5 0 0 0 .5-.5V10.5z" fill="currentColor"></path>
                                </svg>
                            </div>
                            <div>
                                <span class="ticket-card__eyebrow">E-TICKET RESMI</span>
                                <strong>UNIT LAYANAN TERPADU POLINEMA</strong>
                            </div>
                        </div>
                        <span class="ticket-card__status">{{ $statusLabel }}</span>
                    </header>

                    <div class="ticket-card__body">
                        <div class="ticket-code-block">
                            <span class="ticket-label">NOMOR E-TICKET ANDA</span>
                            <strong>{{ $pengajuan->nomor_tiket }}</strong>
                            <small>Gunakan nomor tiket ini untuk memantau status secara langsung tanpa login.</small>
                        </div>

                        <div class="ticket-detail-grid">
                            <div class="ticket-detail-item">
                                <span>Nama Pemohon</span>
                                <strong>{{ $pemohon?->nama_lengkap ?? '-' }}</strong>
                            </div>
                            <div class="ticket-detail-item">
                                <span>NIM/NIP</span>
                                <strong>{{ $pemohon?->nim_nip ?? '-' }}</strong>
                            </div>
                            <div class="ticket-detail-item">
                                <span>Layanan</span>
                                <strong>{{ $namaLayanan }}</strong>
                            </div>
                            <div class="ticket-detail-item">
                                <span>Unit Pengampu</span>
                                <strong>{{ $namaUnit }}</strong>
                            </div>
                            <div class="ticket-detail-item">
                                <span>Tanggal Pengajuan</span>
                                <strong>{{ $pengajuan->dibuat_pada?->translatedFormat('d F Y') ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>

                    <footer class="ticket-card__footer">
                        <div class="ticket-card__seal">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 2 5 5v6c0 4.5 2.95 8.65 7 10 4.05-1.35 7-5.5 7-10V5zm0 5.2 2.73 2.67-1.1 3.43-2.63-1.4-2.63 1.4-1.1-3.43L12 7.2zm-5 7.8V7.8l.9-.5v7.2c0 3.53 2.08 6.56 5.1 7.73-.9-.2-1.73-.58-2.52-1.1-.42-.28-.8-.63-1.13-1.03A8.7 8.7 0 0 1 7 15zm10 0c0 1.1-.25 2.14-.7 3.07a8.03 8.03 0 0 1-1.13 1.03c-.79.52-1.62.9-2.52 1.1 3.02-1.17 5.1-4.2 5.1-7.73z" fill="currentColor"></path>
                            </svg>
                        </div>
                        <span>Dokumen yang diunggah telah tercatat dalam sistem Unit Layanan Terpadu Polinema.</span>
                    </footer>

                    <div class="ticket-card__actions">
                        <a href="{{ route('cek-status', ['ticket' => $pengajuan->nomor_tiket]) }}" class="btn btn-primary">Pantau Status</a>
                        <a href="{{ route('beranda') }}" class="btn btn-secondary">Kembali ke Beranda</a>
                    </div>
                </article>
            </section>
        </main>

        @include('user.footer')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

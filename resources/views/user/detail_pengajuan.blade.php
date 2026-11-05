<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengajuan {{ $pengajuan->nomor_tiket }} | ULT Polinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/detail_pengajuan.css', 'resources/js/detail_pengajuan.js'])
</head>
<body>
    @php
        $statusLabels = [
            'diajukan' => 'Diajukan',
            'diverifikasi' => 'Diverifikasi',
            'diproses' => 'Diproses',
            'didisposisikan' => 'Didisposisikan',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];
        $statusKey = strtolower((string) $pengajuan->status);
        $statusLabel = $statusLabels[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey));
        $unitName = $pengajuan->layanan?->unit?->nama_unit ?? '-';
        $serviceName = $pengajuan->layanan?->nama_layanan ?? '-';
    @endphp

    @include('user.navbar', ['theme' => 'purple', 'active' => 'services', 'showServiceCta' => true])

    <main class="detail-pengajuan-page">
        <header class="detail-pengajuan-header">
            <div class="container-xl">
                <h1>DETAIL PENGAJUAN LAYANAN</h1>
                <p>Lihat informasi pengajuan, status proses, dan komunikasi terkait layanan Anda.</p>
            </div>
        </header>

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

        <div class="detail-pengajuan-content">
            <div class="container-xl dp-content-container">
                <div class="detail-pengajuan-layout {{ $ratingUlasan ? 'detail-pengajuan-layout--single' : 'detail-pengajuan-layout--with-rating' }}">
                    <div class="dp-main-column">
                        @if (session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
                        <section class="dp-ticket-summary" aria-label="Ringkasan E-Ticket">
                    <div class="dp-ticket-summary__top"><div class="dp-ticket-summary__number"><span class="dp-ticket-summary__icon"><i class="bi bi-ticket-perforated-fill" aria-hidden="true"></i></span><div><span class="dp-eyebrow">Nomor E-Ticket</span><strong>{{ $pengajuan->nomor_tiket }}</strong></div></div><span class="dp-status dp-status--{{ \Illuminate\Support\Str::slug($statusKey) }}"><i class="bi bi-circle-fill"></i>{{ $statusLabel }}</span></div>
                    <div class="dp-ticket-summary__meta"><div><span>Tanggal Pengajuan</span><strong>{{ $pengajuan->dibuat_pada?->translatedFormat('d F Y') ?? '-' }}</strong></div><div><span>Unit Pengampu</span><strong>{{ $pengajuan->layanan?->unit?->nama_unit ?? '-' }}</strong></div><div><span>Layanan yang Diajukan</span><strong>{{ $pengajuan->layanan?->nama_layanan ?? '-' }}</strong></div></div>
                    <p class="dp-ticket-summary__hint"><i class="bi bi-info-circle"></i>Gunakan nomor E-Ticket ini untuk memantau status pengajuan setelah login ke akun Anda.</p>
                </section>
                <section class="dp-card" aria-labelledby="dp-applicant-title"><header class="dp-card__header"><h2 id="dp-applicant-title">01. DATA PEMOHON</h2><p>Informasi data pemohon yang digunakan pada pengajuan ini.</p></header><div class="dp-field-grid">
                    <div class="dp-field"><span>Nama Lengkap</span><div>{{ $pemohon?->nama_lengkap ?? '-' }}</div></div><div class="dp-field"><span>Jenis Pemohon</span><div>{{ $jenisPemohon ?? 'Belum tercatat' }}</div></div>
                    <div class="dp-field"><span>NIM / NIP</span><div>{{ $pemohon?->nim_nip ?? '-' }}</div></div><div class="dp-field"><span>Email Aktif</span><div>{{ $pemohon?->email ?? '-' }}</div></div>
                    <div class="dp-field dp-field--full"><span>Nomor WhatsApp</span><div>{{ $pemohon?->nomor_telepon ?? '-' }}</div></div>
                </div></section>
                <section class="dp-card" aria-labelledby="dp-service-title"><header class="dp-card__header"><h2 id="dp-service-title">02. DETAIL LAYANAN</h2><p>Detail permohonan layanan publik yang diajukan ke unit pengampu.</p></header><div class="dp-field-grid">
                    <div class="dp-field"><span>Unit Layanan</span><div>{{ $pengajuan->layanan?->unit?->nama_unit ?? '-' }}</div></div><div class="dp-field"><span>Layanan Terpilih</span><div>{{ $pengajuan->layanan?->nama_layanan ?? '-' }}</div></div>
                    <div class="dp-field dp-field--full"><span>Subjek / Keperluan</span><div>{{ $pengajuan->subjek }}</div></div><div class="dp-field dp-field--full"><span>Deskripsi Pengajuan</span><div class="dp-field__multiline">{{ $pengajuan->deskripsi }}</div></div>
                </div></section>
                <section class="dp-card" aria-labelledby="dp-documents-title"><header class="dp-card__header"><h2 id="dp-documents-title">03. DOKUMEN PENDUKUNG (Opsional)</h2><p>Lampiran berkas atau dokumen yang diunggah sebagai persyaratan administratif layanan.</p></header>
                    @forelse ($dokumen as $document)<div class="dp-document"><span class="dp-document__icon"><i class="bi bi-file-earmark-lock2"></i></span><div class="dp-document__info"><strong>{{ $document->nama_file }}</strong><small>{{ $document->ukuran_label }} • Berhasil Diunggah</small></div><div class="dp-document__actions"><a class="dp-button dp-button--outline" href="{{ route('pengajuan.dokumen.view', ['nomorTiket' => $pengajuan->nomor_tiket, 'dokumenId' => $document->id]) }}" target="_blank" rel="noopener noreferrer"><i class="bi bi-eye-fill"></i>Lihat Dokumen</a><a class="dp-button dp-button--purple" href="{{ route('pengajuan.dokumen.download', ['nomorTiket' => $pengajuan->nomor_tiket, 'dokumenId' => $document->id]) }}"><i class="bi bi-download"></i>Unduh</a></div></div>
                    @empty<p class="dp-empty-state">Belum ada dokumen yang diunggah untuk pengajuan ini.</p>@endforelse
                </section>
                <section class="dp-card" id="komunikasi" aria-labelledby="dp-communication-title"><header class="dp-card__header"><h2 id="dp-communication-title">04. KOMUNIKASI TERPADU PENGAJUAN</h2><p>Pesan pada halaman ini terkait langsung dengan pengajuan layanan Anda. Gunakan fitur ini untuk koordinasi dengan petugas.</p></header>
                    <div class="dp-communication-list" aria-live="polite">@forelse ($komunikasi as $item)
                        @if ($item->type === 'status')<div class="dp-status-event"><i class="bi bi-gear-fill"></i><span>{{ $item->judul }}@if ($item->deskripsi) — {{ $item->deskripsi }}@endif</span><time>{{ $item->dibuat_pada instanceof \Carbon\Carbon ? $item->dibuat_pada->translatedFormat('d F Y, H:i') : ($item->dibuat_pada ? \Carbon\Carbon::parse($item->dibuat_pada)->translatedFormat('d F Y, H:i') : '-') }}</time></div>
                        @else<article class="dp-message {{ $item->pengirim_tipe === 'admin' ? 'dp-message--officer' : 'dp-message--applicant' }}"><header class="dp-message__meta"><div><strong>{{ $item->nama_pengirim }}</strong><span class="dp-role-badge {{ $item->pengirim_tipe === 'admin' ? 'dp-role-badge--officer' : 'dp-role-badge--applicant' }}">{{ $item->pengirim_tipe === 'admin' ? 'PETUGAS' : 'PEMOHON' }}</span></div><time>{{ $item->dibuat_pada instanceof \Carbon\Carbon ? $item->dibuat_pada->translatedFormat('d F Y, H:i') : ($item->dibuat_pada ? \Carbon\Carbon::parse($item->dibuat_pada)->translatedFormat('d F Y, H:i') : '-') }}</time></header><p>{{ $item->pesan }}</p>@if ($item->lampiran_route)<a class="dp-button dp-button--outline dp-message-attachment-toggle" href="{{ $item->lampiran_route }}" target="_blank" rel="noopener noreferrer"><i class="bi bi-eye-fill"></i>Lihat Dokumen</a>@endif</article>@endif
                    @empty<p class="dp-empty-state">Belum ada riwayat komunikasi untuk pengajuan ini.</p>@endforelse</div>
                    <form class="dp-message-form" action="{{ route('pengajuan.pesan.store', ['nomorTiket' => $pengajuan->nomor_tiket]) }}" method="POST" enctype="multipart/form-data">@csrf
                        <label class="visually-hidden" for="dp-message-input">Pesan kepada petugas layanan</label><textarea id="dp-message-input" name="pesan" rows="3" maxlength="5000" placeholder="Tulis pesan kepada petugas layanan..." required>{{ old('pesan') }}</textarea>
                        @error('pesan')<small class="dp-form-error">{{ $message }}</small>@enderror @error('lampiran')<small class="dp-form-error">{{ $message }}</small>@enderror
                        <div class="dp-message-form__actions"><div><input class="dp-file-input" id="dp-message-file" type="file" name="lampiran" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"><label class="dp-button dp-button--outline" for="dp-message-file"><i class="bi bi-paperclip"></i>Lampirkan File</label><small class="dp-file-name" data-file-name>PDF, JPG, PNG, DOC, DOCX; maks. 5 MB.</small></div><button class="dp-button dp-button--send" type="submit"><i class="bi bi-send-fill"></i>Kirim Pesan</button></div>
                        <small class="dp-form-note">Pesan Anda akan tercatat dalam riwayat komunikasi pengajuan secara resmi.</small>
                    </form>
                </section>
                <section class="dp-ticket-actions" aria-label="Aksi E-Ticket"><div class="dp-ticket-actions__top"><div><span>Nomor E-Ticket Anda</span><strong>{{ $pengajuan->nomor_tiket }}</strong></div><button class="dp-button dp-button--outline" type="button" data-copy-ticket="{{ $pengajuan->nomor_tiket }}"><i class="bi bi-copy"></i>Salin E-Ticket</button></div><div class="dp-ticket-actions__bottom"><a href="{{ route('cek-status') }}"><i class="bi bi-arrow-left"></i>Kembali ke Cek Status</a><a class="dp-button dp-button--purple" href="{{ route('cek-status', ['ticket' => $pengajuan->nomor_tiket]) }}"><i class="bi bi-arrow-repeat"></i>Pantau Status Terbaru</a></div><span class="dp-copy-feedback" role="status" aria-live="polite" data-copy-feedback></span></section>
                    </div>
                    @if (! $ratingUlasan)
                        <aside class="dp-sidebar-column">
                            @include('user.partials.rating_layanan')
                        </aside>
                    @endif
                </div>
            </div>
        </div>

    </main>

    @include('user.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

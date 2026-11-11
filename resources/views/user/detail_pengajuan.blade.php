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
            'baru' => 'Baru',
            'diajukan' => 'Diajukan',
            'diverifikasi' => 'Diverifikasi',
            'diproses' => 'Diproses',
            'didisposisikan' => 'Didisposisikan',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];
        $historyIcons = [
            'baru' => 'bi bi-inbox-fill',
            'diajukan' => 'bi bi-send-fill',
            'perlu_perbaikan' => 'bi bi-pencil-square',
            'perlu_diperbaiki' => 'bi bi-pencil-square',
            'diverifikasi' => 'bi bi-patch-check-fill',
            'disetujui' => 'bi bi-patch-check-fill',
            'acc' => 'bi bi-patch-check-fill',
            'didisposisikan' => 'bi bi-arrow-left-right',
            'diproses' => 'bi bi-gear-fill',
            'selesai' => 'bi bi-check2-circle',
            'ditolak' => 'bi bi-x-circle-fill',
        ];
        $statusKey = strtolower((string) $pengajuan->status);
        $statusLabel = $statusLabels[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey));
    @endphp

    @include('user.navbar', ['theme' => 'purple', 'active' => 'services', 'showServiceCta' => true])

    <main class="detail-pengajuan-page">
        <header class="detail-pengajuan-header">
            <div class="container-xl">
                <h1>DETAIL PENGAJUAN LAYANAN</h1>
                <p>Lihat informasi pengajuan, dokumen, riwayat proses, dan komunikasi terkait layanan Anda.</p>
            </div>
        </header>

        <div class="detail-pengajuan-content">
            <div class="container-xl dp-content-container">
                <div class="detail-pengajuan-layout {{ $ratingUlasan ? 'detail-pengajuan-layout--single' : 'detail-pengajuan-layout--with-rating' }}">
                    <div class="dp-main-column">
                        @if (session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
                        <section class="dp-card" aria-labelledby="dp-information-title">
                            <header class="dp-card__header">
                                <h2 id="dp-information-title">01. INFORMASI PENGAJUAN</h2>
                                <p>Informasi dan rincian pengajuan layanan Anda.</p>
                            </header>
                            <div class="dp-ticket-summary__top">
                                <div class="dp-ticket-summary__number">
                                    <span class="dp-ticket-summary__icon"><i class="bi bi-ticket-perforated-fill" aria-hidden="true"></i></span>
                                    <div>
                                        <span class="dp-eyebrow">Nomor E-Tiket</span>
                                        <strong>{{ $pengajuan->nomor_tiket }}</strong>
                                    </div>
                                </div>
                                <span class="dp-status dp-status--{{ \Illuminate\Support\Str::slug($statusKey) }}">
                                    <i class="bi bi-circle-fill" aria-hidden="true"></i>{{ $statusLabel }}
                                </span>
                            </div>
                            <div class="dp-ticket-summary__meta">
                                <div><span>Tanggal Pengajuan</span><strong>{{ $pengajuan->dibuat_pada?->locale('id')->translatedFormat('d F Y') ?? '-' }}</strong></div>
                                <div><span>Unit Pengampu</span><strong>{{ $pengajuan->layanan?->unit?->nama_unit ?? '-' }}</strong></div>
                                <div><span>Layanan yang Diajukan</span><strong>{{ $pengajuan->layanan?->nama_layanan ?? '-' }}</strong></div>
                            </div>
                            <div class="dp-field-grid dp-information-details">
                                <div class="dp-field dp-field--full"><span>Subjek / Keperluan</span><div>{{ $pengajuan->subjek }}</div></div>
                                <div class="dp-field dp-field--full"><span>Deskripsi Pengajuan</span><div class="dp-field__multiline">{{ $pengajuan->deskripsi }}</div></div>
                            </div>
                        </section>

                        <section class="dp-card" aria-labelledby="dp-documents-title">
                            <header class="dp-card__header">
                                <h2 id="dp-documents-title">02. DOKUMEN PENDUKUNG (Opsional)</h2>
                                <p>Lampiran berkas atau dokumen yang diunggah sebagai persyaratan administratif layanan.</p>
                            </header>
                            <div class="dp-document-list">
                                @forelse ($dokumen as $document)
                                    <div class="dp-document">
                                        <span class="dp-document__icon"><i class="bi bi-file-earmark-lock2" aria-hidden="true"></i></span>
                                        <div class="dp-document__info">
                                            <strong>{{ $document->nama_file }}</strong>
                                            <small>{{ $document->ukuran_label }} • Berhasil Diunggah</small>
                                        </div>
                                        <div class="dp-document__actions">
                                            <a class="dp-button dp-button--outline" href="{{ route('pengajuan.dokumen.view', ['nomorTiket' => $pengajuan->nomor_tiket, 'dokumenId' => $document->id]) }}" target="_blank" rel="noopener noreferrer">
                                                <i class="bi bi-eye-fill" aria-hidden="true"></i>Lihat Dokumen
                                            </a>
                                            <a class="dp-button dp-button--purple" href="{{ route('pengajuan.dokumen.download', ['nomorTiket' => $pengajuan->nomor_tiket, 'dokumenId' => $document->id]) }}">
                                                <i class="bi bi-download" aria-hidden="true"></i>Unduh
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <p class="dp-empty-state">Tidak ada dokumen pendukung yang dilampirkan.</p>
                                @endforelse
                            </div>
                        </section>

                        <section class="dp-card" aria-labelledby="dp-history-title">
                            <header class="dp-card__header">
                                <h2 id="dp-history-title">03. RIWAYAT PROSES</h2>
                                <p>Riwayat perkembangan pengajuan berdasarkan aktivitas yang tercatat.</p>
                            </header>
                            <div class="timeline">
                                @forelse ($pengajuan->riwayat as $history)
                                    @php
                                        $historyStatusKey = strtolower(trim((string) $history->status_baru));
                                        $historyIcon = $historyIcons[$historyStatusKey] ?? 'bi bi-arrow-repeat';
                                    @endphp
                                    <article class="timeline-item timeline-item--{{ \Illuminate\Support\Str::slug($historyStatusKey) }} {{ $loop->last ? 'is-latest' : 'is-complete' }}">
                                        <div class="timeline-marker" aria-hidden="true">
                                            <i class="{{ $historyIcon }}"></i>
                                            @if (! $loop->last)
                                                <span class="timeline-marker__check"><i class="bi bi-check-lg"></i></span>
                                            @endif
                                        </div>
                                        <div class="timeline-content">
                                            <div class="timeline-content__meta">
                                                <span class="timeline-step">Tahap {{ $loop->iteration }}</span>
                                                @if ($loop->last)
                                                    <span class="timeline-state timeline-state--latest">Proses terbaru</span>
                                                @else
                                                    <span class="timeline-state timeline-state--complete"><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Selesai dilalui</span>
                                                @endif
                                            </div>
                                            <strong>{{ strtolower(trim((string) $history->judul_status)) === 'pengajuan diterima' ? 'Pengajuan Masuk' : $history->judul_status }}</strong>
                                            <time datetime="{{ $history->dibuat_pada?->toIso8601String() ?? '' }}">
                                                {{ $history->dibuat_pada?->locale('id')->translatedFormat('d F Y · H:i') ?? '-' }}{{ $history->dibuat_pada ? ' WIB' : '' }}
                                            </time>
                                            <p>{{ $history->deskripsi }}</p>
                                        </div>
                                    </article>
                                @empty
                                    <p class="dp-empty-state">Belum ada riwayat proses untuk pengajuan ini.</p>
                                @endforelse
                            </div>
                        </section>

                        <section class="dp-card" id="komunikasi" aria-labelledby="dp-communication-title">
                            <header class="dp-card__header">
                                <h2 id="dp-communication-title">04. KOMUNIKASI DENGAN ADMIN</h2>
                            </header>
                            <div class="dp-communication-list" id="komunikasi-history" aria-live="polite">
                                @forelse ($komunikasi as $item)
                                    <article class="dp-message {{ $item->pengirim_tipe === 'admin' ? 'dp-message--officer' : 'dp-message--applicant' }}">
                                        <header class="dp-message__meta">
                                            <div>
                                                <strong>{{ $item->nama_pengirim }}</strong>
                                                <span class="dp-role-badge {{ $item->pengirim_tipe === 'admin' ? 'dp-role-badge--officer' : 'dp-role-badge--applicant' }}">{{ $item->pengirim_tipe === 'admin' ? 'PETUGAS' : 'PEMOHON' }}</span>
                                            </div>
                                            <time>{{ $item->dibuat_pada?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}</time>
                                        </header>
                                        <p>{{ $item->pesan }}</p>
                                        @if ($item->lampiran_route)
                                            <a class="dp-button dp-button--outline dp-message-attachment-toggle" href="{{ $item->lampiran_route }}" target="_blank" rel="noopener noreferrer">
                                                <i class="bi bi-eye-fill" aria-hidden="true"></i>Lihat Dokumen
                                            </a>
                                        @endif
                                    </article>
                                @empty
                                    <p class="dp-empty-state">Belum ada percakapan pada E-Tiket ini.</p>
                                @endforelse
                            </div>
                            @if ($chatTerbuka)
                                <form class="dp-message-form" id="komunikasi-form" action="{{ route('pengajuan.pesan.store', ['nomorTiket' => $pengajuan->nomor_tiket]) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <label class="visually-hidden" for="dp-message-input">Pesan kepada admin</label>
                                    <textarea id="dp-message-input" name="pesan" rows="3" maxlength="5000" placeholder="Tulis pesan kepada admin..." required>{{ old('pesan') }}</textarea>
                                    @error('pesan')<small class="dp-form-error">{{ $message }}</small>@enderror
                                    @error('lampiran')<small class="dp-form-error">{{ $message }}</small>@enderror
                                    <div class="dp-message-form__actions">
                                        <div>
                                            <input class="dp-file-input" id="dp-message-file" type="file" name="lampiran" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                            <label class="dp-button dp-button--outline" for="dp-message-file"><i class="bi bi-paperclip" aria-hidden="true"></i>Lampirkan File</label>
                                            <small class="dp-file-name" data-file-name>PDF, JPG, PNG, DOC, DOCX; maks. 5 MB.</small>
                                        </div>
                                        <button class="dp-button dp-button--send" type="submit"><i class="bi bi-send-fill" aria-hidden="true"></i>Kirim Pesan</button>
                                    </div>
                                    <small class="dp-form-note">Pesan Anda akan tercatat dalam riwayat komunikasi E-Tiket ini.</small>
                                </form>
                            @else
                                <p class="dp-chat-readonly" role="status">Pengajuan telah {{ strtolower($statusLabel) }}. Percakapan ini hanya dapat dibaca.</p>
                            @endif
                        </section>

                        <div class="dp-status-link-row">
                            <a class="dp-button dp-button--purple" href="{{ route('cek-status', ['ticket' => $pengajuan->nomor_tiket]) }}">
                                <i class="bi bi-arrow-repeat" aria-hidden="true"></i>Pantau Status
                            </a>
                        </div>
                    </div>

                    @if (! $ratingUlasan)
                        <aside class="dp-sidebar-column">
                            <div class="dp-rating-panel" aria-label="Rating dan ulasan layanan">
                                <div class="dp-rating-panel__header">
                                    <h3>Berikan penilaian untuk kami</h3>
                                    <p>Bantu kami untuk meningkatkan kualitas layanan</p>
                                </div>

                                <form
                                    class="dp-rating-form"
                                    action="{{ route('pengajuan.rating.store', ['nomorTiket' => $pengajuan->nomor_tiket]) }}"
                                    method="POST"
                                >
                                    @csrf

                                    <div class="dp-rating-stars" role="radiogroup" aria-label="Pilih rating layanan">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <label
                                                class="dp-star-option"
                                                data-value="{{ $i }}"
                                                data-tooltip="{{ $i }} Bintang"
                                                title="{{ $i }} Bintang"
                                                aria-label="{{ $i }} Bintang"
                                            >
                                                <input
                                                    type="radio"
                                                    name="rating"
                                                    value="{{ $i }}"
                                                    required
                                                    {{ old('rating') == (string) $i ? 'checked' : '' }}
                                                >
                                                <span aria-hidden="true">
                                                    <i class="bi bi-star-fill"></i>
                                                </span>
                                            </label>
                                        @endfor
                                    </div>

                                    @error('rating')
                                        <small class="dp-form-error">{{ $message }}</small>
                                    @enderror

                                    <label class="dp-rating-label" for="rating-komentar">
                                        Saran dan Pendapat <span class="text-danger">*</span>
                                    </label>

                                    <textarea
                                        id="rating-komentar"
                                        name="komentar"
                                        rows="4"
                                        maxlength="500"
                                        required
                                        placeholder="Tuliskan komentar atau saran Anda..."
                                    >{{ old('komentar') }}</textarea>

                                    <div class="dp-rating-counter">
                                        <small>Wajib diisi. Maksimal 500 karakter.</small>
                                        <small id="rating-character-count">0/500</small>
                                    </div>

                                    @error('komentar')
                                        <small class="dp-form-error">{{ $message }}</small>
                                    @enderror

                                    <button class="dp-button dp-button--purple" type="submit">
                                        Submit
                                    </button>
                                </form>
                            </div>
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

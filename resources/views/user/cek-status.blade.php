<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Tiket | Unit Layanan Terpadu Polinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/cek-status.css', 'resources/js/cek-status.js'])
</head>
<body class="status-page">
@include('user.navbar', ['theme' => 'purple', 'active' => 'ticket'])

<main class="status-main">
    @php
        $statusLabels = [
            'baru' => 'Menunggu Verifikasi',
            'diajukan' => 'Menunggu Verifikasi',
            'perlu_perbaikan' => 'Perlu Perbaikan',
            'perlu_diperbaiki' => 'Perlu Perbaikan',
            'disetujui' => 'Disetujui / ACC',
            'acc' => 'Disetujui / ACC',
            'diverifikasi' => 'Disetujui / ACC',
            'didisposisikan' => 'Didisposisikan',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];
        $statusDescriptions = [
            'baru' => 'Pengajuan menunggu verifikasi Admin ULT.',
            'diajukan' => 'Pengajuan menunggu verifikasi Admin ULT.',
            'perlu_perbaikan' => 'Pengajuan memerlukan perbaikan dari pemohon.',
            'perlu_diperbaiki' => 'Pengajuan memerlukan perbaikan dari pemohon.',
            'disetujui' => 'Pengajuan telah disetujui.',
            'acc' => 'Pengajuan telah disetujui.',
            'diverifikasi' => 'Pengajuan telah diverifikasi.',
            'didisposisikan' => 'Pengajuan diteruskan ke unit tujuan.',
            'diproses' => 'Pengajuan sedang ditangani oleh unit tujuan.',
            'selesai' => 'Pengajuan telah diselesaikan oleh unit tujuan.',
            'ditolak' => 'Pengajuan tidak dapat dilanjutkan.',
        ];
    @endphp

    <section class="status-intro container-xl">
        <div class="ticket-list-heading">
            <div>
                <h1>E-Tiket</h1>
                <p>Daftar E-Tiket dari pengajuan layanan yang telah Anda ajukan melalui Sistem Informasi ULT Polinema. Pantau perkembangan setiap pengajuan melalui detail E-Tiket.</p>
            </div>
        </div>

        @if ($error)
            <div class="status-alert" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ $error }}
            </div>
        @endif
    </section>

    @if ($pengajuans->isNotEmpty())
        <section class="ticket-list container-xl" aria-label="Daftar E-Tiket">
            @foreach ($pengajuans as $item)
                @php
                    $latestHistory = $item->riwayat->last();
                    $cardStatusKey = strtolower((string) ($latestHistory?->status_baru ?? $item->status));
                    $cardStatusLabel = $statusLabels[$cardStatusKey] ?? ucfirst(str_replace('_', ' ', $cardStatusKey));
                    $cardStatusDescription = $latestHistory?->deskripsi
                        ?: ($statusDescriptions[$cardStatusKey] ?? 'Pengajuan sedang dalam proses.');
                @endphp
                <article class="ticket-item">
                    <div class="ticket-item__header">
                        <strong class="ticket-item__number">{{ $item->nomor_tiket }}</strong>
                        <span class="ticket-status-badge ticket-status-badge--{{ \Illuminate\Support\Str::slug($cardStatusKey) }}">
                            {{ $cardStatusLabel }}
                        </span>
                    </div>
                    <p class="ticket-item__service">
                        {{ $item->layanan?->nama_layanan ?? 'Layanan' }}
                        <span aria-hidden="true">·</span>
                        {{ $item->layanan?->unit?->nama_unit ?? 'Unit belum ditentukan' }}
                    </p>
                    <p class="ticket-item__date">
                        {{ $item->dibuat_pada?->locale('id')->translatedFormat('d F Y') ?? '-' }}
                    </p>
                    <p class="ticket-item__description">{{ $cardStatusDescription }}</p>
                    <a class="ticket-item__detail" href="{{ route('pengajuan.detail', ['nomorTiket' => $item->nomor_tiket]) }}">
                        Lihat Detail <span aria-hidden="true">→</span>
                    </a>
                </article>
            @endforeach
        </section>
    @else
        <section class="status-empty container-xl">
            <i class="bi bi-ticket-perforated" aria-hidden="true"></i>
            <h2>Belum ada E-Tiket</h2>
            <p>Anda belum memiliki pengajuan layanan.</p>
        </section>
    @endif

    @if ($pengajuan)
        @php
            $latestTicketHistory = $riwayat->last();
            $statusKey = strtolower((string) ($latestTicketHistory?->status_baru ?? $pengajuan->status));
            $statusLabel = $statusLabels[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey));
        @endphp
        <section class="status-results container-xl" aria-live="polite">
            <article class="info-card">
                <h2><i class="bi bi-file-earmark-text"></i> Informasi Pengajuan</h2>
                <dl class="application-details">
                    <div><dt>Nomor E-Tiket</dt><dd class="ticket-code">{{ $pengajuan->nomor_tiket }}</dd></div>
                    <div><dt>Nama Pemohon</dt><dd>{{ $pemohon?->nama_lengkap ?? 'Pemohon' }}</dd></div>
                    <div><dt>Layanan</dt><dd>{{ $pengajuan->layanan?->nama_layanan ?? 'Layanan' }}</dd></div>
                    <div><dt>Unit Tujuan</dt><dd>{{ $pengajuan->layanan?->unit?->nama_unit ?? 'Belum ditentukan' }}</dd></div>
                    <div><dt>Subjek Pengajuan</dt><dd>{{ $pengajuan->subjek }}</dd></div>
                    <div><dt>Tanggal Pengajuan</dt><dd>{{ $pengajuan->dibuat_pada?->locale('id')->translatedFormat('d F Y • H:i') ?? '-' }}{{ $pengajuan->dibuat_pada ? ' WIB' : '' }}</dd></div>
                    @if ($pengajuan->tanggal_selesai)
                        <div><dt>Tanggal Selesai</dt><dd>{{ $pengajuan->tanggal_selesai?->locale('id')->translatedFormat('d F Y • H:i') }} WIB</dd></div>
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
                                    <h3>{{ strtolower(trim((string) $item->judul_status)) === 'pengajuan diterima' ? 'Pengajuan Masuk' : $item->judul_status }}</h3>
                                    <p>{{ $item->deskripsi }}</p>
                                    <small>
                                        {{ $item->dibuat_pada?->locale('id')->translatedFormat('d M Y, H:i') ?? '-' }} WIB
                                        @if ($item->nama_aktor)
                                            · {{ $item->nama_aktor }}
                                        @endif
                                    </small>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <ol class="timeline">
                        <li class="timeline-item is-current"><span class="timeline-marker"><i class="bi bi-circle-fill"></i></span><div class="timeline-content"><h3>{{ $statusLabel }}</h3><p>Pengajuan tercatat di sistem. Riwayat detail akan tampil setelah petugas memperbarui proses pengajuan.</p><small>{{ $pengajuan->dibuat_pada?->locale('id')->translatedFormat('d M Y, H:i') ?? '-' }} WIB</small></div></li>
                    </ol>
                @endif
            </article>
        </section>

    @endif
</main>

@include('user.footer')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
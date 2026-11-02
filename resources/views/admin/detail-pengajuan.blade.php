@extends('adminlte::page')

@section('title', 'Detail Pengajuan Pelayanan')

@section('content_header')
    <div class="detail-page-heading">
        <h1 class="admin-page-title mb-0">Data Pengajuan Pelayanan</h1>
    </div>
@stop

@section('content')
    <div class="detail-pengajuan-page">
        @if (session('success'))
            <div class="alert alert-success detail-alert" role="alert">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger detail-alert" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="detail-status-bar">
            <div class="detail-status-actions">
                @include('admin.partials.status-badge', ['status' => $pengajuan->status])
                @if (auth('admin')->user()?->peran === 'admin_unit' && $pengajuan->status === 'diproses')
                    <form method="POST" action="{{ route('admin.pengajuan.tolak', $pengajuan) }}">
                        @csrf
                        <button class="detail-action-button detail-action-button--reject" type="submit">
                            Tolak Pengajuan
                        </button>
                    </form>
                @endif
                @if (auth('admin')->user()?->peran === 'admin_unit' && $pengajuan->status === 'diproses')
                    <form method="POST" action="{{ route('admin.pengajuan.selesai', $pengajuan) }}">
                        @csrf
                        <button class="detail-action-button detail-action-button--complete" type="submit">
                            Tandai Selesai
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-8">
                <section class="detail-card detail-request-card">
                    <div class="detail-card-heading">
                        <span class="detail-section-number">3.</span>
                        <h2>Detail Permohonan Layanan</h2>
                    </div>
                    <div class="detail-request-grid">
                        <div class="detail-field">
                            <span>Kategori / Jenis Layanan</span>
                            <strong>{{ $pengajuan->layanan?->nama_layanan ?? '-' }}</strong>
                        </div>
                        <div class="detail-field">
                            <span>Layanan Tujuan</span>
                            <strong>{{ $pengajuan->layanan?->nama_layanan ?? '-' }}</strong>
                        </div>
                        <div class="detail-field">
                            <span>Unit Layanan</span>
                            <strong>{{ $pengajuan->layanan?->unit?->nama_unit ?? $pengajuan->unit?->nama_unit ?? '-' }}</strong>
                        </div>
                        <div class="detail-field">
                            <span>Nomor E-Tiket</span>
                            <strong class="text-primary">{{ $pengajuan->nomor_tiket }}</strong>
                        </div>
                        <div class="detail-field detail-field-wide">
                            <span>Subjek Pengajuan</span>
                            <strong>{{ $pengajuan->subjek ?: '-' }}</strong>
                        </div>
                    </div>
                    <div class="detail-description">
                        <span>Deskripsi Permohonan</span>
                        <p>{{ $pengajuan->deskripsi ?: '-' }}</p>
                    </div>
                </section>

                <section class="detail-card detail-documents-card">
                    <div class="detail-card-heading">
                        <span class="detail-section-number">4.</span>
                        <h2>Berkas Lampiran Pendukung</h2>
                    </div>
                    @forelse ($dokumenPengajuan as $dokumen)
                        <div class="detail-document">
                            <i class="bi bi-file-earmark-text detail-document-icon" aria-hidden="true"></i>
                            <div class="detail-document-info">
                                <strong>{{ $dokumen->nama_file }}</strong>
                                <span>PDF Document · {{ number_format(((int) $dokumen->ukuran_file) / 1024 / 1024, 2) }} MB</span>
                            </div>
                            <a class="btn btn-sm btn-outline-primary" href="{{ asset('storage/' . ltrim($dokumen->path_file, '/')) }}" target="_blank" rel="noopener">
                                <i class="bi bi-download" aria-hidden="true"></i> Unduh
                            </a>
                        </div>
                    @empty
                        <div class="detail-empty-document">
                            <i class="bi bi-paperclip" aria-hidden="true"></i>
                            <span>Tidak ada dokumen pendukung yang dilampirkan.</span>
                        </div>
                    @endforelse
                </section>

                <section class="detail-card detail-conversation-card">
                    <div class="detail-card-heading detail-conversation-heading">
                        <div>
                            <span class="detail-section-number">5.</span>
                            <h2>Komunikasi Pengajuan</h2>
                        </div>
                        <span class="detail-muted-label">Ruang komunikasi</span>
                    </div>
                    <p class="detail-helper-text">Komunikasi langsung dengan pemohon terkait pengajuan layanan ini.</p>
                    <div class="detail-conversation-meta">
                        <span>E-Ticket: <strong>{{ $pengajuan->nomor_tiket }}</strong></span>
                        <span>Pemohon: <strong>{{ $pemohon->nama_lengkap ?? 'Pemohon #' . $pengajuan->id_pengguna }}</strong></span>
                        <span>Unit Penanggung Jawab: <strong>{{ $pengajuan->unit?->nama_unit ?? '-' }}</strong></span>
                        @include('admin.partials.status-badge', ['status' => $pengajuan->status])
                    </div>
                    @if ($pesanPengajuan->isEmpty())
                        <div class="detail-chat-empty">
                            <i class="bi bi-chat-dots" aria-hidden="true"></i>
                            <strong>Belum ada percakapan</strong>
                            <span>Riwayat komunikasi dengan pemohon akan tampil di area ini.</span>
                        </div>
                    @else
                        <div class="detail-chat-history">
                            @foreach ($pesanPengajuan as $pesan)
                                <div class="detail-chat-message {{ strtolower($pesan->pengirim_tipe ?? '') === 'admin' ? 'detail-chat-message--outgoing' : 'detail-chat-message--incoming' }}">
                                    <div class="detail-chat-message-meta">
                                        <strong>{{ strtolower($pesan->pengirim_tipe ?? '') === 'admin' ? 'Petugas ULT' : 'Pemohon' }}</strong>
                                        <span>{{ $pesan->dibuat_pada ? \Carbon\Carbon::parse($pesan->dibuat_pada)->format('d M Y, H:i') : '-' }}</span>
                                    </div>
                                    <p>{{ $pesan->pesan }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="detail-chat-compose" aria-label="Placeholder penulisan pesan">
                        <input type="text" class="form-control" placeholder="Tulis pesan kepada pemohon..." disabled>
                        <button type="button" class="btn btn-primary" disabled>
                            <i class="bi bi-send" aria-hidden="true"></i> Kirim Pesan
                        </button>
                    </div>
                    <small class="detail-chat-note">
                        {{ $pengajuan->isTerminal() ? 'Tiket terminal. Riwayat komunikasi hanya dapat dibaca.' : 'Fitur komunikasi akan tersedia setelah integrasi percakapan selesai.' }}
                    </small>
                </section>
            </div>

            <div class="col-xl-4">
                <section class="detail-card detail-applicant-card">
                    <div class="detail-card-heading">
                        <span class="detail-section-number">1.</span>
                        <h2>Informasi Pemohon</h2>
                    </div>
                    <div class="detail-applicant-summary">
                        <span class="detail-applicant-avatar">{{ strtoupper(substr($pemohon->nama_lengkap ?? 'P', 0, 1)) }}</span>
                        <div>
                            <strong>{{ $pemohon->nama_lengkap ?? 'Pemohon #' . $pengajuan->id_pengguna }}</strong>
                            <span>Pemohon layanan</span>
                        </div>
                    </div>
                    <dl class="detail-definition-list">
                        <div><dt>NIM / NIP / NIK</dt><dd>{{ $pemohon->nim_nip ?? '-' }}</dd></div>
                        <div><dt>Nomor WhatsApp</dt><dd>{{ $pemohon->nomor_telepon ?? '-' }}</dd></div>
                        <div><dt>Email</dt><dd class="detail-breakable">{{ $pemohon->email ?? '-' }}</dd></div>
                        <div><dt>Tanggal Pengajuan</dt><dd>{{ $pengajuan->dibuat_pada?->format('d F Y, H:i') ?? '-' }}</dd></div>
                    </dl>
                </section>

                <section class="detail-card detail-service-card">
                    <div class="detail-card-heading">
                        <span class="detail-section-number">2.</span>
                        <h2>Informasi Layanan</h2>
                    </div>
                    <dl class="detail-definition-list">
                        <div><dt>Nama Layanan</dt><dd>{{ $pengajuan->layanan?->nama_layanan ?? '-' }}</dd></div>
                        <div><dt>Unit Penyelenggara</dt><dd>{{ $pengajuan->layanan?->unit?->nama_unit ?? $pengajuan->unit?->nama_unit ?? '-' }}</dd></div>
                        <div><dt>Estimasi Selesai</dt><dd>{{ $pengajuan->layanan?->estimasi_waktu ?? '-' }}</dd></div>
                    </dl>
                </section>
            </div>
        </div>

        <section class="detail-card detail-history-card">
            <div class="detail-card-heading"><h2>Riwayat Disposisi & Status</h2></div>
            <div class="table-responsive">
                <table class="table detail-history-table align-middle mb-0">
                    <thead><tr><th>Tanggal</th><th>Aktivitas</th><th>Unit / Admin</th><th>Status</th><th>Catatan</th></tr></thead>
                    <tbody>
                        @forelse ($pengajuan->riwayat->sortByDesc('dibuat_pada') as $item)
                            <tr>
                                <td>{{ $item->dibuat_pada?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td><strong>{{ $item->judul_status }}</strong><small>{{ $item->nama_aktor }}</small></td>
                                <td>{{ $pengajuan->unit?->nama_unit ?? '-' }}</td>
                                <td>@include('admin.partials.status-badge', ['status' => $item->status_baru])</td>
                                <td>{{ $item->deskripsi ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada riwayat status.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@stop

@section('css')
<style>
    .detail-pengajuan-page { --detail-border: #e2e8f0; --detail-muted: #64748b; color: #172033; }
    .detail-page-heading { display: flex; align-items: center; min-height: 31px; }
    .detail-alert { border-radius: 8px; }
    .detail-status-bar { display: flex; align-items: center; justify-content: flex-end; gap: 1rem; min-height: 42px; margin-bottom: 8px; padding: 6px 9px; border: 1px solid #e5e9ef; border-radius: 8px; background: #fff; box-shadow: 0 2px 6px rgba(15, 23, 42, .025); }
    .detail-status-actions { display: flex; align-items: center; gap: 6px; }
    .detail-status-actions form { margin: 0; }
    .detail-action-button { min-width: 77px; height: 24px; padding: 3px 9px; border: 1px solid transparent; border-radius: 5px; font-family: inherit; font-size: .59rem; font-weight: 600; line-height: 1; white-space: nowrap; }
    .detail-action-button--reject { border-color: #f1aeb5; background: #fff5f5; color: #c03d4d; }
    .detail-action-button--reject:hover { background: #ffe4e6; }
    .detail-action-button--complete { border-color: #92d3c4; background: #e9f8f3; color: #087c68; }
    .detail-action-button--complete:hover { background: #d3f1e7; }
    .detail-card { margin-bottom: 14px; padding: 14px; border: 1px solid var(--detail-border); border-radius: 8px; background: #fff; box-shadow: 0 2px 6px rgba(15, 23, 42, .025); }
    .detail-card-heading { display: flex; align-items: center; gap: 5px; margin-bottom: 13px; }
    .detail-card-heading h2 { margin: 0; color: #172033; font-size: .72rem; font-weight: 700; }
    .detail-section-number { color: #172033; font-size: .7rem; font-weight: 800; }
    .detail-request-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 13px 25px; }
    .detail-field { min-width: 0; }
    .detail-field-wide { grid-column: 1 / -1; }
    .detail-field span, .detail-description > span, .detail-definition-list dt { display: block; margin-bottom: 4px; color: #94a3b8; font-size: .63rem; font-weight: 500; }
    .detail-field strong, .detail-definition-list dd { color: #27344a; font-size: .73rem; font-weight: 600; }
    .detail-description { margin-top: 14px; }
    .detail-description p { min-height: 54px; margin: 0; padding: 9px 10px; border: 1px solid var(--detail-border); border-radius: 6px; background: #f8fafc; color: #475569; font-size: .66rem; line-height: 1.45; white-space: pre-wrap; }
    .detail-document { display: flex; align-items: center; gap: 10px; margin-top: 8px; padding: 9px 10px; border: 1px solid var(--detail-border); border-radius: 7px; background: #f8fafc; }
    .detail-document-icon { color: #d65b77; font-size: 1.05rem; }
    .detail-document-info { display: flex; min-width: 0; flex: 1; flex-direction: column; gap: 2px; }
    .detail-document-info strong { overflow: hidden; color: #334155; font-size: .7rem; text-overflow: ellipsis; white-space: nowrap; }
    .detail-document-info span { color: #94a3b8; font-size: .59rem; }
    .detail-document .btn { padding: .2rem .5rem; border-color: #dbe3ee; background: #fff; font-size: .62rem; }
    .detail-empty-document { display: flex; align-items: center; gap: 8px; color: #94a3b8; font-size: .7rem; }
    .detail-applicant-summary { display: flex; align-items: center; gap: 10px; margin-bottom: 17px; }
    .detail-applicant-avatar { display: grid; width: 35px; height: 35px; place-items: center; border-radius: 50%; background: #e7eefb; color: #2759a4; font-size: .78rem; font-weight: 700; }
    .detail-applicant-summary div { display: flex; flex-direction: column; gap: 2px; }
    .detail-applicant-summary strong { color: #27344a; font-size: .72rem; }
    .detail-applicant-summary span { color: #168b70; font-size: .58rem; font-weight: 600; }
    .detail-definition-list { margin: 0; }
    .detail-definition-list > div { padding: 9px 0; border-top: 1px solid #eef2f6; }
    .detail-definition-list > div:first-child { padding-top: 0; border-top: 0; }
    .detail-definition-list dt, .detail-definition-list dd { margin: 0; }
    .detail-definition-list dd { line-height: 1.4; }
    .detail-breakable { overflow-wrap: anywhere; }
    .detail-service-card .detail-definition-list > div:last-child { padding-bottom: 0; }
    .detail-conversation-heading { justify-content: space-between; }
    .detail-conversation-heading > div { display: flex; align-items: center; gap: 6px; }
    .detail-muted-label { color: #94a3b8; font-size: .62rem; }
    .detail-helper-text { margin: -7px 0 13px; color: #94a3b8; font-size: .65rem; }
    .detail-conversation-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 7px 17px; padding: 9px 10px; border-bottom: 1px solid #edf1f5; color: #64748b; font-size: .6rem; }
    .detail-conversation-meta strong { color: #475569; font-weight: 600; }
    .detail-conversation-meta .ticket-status-badge { margin-left: auto; }
    .detail-chat-empty { display: flex; min-height: 115px; align-items: center; justify-content: center; flex-direction: column; gap: 5px; color: #94a3b8; text-align: center; }
    .detail-chat-empty i { color: #cbd5e1; font-size: 1.7rem; }
    .detail-chat-empty strong { color: #64748b; font-size: .72rem; }
    .detail-chat-empty span { font-size: .64rem; }
    .detail-chat-history { display: flex; max-height: 260px; overflow-y: auto; flex-direction: column; gap: 9px; padding: 12px 0; }
    .detail-chat-message { max-width: 82%; padding: 9px 11px; border-radius: 8px; }
    .detail-chat-message--incoming { align-self: flex-start; background: #f1f5f9; }
    .detail-chat-message--outgoing { align-self: flex-end; background: #e0f2fe; }
    .detail-chat-message-meta { display: flex; justify-content: space-between; gap: 12px; margin-bottom: 4px; color: #64748b; font-size: .58rem; }
    .detail-chat-message-meta strong { color: #315a91; }
    .detail-chat-message p { margin: 0; color: #475569; font-size: .68rem; line-height: 1.45; white-space: pre-wrap; }
    .detail-chat-compose { display: flex; gap: 8px; padding-top: 11px; border-top: 1px solid #edf1f5; }
    .detail-chat-compose .form-control { height: 34px; background: #f8fafc; font-size: .67rem; }
    .detail-chat-compose .btn { min-width: 92px; font-size: .65rem; }
    .detail-chat-note { display: block; margin-top: 8px; color: #a0aec0; font-size: .58rem; }
    .detail-history-card { padding: 0; overflow: hidden; }
    .detail-history-card .detail-card-heading { margin: 0; padding: 15px 17px; border-bottom: 1px solid var(--detail-border); }
    .detail-history-table { font-size: .68rem; }
    .detail-history-table th { padding: 10px 15px; color: #94a3b8; background: #f8fafc; font-size: .61rem; font-weight: 600; white-space: nowrap; }
    .detail-history-table td { padding: 11px 15px; color: #475569; }
    .detail-history-table td strong, .detail-history-table td small { display: block; }
    .detail-history-table td small { margin-top: 3px; color: #94a3b8; font-size: .58rem; }
    .ticket-status-badge { display: inline-flex; align-items: center; justify-content: center; padding: 4px 8px; border-radius: 5px; font-size: .57rem; font-weight: 700; line-height: 1.2; text-transform: uppercase; white-space: nowrap; }
    .ticket-status-badge--baru { color: #1d4ed8; background: #dbeafe; }
    .ticket-status-badge--didisposisikan { color: #7e22ce; background: #f3e8ff; }
    .ticket-status-badge--diproses { color: #a16207; background: #fef3c7; }
    .ticket-status-badge--selesai { color: #047857; background: #d1fae5; }
    .ticket-status-badge--ditolak { color: #be123c; background: #ffe4e6; }
    .ticket-status-badge--default { color: #475569; background: #e2e8f0; }
    @media (max-width: 767.98px) {
        .detail-page-heading, .detail-status-bar { align-items: flex-start; flex-direction: column; }
        .detail-status-actions { width: 100%; flex-wrap: wrap; }
        .detail-request-grid { grid-template-columns: 1fr; }
        .detail-field-wide { grid-column: auto; }
        .detail-chat-compose { flex-direction: column; }
        .detail-chat-compose .btn { width: 100%; }
        .detail-conversation-meta .ticket-status-badge { margin-left: 0; }
    }
</style>
@stop

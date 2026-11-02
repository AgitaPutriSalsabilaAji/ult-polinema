@extends('adminlte::page')

@section('title', 'Detail Pengajuan Layanan')

@section('content_header')
    <div class="ticket-page-heading">
        <div>
            <span class="ticket-page-eyebrow">E-Ticket Management</span>
            <h1>Detail Pengajuan Layanan</h1>
        </div>
        <a href="{{ route('admin.pengajuan.index') }}" class="ticket-back-link">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Data Pengajuan
        </a>
    </div>
@stop

@section('content')
<div class="ticket-detail-page">
    @if(session('success'))
        <div class="alert alert-success ticket-alert">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger ticket-alert">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="ticket-action-bar">
        <div>
            <span class="ticket-number-label">Nomor E-Ticket</span>
            <strong class="ticket-number">{{ $pengajuan->nomor_tiket }}</strong>
        </div>
        <div class="ticket-actions">
            @if(auth('admin')->user()?->peran === 'admin_unit'
                && $pengajuan->status === 'diproses')
                <form method="POST" action="{{ route('admin.pengajuan.tolak', $pengajuan) }}">
                    @csrf
                    <button class="ticket-button ticket-button--reject" type="submit">
                        <i class="fas fa-times"></i> Tolak Pengajuan
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.pengajuan.selesai', $pengajuan) }}">
                    @csrf
                    <button class="ticket-button ticket-button--complete" type="submit">
                        <i class="fas fa-check"></i> Tandai Selesai
                    </button>
                </form>
            @endif
            @include('admin.partials.status-badge', ['status' => $pengajuan->status])
        </div>
    </div>

    <div class="ticket-layout">
        <main class="ticket-main-column">
            <section class="ticket-card">
                <div class="ticket-card-heading">
                    <span class="ticket-section-number">3.</span>
                    <h2>Detail Permohonan Layanan</h2>
                </div>
                <div class="ticket-detail-grid">
                    <div class="ticket-field">
                        <span>Kategori Pemohon</span>
                        <strong>{{ $pemohon->kategori ?? $pemohon->jenis_pemohon ?? '-' }}</strong>
                    </div>
                    <div class="ticket-field">
                        <span>Layanan Terpilih</span>
                        <strong>{{ $pengajuan->layanan?->nama_layanan ?? '-' }}</strong>
                    </div>
                    <div class="ticket-field">
                        <span>Nama Lengkap</span>
                        <strong>{{ $pemohon->nama_lengkap ?? 'Pemohon #'.$pengajuan->id_pengguna }}</strong>
                    </div>
                    <div class="ticket-field">
                        <span>Unit Tujuan</span>
                        <strong>{{ $pengajuan->unit?->nama_unit ?? 'Belum ditentukan' }}</strong>
                    </div>
                    <div class="ticket-field">
                        <span>NIM / NIP / NIK</span>
                        <strong>{{ $pemohon->nim_nip ?? $pemohon->nip ?? $pemohon->nik ?? '-' }}</strong>
                    </div>
                    <div class="ticket-field">
                        <span>Tanggal Pengajuan</span>
                        <strong>{{ $pengajuan->dibuat_pada?->format('d F Y, H:i') ?? '-' }}</strong>
                    </div>
                    <div class="ticket-field ticket-field--wide">
                        <span>Subjek Permohonan</span>
                        <strong>{{ $pengajuan->subjek }}</strong>
                    </div>
                    <div class="ticket-field ticket-field--wide">
                        <span>Deskripsi Permohonan</span>
                        <div class="ticket-description">{{ $pengajuan->deskripsi }}</div>
                    </div>
                </div>
            </section>

            <section class="ticket-card">
                <div class="ticket-card-heading">
                    <span class="ticket-section-number">4.</span>
                    <h2>Berkas Lampiran Pendukung</h2>
                </div>
                @if($dokumen->isNotEmpty())
                    <div class="ticket-files">
                        @foreach($dokumen as $file)
                            <div class="ticket-file">
                                <span class="ticket-file-icon"><i class="far fa-file-pdf"></i></span>
                                <div class="ticket-file-info">
                                    <strong>{{ $file->nama_file }}</strong>
                                    <small>Dokumen pendukung · {{ $file->ukuran_file ?: 'Ukuran tidak tersedia' }}</small>
                                </div>
                                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($file->path_file) }}"
                                   class="ticket-file-link" target="_blank" rel="noopener">
                                    Unduh
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="ticket-empty-state">
                        <i class="far fa-folder-open"></i>
                        Belum ada dokumen pendukung.
                    </div>
                @endif
            </section>

            <section class="ticket-card conversation-card">
                <div class="ticket-card-heading">
                    <span class="ticket-section-number">5.</span>
                    <div>
                        <h2>Komunikasi Pengajuan</h2>
                        <p>Ruang komunikasi dengan pemohon terkait pengajuan layanan ini.</p>
                    </div>
                </div>
                <div class="conversation-meta">
                    <span>E-Ticket: <strong>{{ $pengajuan->nomor_tiket }}</strong></span>
                    <span>Pemohon: <strong>{{ $pemohon->nama_lengkap ?? '-' }}</strong></span>
                    <span>Unit Penanggung Jawab: <strong>{{ $pengajuan->unit?->nama_unit ?? '-' }}</strong></span>
                </div>
                <div class="conversation-placeholder" aria-label="Placeholder komunikasi pengajuan">
                    <i class="far fa-comments"></i>
                    <strong>Area komunikasi</strong>
                    <span>Riwayat percakapan dan pesan akan tersedia setelah fitur komunikasi diaktifkan.</span>
                </div>
                <div class="conversation-composer" aria-hidden="true">
                    <span>Tulis pesan kepada pemohon...</span>
                    <button type="button" disabled><i class="fas fa-paperclip"></i> Lampirkan File</button>
                    <button type="button" disabled><i class="fas fa-paper-plane"></i> Kirim Pesan</button>
                </div>
                <small class="conversation-note">
                    <i class="fas fa-info-circle"></i>
                    Fitur komunikasi belum tersedia. Kolom ini hanya sebagai persiapan antarmuka.
                </small>
            </section>
        </main>

        <aside class="ticket-side-column">
            <section class="ticket-card applicant-card">
                <div class="ticket-card-heading">
                    <span class="ticket-section-number">1.</span>
                    <h2>Informasi Pemohon</h2>
                </div>
                <div class="applicant-identity">
                    <div class="applicant-avatar"><i class="fas fa-user"></i></div>
                    <div>
                        <strong>{{ $pemohon->nama_lengkap ?? 'Pemohon #'.$pengajuan->id_pengguna }}</strong>
                        <span>{{ $pemohon->kategori ?? $pemohon->jenis_pemohon ?? 'Pemohon' }}</span>
                    </div>
                </div>
                <dl class="ticket-info-list">
                    <div><dt>NIM / NIP / NIK</dt><dd>{{ $pemohon->nim_nip ?? $pemohon->nip ?? $pemohon->nik ?? '-' }}</dd></div>
                    <div><dt>Nomor WhatsApp</dt><dd>{{ $pemohon->nomor_telepon ?? '-' }}</dd></div>
                    <div><dt>Email</dt><dd>{{ $pemohon->email ?? '-' }}</dd></div>
                </dl>
            </section>

            <section class="ticket-card">
                <div class="ticket-card-heading">
                    <span class="ticket-section-number">2.</span>
                    <h2>Informasi Layanan</h2>
                </div>
                <dl class="ticket-info-list">
                    <div><dt>Nama Layanan</dt><dd>{{ $pengajuan->layanan?->nama_layanan ?? '-' }}</dd></div>
                    <div><dt>Unit Penanggung Jawab</dt><dd>{{ $pengajuan->unit?->nama_unit ?? 'Belum ditentukan' }}</dd></div>
                    <div><dt>Status E-Ticket</dt><dd>@include('admin.partials.status-badge', ['status' => $pengajuan->status])</dd></div>
                </dl>
            </section>

            <section class="ticket-card">
                <div class="ticket-card-heading">
                    <h2>Riwayat Status</h2>
                </div>
                <div class="ticket-timeline">
                    @forelse($pengajuan->riwayat->sortByDesc('dibuat_pada') as $item)
                        <div class="timeline-entry">
                            <span class="timeline-dot"></span>
                            <div>
                                <strong>{{ $item->judul_status }}</strong>
                                <small>{{ $item->dibuat_pada?->format('d/m/Y H:i') ?? '-' }}</small>
                                <p>{{ $item->deskripsi }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="ticket-muted">Belum ada riwayat status.</p>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>
</div>
@stop

@section('css')
<style>
    .ticket-detail-page {
        --ticket-navy: #102b4c;
        --ticket-blue: #1d4ed8;
        --ticket-border: #dce5ef;
        --ticket-muted: #78889d;
        max-width: 1440px;
        margin: 0 auto;
        color: #23344d;
        font-family: Inter, sans-serif;
        font-size: 14px;
    }
    .ticket-page-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
    }
    .ticket-page-heading h1 {
        margin: 0;
        color: #172a46;
        font-family: Inter, sans-serif;
        font-size: 20px;
        font-weight: 700;
    }
    .ticket-page-eyebrow {
        display: block;
        margin-bottom: 5px;
        color: var(--ticket-muted);
        font-size: 12px;
        letter-spacing: .08em;
        text-transform: uppercase;
    }
    .ticket-back-link {
        color: var(--ticket-blue);
        font-size: 14px;
        font-weight: 600;
    }
    .ticket-alert { border-radius: 8px; }
    .ticket-action-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
        padding: 12px 16px;
        background: #fff;
        border: 1px solid var(--ticket-border);
        border-radius: 9px;
        box-shadow: 0 2px 8px rgba(16, 43, 76, .04);
    }
    .ticket-number-label, .ticket-field span, .ticket-info-list dt {
        display: block;
        margin-bottom: 4px;
        color: var(--ticket-muted);
        font-size: 12px;
    }
    .ticket-number { color: var(--ticket-blue); font-size: 14px; }
    .ticket-actions { display: flex; align-items: center; gap: 8px; }
    .ticket-status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.2;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .ticket-status-badge--baru { color: #1d4ed8; background: #dbeafe; }
    .ticket-status-badge--didisposisikan { color: #7e22ce; background: #f3e8ff; }
    .ticket-status-badge--diproses { color: #b45309; background: #fef3c7; }
    .ticket-status-badge--selesai { color: #047857; background: #d1fae5; }
    .ticket-status-badge--ditolak { color: #be123c; background: #ffe4e6; }
    .ticket-status-badge--default { color: #475569; background: #e2e8f0; }
    .ticket-actions form { margin: 0; }
    .ticket-button {
        padding: 7px 10px;
        border: 0;
        border-radius: 5px;
        font-size: 14px;
        font-weight: 600;
    }
    .ticket-button--reject { color: #be123c; background: #ffe4e6; }
    .ticket-button--complete { color: #047857; background: #d1fae5; }
    .ticket-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.65fr) minmax(270px, .75fr);
        gap: 18px;
        align-items: start;
    }
    .ticket-main-column, .ticket-side-column { display: grid; gap: 18px; min-width: 0; }
    .ticket-card {
        padding: 16px;
        background: #fff;
        border: 1px solid var(--ticket-border);
        border-radius: 9px;
        box-shadow: 0 2px 8px rgba(16, 43, 76, .035);
    }
    .ticket-card-heading {
        display: flex;
        align-items: flex-start;
        gap: 6px;
        margin-bottom: 14px;
    }
    .ticket-card-heading h2 {
        margin: 0;
        color: #203651;
        font-family: Inter, sans-serif;
        font-size: 20px;
        font-weight: 700;
    }
    .ticket-card-heading p { margin: 4px 0 0; color: var(--ticket-muted); font-size: 12px; }
    .ticket-section-number { color: var(--ticket-blue); font-weight: 800; }
    .ticket-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 28px; }
    .ticket-field--wide { grid-column: 1 / -1; }
    .ticket-field strong { display: block; color: #203651; font-size: 14px; font-weight: 600; }
    .ticket-description {
        padding: 10px 12px;
        color: #4b5c72;
        background: #f7fafd;
        border: 1px solid #e1eaf3;
        border-radius: 6px;
        line-height: 1.55;
        white-space: pre-wrap;
    }
    .ticket-files { display: grid; gap: 8px; }
    .ticket-file {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 10px;
        background: #f8fafc;
        border: 1px solid #e1eaf3;
        border-radius: 6px;
    }
    .ticket-file-icon { color: #f06b83; font-size: 18px; }
    .ticket-file-info { min-width: 0; flex: 1; }
    .ticket-file-info strong, .ticket-file-info small { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ticket-file-info strong { color: #293d59; font-size: 14px; }
    .ticket-file-info small { color: var(--ticket-muted); font-size: 12px; }
    .ticket-file-link { padding: 4px 8px; color: var(--ticket-blue); border: 1px solid #d5e0f0; border-radius: 4px; font-size: 12px; }
    .ticket-empty-state, .ticket-muted { color: var(--ticket-muted); font-size: 12px; text-align: center; }
    .ticket-empty-state i { margin-right: 5px; }
    .applicant-identity { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
    .applicant-avatar { display: grid; width: 38px; height: 38px; place-items: center; color: #fff; background: #91a6bd; border-radius: 50%; }
    .applicant-identity strong, .applicant-identity span { display: block; }
    .applicant-identity strong { color: #203651; font-size: 14px; }
    .applicant-identity span { margin-top: 3px; color: #047857; font-size: 12px; }
    .ticket-info-list { display: grid; gap: 11px; margin: 0; }
    .ticket-info-list div { min-width: 0; }
    .ticket-info-list dd { margin: 0; color: #203651; font-size: 14px; font-weight: 600; overflow-wrap: anywhere; }
    .ticket-timeline { display: grid; gap: 13px; }
    .timeline-entry { display: grid; grid-template-columns: 10px minmax(0, 1fr); gap: 8px; }
    .timeline-dot { width: 8px; height: 8px; margin-top: 4px; background: #7aa7df; border-radius: 50%; }
    .timeline-entry strong, .timeline-entry small, .timeline-entry p { display: block; }
    .timeline-entry strong { color: #203651; font-size: 14px; }
    .timeline-entry small { color: var(--ticket-muted); font-size: 12px; }
    .timeline-entry p { margin: 3px 0 0; color: #607087; font-size: 14px; line-height: 1.45; }
    .conversation-meta { display: flex; flex-wrap: wrap; gap: 8px 18px; padding: 9px 10px; color: var(--ticket-muted); background: #f7fafd; border: 1px solid #e1eaf3; border-radius: 6px; font-size: 12px; }
    .conversation-meta strong { color: var(--ticket-blue); }
    .conversation-placeholder { display: grid; min-height: 150px; place-content: center; justify-items: center; gap: 5px; margin-top: 12px; color: #91a0b2; background: #fbfcfe; border: 1px dashed #cbd8e6; border-radius: 7px; text-align: center; }
    .conversation-placeholder i { color: #9eb4cf; font-size: 24px; }
    .conversation-placeholder strong { color: #718198; font-size: 14px; }
    .conversation-placeholder span { max-width: 330px; font-size: 12px; }
    .conversation-composer { display: flex; align-items: center; gap: 7px; margin-top: 12px; }
    .conversation-composer > span { flex: 1; padding: 9px 10px; color: #a4afbd; background: #f7fafd; border: 1px solid #dfe7f0; border-radius: 5px; font-size: 14px; }
    .conversation-composer button { padding: 8px 9px; color: #8797aa; background: #f3f6fa; border: 1px solid #dfe7f0; border-radius: 5px; font-size: 12px; }
    .conversation-note { display: block; margin-top: 8px; color: #a0adbb; font-size: 12px; }
    @media (max-width: 991.98px) {
        .ticket-layout { grid-template-columns: 1fr; }
        .ticket-side-column { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ticket-side-column .ticket-card:last-child { grid-column: 1 / -1; }
    }
    @media (max-width: 575.98px) {
        .ticket-page-heading, .ticket-action-bar { align-items: flex-start; flex-direction: column; }
        .ticket-page-heading h1 { font-size: 20px; }
        .ticket-actions { flex-wrap: wrap; }
        .ticket-detail-grid, .ticket-side-column { grid-template-columns: 1fr; }
        .ticket-field--wide, .ticket-side-column .ticket-card:last-child { grid-column: auto; }
        .conversation-composer { align-items: stretch; flex-wrap: wrap; }
        .conversation-composer > span { flex-basis: 100%; }
    }
</style>
@stop

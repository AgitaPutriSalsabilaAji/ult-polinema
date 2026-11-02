@extends('adminlte::page')

@section('title', 'Data Pengajuan')

@section('content_header')
    <h1 class="admin-page-title">Data Pengajuan Pelayanan</h1>
@stop

@section('content')
<div class="container-fluid pengajuan-page">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm pengajuan-filter">
        <div class="card-body">
            <form method="GET"
                action="{{ route('admin.pengajuan.index') }}"
                class="row g-2 align-items-center">

                <div class="{{ $isAdminUlt ? 'col-lg-5' : 'col-lg-7' }}">
                    <input type="search"
                        name="q"
                        class="form-control"
                        value="{{ request('q') }}"
                        placeholder="Cari ID, nama pemohon, atau subjek...">
                </div>

                @if($isAdminUlt)
                    <div class="col-lg-3">
                        <select name="unit" class="form-select">
                            <option value="">Semua Unit Pelaksana</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}"
                                    @selected((string) request('unit') === (string) $unit->id)>
                                    {{ $unit->nama_unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="col-lg-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach([
                            'diajukan' => 'Diajukan',
                            'baru' => 'Baru',
                            'diproses' => 'Diproses',
                            'didisposisikan' => 'Didisposisikan',
                            'selesai' => 'Selesai',
                            'ditolak' => 'Ditolak',
                        ] as $value => $label)
                            <option value="{{ $value }}"
                                @selected(request('status') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 d-grid">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>

            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>ID Pengajuan</th>
                            <th>Nama Pemohon</th>
                            <th>Layanan Diminta</th>
                            <th>Unit</th>
                            <th>Tanggal Masuk</th>
                            <th>Status</th>
                            <th>Admin FO</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($pengajuans as $item)
                        <tr>
                            <td>{{ $pengajuans->firstItem() + $loop->index }}</td>
                            <td>
                                <strong class="text-primary">
                                    {{ $item->nomor_tiket }}
                                </strong>
                                <div class="small text-muted">
                                    #{{ $item->id }}
                                </div>
                            </td>
                            <td>
                                <strong>{{ $item->nama_pemohon ?? 'Pemohon #' . $item->id_pengguna }}</strong>
                                @if(!empty($item->nip_pemohon))
                                    <div class="small text-muted">
                                        NIP: {{ $item->nip_pemohon }}
                                    </div>
                                @endif
                            </td>
                            <td>{{ $item->nama_layanan ?? $item->subjek }}</td>
                            <td>{{ $item->nama_unit ?? 'Belum ditentukan' }}</td>
                            <td>
                                {{ $item->dibuat_pada
                                    ? \Carbon\Carbon::parse($item->dibuat_pada)->format('d/m/Y')
                                    : '-' }}
                            </td>
                            <td>
                                @include('admin.partials.status-badge', ['status' => $item->status])
                            </td>
                            <td>{{ $item->nama_admin_fo ?? 'Belum ditugaskan' }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('admin.pengajuan.show', $item->id) }}"
                                class="btn btn-xs btn-outline-secondary">
                                    Detail
                                </a>

                                @if($isAdminUlt && in_array($item->status, ['baru', 'diajukan']))
                                    <button type="button"
                                            class="btn btn-xs btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalDisposisi"
                                            data-id="{{ $item->id }}"
                                            data-tiket="{{ $item->nomor_tiket }}"
                                            data-pemohon="{{ $item->nama_pemohon ?? 'Pemohon #' . $item->id_pengguna }}"
                                            data-layanan="{{ $item->nama_layanan ?? $item->subjek }}"
                                            data-unit-id="{{ $item->unit_tujuan_id }}"
                                            data-unit-name="{{ $item->nama_unit ?? 'Belum ditentukan' }}"
                                            data-status="{{ strtoupper($item->status === 'diajukan' ? 'BARU' : $item->status) }}">
                                        Disposisi
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center p-4">
                                Belum ada data pengajuan.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <small>
                Menampilkan {{ $pengajuans->firstItem() ?? 0 }}–{{ $pengajuans->lastItem() ?? 0 }}
                dari {{ $pengajuans->total() }} pengajuan
            </small>
            {{ $pengajuans->links() }}
        </div>
    </div>
</div>

@if($isAdminUlt)
<div class="modal fade" id="modalDisposisi" tabindex="-1"
     aria-labelledby="judulModalDisposisi" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-disposisi-dialog">
        <div class="modal-content modal-disposisi">
            <div class="modal-header modal-disposisi__header">
                <div class="modal-disposisi__heading">
                    <h5 class="modal-title" id="judulModalDisposisi">
                        Disposisi Pengajuan ke Unit Kerja
                    </h5>
                    <p class="modal-disposisi__subtitle mb-0">
                        Teruskan berkas permohonan ke admin pelaksana
                    </p>
                </div>
                <span id="headerTiket" class="modal-disposisi__ticket"></span>
            </div>

            <form id="formDisposisi" method="POST">
                @csrf
                <div class="modal-body">
                    <div id="disposisiError" class="alert alert-danger d-none" role="alert"></div>
                    <div class="modal-disposisi__application-info">
                        <div class="row">
                            <div class="col-6 col-md-3">
                                <span class="modal-disposisi__info-label">E-Tiket</span>
                                <strong id="detailTiket"></strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="modal-disposisi__info-label">Pemohon</span>
                                <strong id="detailPemohon"></strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="modal-disposisi__info-label">Layanan</span>
                                <strong id="detailLayanan"></strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="modal-disposisi__info-label">Status Saat Ini</span>
                                <strong id="detailStatus" class="modal-disposisi__status"></strong>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="id_unit_tujuan" class="modal-disposisi__label">
                            Unit Tujuan Disposisi <span aria-hidden="true">*</span>
                        </label>
                        <input type="hidden" name="id_unit_tujuan" id="id_unit_tujuan">
                        <input type="text" id="nama_unit_tujuan"
                               class="form-control modal-disposisi__input modal-disposisi__input--readonly"
                               readonly aria-readonly="true">
                    </div>

                    <div class="mb-3">
                        <label for="id_admin_tujuan" class="modal-disposisi__label">
                            Admin Staf Tujuan (Assignee) <span aria-hidden="true">*</span>
                        </label>
                        <select name="id_admin_tujuan" id="id_admin_tujuan"
                                class="form-select modal-disposisi__input" required disabled>
                            <option value="">Pilih unit terlebih dahulu</option>
                        </select>
                    </div>

                    <div>
                        <label for="catatan_disposisi" class="modal-disposisi__label">
                            Catatan Instruksi Disposisi (Opsional)
                        </label>
                        <textarea name="catatan_disposisi"
                                  id="catatan_disposisi"
                                  class="form-control modal-disposisi__input" rows="3"
                                  maxlength="5000"
                                  placeholder="Masukkan catatan atau instruksi disposisi (opsional)..."></textarea>
                    </div>
                </div>

                <div class="modal-footer modal-disposisi__footer">
                    <button type="button" class="btn modal-disposisi__cancel"
                            data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn modal-disposisi__submit">
                        <i class="bi bi-send-fill" aria-hidden="true"></i>
                        <span class="modal-disposisi__submit-label">Kirim Disposisi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@stop

@section('css')
<style>
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
    .table th, .table td {
        vertical-align: middle;
        padding: 11px 9px;
        font-size: 14px;
    }
    .table thead th { white-space: nowrap; }
    .btn-xs { padding: 4px 7px; }
    .modal-disposisi-dialog {
        max-width: 560px;
    }
    .modal-disposisi {
        font-family: 'Inter', sans-serif;
        max-height: calc(100vh - 2rem);
        border: 1px solid #c8d4e4;
        border-radius: 14px;
        box-shadow: 0 18px 45px rgba(8, 29, 55, 0.24);
        overflow: hidden;
    }
    .modal-disposisi__header {
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 16px 20px 14px;
        color: #fff;
        background: #102b4c;
        border-bottom: 0;
    }
    .modal-disposisi__heading { min-width: 0; }
    .modal-disposisi .modal-title {
        font-size: 20px;
        font-weight: 700;
        line-height: 1.35;
    }
    .modal-disposisi__subtitle {
        color: #a9bed8;
        font-size: 12px;
        font-weight: 400;
        line-height: 1.5;
    }
    .modal-disposisi__ticket {
        flex: 0 0 auto;
        margin-top: 7px;
        color: #8eb6e2;
        font-size: 12px;
        font-weight: 400;
    }
    .modal-disposisi .modal-body {
        overflow-y: auto;
        padding: 20px;
    }
    .modal-disposisi__application-info {
        margin-bottom: 17px;
        padding: 12px 11px;
        color: #243650;
        background: #f4f7fb;
        border: 1px solid #dfe7f1;
        border-radius: 10px;
    }
    .modal-disposisi__application-info .row {
        row-gap: 12px;
    }
    .modal-disposisi__application-info strong {
        display: block;
        overflow: hidden;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.3;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .modal-disposisi__info-label {
        display: block;
        margin-bottom: 4px;
        color: #91a3ba;
        font-size: 12px;
        line-height: 1;
    }
    .modal-disposisi__status {
        width: fit-content;
        padding: 3px 6px;
        color: #2461ba;
        background: #dceaff;
        border-radius: 3px;
        font-size: 12px !important;
    }
    .modal-disposisi__label {
        display: block;
        margin-bottom: 7px;
        color: #243650;
        font-size: 14px;
        font-weight: 500;
    }
    .modal-disposisi__label span { color: #e05252; }
    .modal-disposisi__input {
        min-height: 34px;
        color: #34445a;
        background: #f7f9fc;
        border-color: #ccd9e8;
        border-radius: 7px;
        font-size: 14px;
        font-weight: 400;
    }
    .modal-disposisi__input:focus {
        background: #fff;
        border-color: #5279b2;
        box-shadow: 0 0 0 0.15rem rgba(39, 81, 145, 0.12);
    }
    .modal-disposisi__input--readonly {
        cursor: default;
        background: #f7f9fc;
    }
    .modal-disposisi textarea.modal-disposisi__input {
        min-height: 58px;
        resize: vertical;
    }
    .modal-disposisi__footer {
        justify-content: flex-end;
        gap: 8px;
        padding: 0 20px 16px;
        border-top: 0;
    }
    .modal-disposisi__cancel,
    .modal-disposisi__submit {
        min-height: 30px;
        padding: 5px 13px;
        border-radius: 7px;
        font-size: 14px;
        font-weight: 500;
    }
    .modal-disposisi__cancel {
        color: #53647a;
        background: #fff;
        border: 1px solid #cbd7e5;
    }
    .modal-disposisi__submit {
        color: #fff;
        background: #24449a;
        border: 1px solid #24449a;
    }
    .modal-disposisi__submit:hover,
    .modal-disposisi__submit:focus {
        color: #fff;
        background: #1b367f;
        border-color: #1b367f;
    }
    .modal-disposisi__submit:disabled {
        cursor: wait;
        opacity: 0.7;
    }
    .modal-backdrop.show {
        opacity: 0.58;
        backdrop-filter: blur(3px);
    }

    .pengajuan-page {
        font-size: 12px;
    }

    /* Judul halaman */
    .admin-page-title {
        font-size: 20px;
        font-weight: 700;
    }

    /* Jarak antarkartu */
    .pengajuan-page .card {
        margin-bottom: 14px;
        border-radius: 8px;
    }

    /* Card pencarian */
    .pengajuan-page .pengajuan-filter .card-body {
        padding: 14px !important;
    }

    /* Input dan dropdown */
    .pengajuan-page .form-control,
    .pengajuan-page .form-select {
        min-height: 33px;
        padding: 6px 10px;
        font-size: 12px;
        border-radius: 5px;
    }

    /* Placeholder pencarian */
    .pengajuan-page input::placeholder {
        font-size: 12px;
    }

    /* Tombol */
    .pengajuan-page .btn {
        padding: 5px 10px;
        font-size: 12px;
        border-radius: 5px;
    }

    /* Tabel */
    .pengajuan-page .table {
        margin-bottom: 0;
        font-size: 12px;
    }

    /* Header tabel */
    .pengajuan-page .table thead th {
        padding: 11px 12px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        vertical-align: middle;
    }

    /* Isi tabel */
    .pengajuan-page .table tbody td {
        padding: 10px 12px;
        font-size: 12px;
        line-height: 1.5;
        vertical-align: middle;
    }

    /* Nomor tiket */
    .pengajuan-page .table strong {
        font-weight: 600;
    }

    /* Informasi tambahan */
    .pengajuan-page .table .small {
        font-size: 11px;
    }

    /* Tombol detail dan disposisi */
    .pengajuan-page .btn-xs {
        padding: 4px 8px;
        font-size: 11px;
        line-height: 1.4;
        border-radius: 4px;
    }

    /* Badge status */
    .pengajuan-page .ticket-status-badge {
        padding: 4px 8px;
        font-size: 10px;
        border-radius: 5px;
    }

    /* Footer tabel */
    .pengajuan-page .card-footer {
        padding: 10px 14px;
        font-size: 11px;
        background: #fff;
    }

    /* Pagination */
    .pengajuan-page .pagination {
        margin-bottom: 0;
        font-size: 11px;
    }

    .pengajuan-page .pagination .page-link {
        padding: 5px 9px;
    }

    /* Responsif untuk layar kecil */
    @media (max-width: 767.98px) {
        .pengajuan-page .pengajuan-filter .card-body {
            padding: 12px !important;
        }

        .pengajuan-page .table thead th,
        .pengajuan-page .table tbody td {
            padding: 9px 10px;
        }

        .pengajuan-page .card-footer {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 10px;
        }
    }

    .pengajuan-page .table .btn-xs {
        padding: 2px 6px !important;
        font-size: 12px !important;
        line-height: 1.3 !important;
        border-radius: 4px;
        min-height: 22px;
    }

    /* Jarak antar tombol */
    .pengajuan-page .table td.text-nowrap .btn {
        margin-right: 2px;
    }

    @media (max-width: 575.98px) {
        .modal-disposisi-dialog {
            margin: 0.75rem;
        }
        .modal-disposisi__header,
        .modal-disposisi .modal-body {
            padding-left: 15px;
            padding-right: 15px;
        }
        .modal-disposisi__footer {
            padding-left: 15px;
            padding-right: 15px;
        }
    }
</style>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalDisposisi');
    const unitIdField = document.getElementById('id_unit_tujuan');
    const unitField = document.getElementById('nama_unit_tujuan');
    const adminSelect = document.getElementById('id_admin_tujuan');
    const noteField = document.getElementById('catatan_disposisi');
    const form = document.getElementById('formDisposisi');
    const submitButton = form?.querySelector('.modal-disposisi__submit');
    const errorBox = document.getElementById('disposisiError');
    const adminsByUnit = {{ Illuminate\Support\Js::from(
        $adminTujuanByUnit->map(function ($admins) {
            return $admins->map(function ($admin) {
                return [
                    'id' => $admin->id,
                    'nama' => $admin->nama_lengkap_gelar,
                    'peran' => $admin->peran,
                ];
            })->values();
        })
    ) }};

    function resetAdminSelect(message) {
        adminSelect.replaceChildren(new Option(message, ''));
        adminSelect.disabled = true;
    }

    function updateAdminSelect() {
        const unitId = String(unitIdField.value);
        adminSelect.replaceChildren();

        if (!unitId) {
            resetAdminSelect('Unit tujuan belum tersedia');
            return;
        }

        const matchingAdmins = adminsByUnit[unitId] || [];

        if (!matchingAdmins.length) {
            resetAdminSelect('Belum ada admin aktif pada unit ini');
            return;
        }

        adminSelect.append(new Option('Pilih admin tujuan', ''));
        matchingAdmins.forEach(function (admin) {
            const role = admin.peran === 'admin_unit' ? 'Staf Unit' : admin.peran;
            adminSelect.append(new Option(
                admin.nama + ' (' + role + ')',
                admin.id
            ));
        });
        adminSelect.disabled = false;
    }

    if (!modal || !unitIdField || !unitField || !adminSelect || !noteField || !form || !submitButton || !errorBox) {
        return;
    }

    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        if (!button) {
            return;
        }

        document.getElementById('headerTiket').textContent = '#' + button.dataset.tiket;
        document.getElementById('detailTiket').textContent = button.dataset.tiket;
        document.getElementById('detailPemohon').textContent = button.dataset.pemohon;
        document.getElementById('detailLayanan').textContent = button.dataset.layanan;
        document.getElementById('detailStatus').textContent = button.dataset.status;
        unitIdField.value = button.dataset.unitId || '';
        unitField.value = button.dataset.unitName || 'Unit belum ditentukan';
        form.action = '{{ url('/admin/pengajuan') }}/' + button.dataset.id + '/disposisi';
        noteField.value = '';
        adminSelect.value = '';
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
        submitButton.disabled = false;
        submitButton.querySelector('.modal-disposisi__submit-label').textContent = 'Kirim Disposisi';
        updateAdminSelect();
    });

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        submitButton.disabled = true;
        submitButton.querySelector('.modal-disposisi__submit-label').textContent = 'Mengirim...';
        errorBox.textContent = '';
        errorBox.classList.add('d-none');

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(form),
            });
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Disposisi gagal dikirim.');
            }

            bootstrap.Modal.getOrCreateInstance(modal).hide();
            window.location.reload();
        } catch (error) {
            errorBox.textContent = error.message || 'Disposisi gagal dikirim.';
            errorBox.classList.remove('d-none');
            submitButton.disabled = false;
            submitButton.querySelector('.modal-disposisi__submit-label').textContent = 'Kirim Disposisi';
        }
    });
});
</script>
@stop

```blade
@extends('adminlte::page')

@section('title', 'Profil & Pengaturan Akun')

@section('content_header')
    <h4 class="profile-page-title mb-0">Profil & Pengaturan Akun</h4>
@stop

@section('content')
<div class="profile-page">

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close"
                data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Periksa kembali data yang dimasukkan.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="profile-wrapper">

        {{-- INFORMASI PROFIL --}}
        <div class="card profile-card mb-4">

            <div class="card-body">

                {{-- Header profil --}}
                <div class="profile-header">

                    <div class="profile-identity">
                        <div class="profile-avatar">
                            @if ($admin->avatar)
                                <img
                                    src="{{ asset('storage/' . $admin->avatar) }}"
                                    alt="Foto profil admin">
                            @else
                                <div class="profile-avatar-placeholder">
                                    {{ strtoupper(substr($admin->nama_lengkap_gelar, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <div class="profile-identity-info">
                            <h5 class="profile-name">
                                {{ $admin->nama_lengkap_gelar }}
                            </h5>

                            <p class="profile-position">
                                {{ $admin->peran === 'admin_ult'
                                    ? 'Administrator Unit Layanan Terpadu'
                                    : 'Administrator Unit' }}
                            </p>

                            <div class="profile-badges">
                                <span class="badge-role">
                                    {{ $admin->peran === 'admin_ult'
                                        ? 'Admin ULT'
                                        : 'Admin Unit' }}
                                </span>

                                <span class="badge-unit">
                                    {{ $admin->unit?->nama_unit ?? 'Semua Akses Unit' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <button type="button"
                        class="btn btn-edit-profile"
                        id="btnEditProfil"
                        aria-expanded="false">
                        <i class="bi bi-pencil-square me-1"></i>
                        Edit Profil
                    </button>

                </div>

                <hr class="profile-divider">

                {{-- Informasi profil --}}
                <form action="{{ route('admin.profil.update') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    id="formProfil">

                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap & Gelar</label>
                            <input type="text"
                                name="nama_lengkap_gelar"
                                class="form-control profile-input"
                                value="{{ old('nama_lengkap_gelar', $admin->nama_lengkap_gelar) }}"
                                maxlength="150"
                                required
                                disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email Kedinasan</label>
                            <input type="email"
                                name="email"
                                class="form-control profile-input"
                                value="{{ old('email', $admin->email) }}"
                                maxlength="255"
                                required
                                disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nomor Telepon / WhatsApp</label>
                            <input type="text"
                                name="nomor_telepon"
                                class="form-control profile-input"
                                value="{{ old('nomor_telepon', $admin->nomor_telepon) }}"
                                maxlength="30"
                                disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">NIP / Nomor Induk Pegawai</label>
                            <input type="text"
                                name="nip"
                                class="form-control profile-input"
                                value="{{ old('nip', $admin->nip) }}"
                                maxlength="30"
                                disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Role Pengguna (System Role)</label>
                            <div class="profile-readonly-field">
                                {{ $admin->peran === 'admin_ult'
                                    ? 'Admin ULT (Super Access)'
                                    : 'Admin Unit' }}
                                <i class="bi bi-lock"></i>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Unit Terdaftar (Read-Only)</label>
                            <div class="profile-readonly-field">
                                {{ $admin->unit?->nama_unit ?? 'Belum ditentukan' }}
                                <i class="bi bi-lock"></i>
                            </div>
                        </div>

                        {{-- Upload foto muncul saat mode edit --}}
                        <div class="col-12 profile-photo-field d-none">
                            <label class="form-label">Foto Profil</label>
                            <input type="file"
                                name="avatar"
                                class="form-control profile-input"
                                accept=".jpg,.jpeg,.png,.webp"
                                disabled>
                            <small class="text-muted">
                                Format JPG, PNG, atau WEBP. Maksimal 2 MB.
                            </small>
                        </div>

                    </div>

                    <div class="profile-edit-actions d-none mt-4">
                        <button type="button"
                            class="btn btn-light btn-profile-cancel"
                            id="btnBatalEdit">
                            Batal
                        </button>

                        <button type="submit"
                            class="btn btn-primary btn-profile-save">
                            <i class="bi bi-check2 me-1"></i>
                            Simpan Perubahan
                        </button>
                    </div>

                </form>

            </div>
        </div>

        {{-- KEAMANAN AKUN --}}
        <div class="card profile-card security-card">

            <div class="card-body">

                <h6 class="security-title">Keamanan & Ubah Kata Sandi</h6>

                <hr class="profile-divider security-divider">

                <form action="{{ route('admin.profil.password') }}"
                    method="POST">

                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Kata Sandi Saat Ini</label>
                            <input type="password"
                                name="kata_sandi_saat_ini"
                                class="form-control profile-input"
                                autocomplete="current-password"
                                required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Kata Sandi Baru</label>
                            <input type="password"
                                name="kata_sandi_baru"
                                class="form-control profile-input"
                                placeholder="Minimal 8 karakter..."
                                minlength="8"
                                autocomplete="new-password"
                                required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Ulangi Kata Sandi Baru</label>
                            <input type="password"
                                name="kata_sandi_baru_confirmation"
                                class="form-control profile-input"
                                placeholder="Konfirmasi sandi..."
                                minlength="8"
                                autocomplete="new-password"
                                required>
                        </div>

                    </div>

                    <div class="security-footer">
                        <button type="submit"
                            class="btn btn-password-update">
                            Perbarui Kata Sandi
                        </button>

                        <small class="password-note">
                            Gunakan kata sandi minimal 8 karakter.
                        </small>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>
@stop

@push('css')
<style>
    /* ===== HALAMAN PROFIL ===== */

    .content-wrapper {
        background: #f7f9fc !important;
    }

    .content-header {
        background: #fff;
        padding: 17px 24px !important;
        border-bottom: 1px solid #e2e8f0;
    }

    .profile-page-title {
        font-size: 21px;
        font-weight: 700;
        color: #172033;
    }

    .profile-page {
        min-height: calc(100vh - 150px);
        padding: 10px 12px 32px;
        font-size: 12px;
    }

    .profile-wrapper {
        width: 100%;
        max-width: 848px;
        margin: 0 auto;
    }

    /* Card */

    .profile-card {
        background: #fff;
        border: 1px solid #dce5f1 !important;
        border-radius: 13px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04) !important;
    }

    .profile-card > .card-body {
        padding: 24px !important;
    }

    /* Header profil */

    .profile-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }

    .profile-identity {
        display: flex;
        align-items: center;
        gap: 20px;
        min-width: 0;
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
        min-width: 80px;
        overflow: hidden;
        border-radius: 50%;
        border: 2px solid #29479c;
        background: #e8eefc;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-avatar-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #29479c;
        font-size: 28px;
        font-weight: 700;
    }

    .profile-identity-info {
        min-width: 0;
    }

    .profile-name {
        font-size: 16px;
        line-height: 1.4;
        font-weight: 700;
        color: #172033;
        margin: 0 0 2px;
    }

    .profile-position {
        font-size: 11.5px;
        color: #71829e;
        margin: 0 0 7px;
    }

    .profile-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
    }

    .badge-role,
    .badge-unit {
        display: inline-block;
        border-radius: 4px;
        padding: 4px 9px;
        font-size: 10px;
        line-height: 1.3;
    }

    .badge-role {
        background: #263f91;
        color: #fff;
        font-weight: 600;
    }

    .badge-unit {
        background: #f0f4fa;
        color: #34445f;
    }

    /* Tombol edit profil */

    .btn-edit-profile {
        background: #263f91;
        color: #fff;
        border: 1px solid #263f91;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .btn-edit-profile:hover,
    .btn-edit-profile:focus {
        background: #1c3174;
        border-color: #1c3174;
        color: #fff;
    }

    .profile-divider {
        border: 0;
        border-top: 1px solid #edf1f7;
        opacity: 1;
        margin: 24px 0;
    }

    /* Form profil */

    .profile-page .form-label {
        display: block;
        color: #34445f;
        font-size: 11.5px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .profile-page .profile-input {
        width: 100%;
        height: 39px;
        padding: 8px 10px;
        font-size: 11.5px;
        color: #172033;
        background: #f8fafc;
        border: 1px solid #dce5f1;
        border-radius: 8px;
        box-shadow: none;
    }

    .profile-page .profile-input:focus {
        background: #fff;
        border-color: #8ca8e7;
        box-shadow: 0 0 0 2px rgba(38, 63, 145, .08);
    }

    .profile-page .profile-input:disabled {
        background: #f8fafc;
        opacity: 1;
        color: #172033;
        cursor: default;
    }

    .profile-page .readonly-input,
    .profile-readonly-field {
        background: #f0f4f9 !important;
        color: #34445f !important;
    }

    .profile-readonly-field {
        min-height: 39px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 8px 10px;
        border: 1px solid #dce5f1;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 500;
        overflow-wrap: anywhere;
    }

    .profile-readonly-field i {
        flex-shrink: 0;
        color: #8b9bb5;
        font-size: 12px;
    }

    .profile-photo-field small {
        display: block;
        font-size: 10.5px;
        margin-top: 5px;
    }

    .btn-profile-cancel,
    .btn-profile-save {
        border-radius: 7px;
        padding: 8px 13px;
        font-size: 11.5px;
        font-weight: 600;
    }

    .btn-profile-save {
        background: #263f91;
        border-color: #263f91;
    }

    /* Keamanan akun */

    .security-card > .card-body {
        padding: 24px !important;
    }

    .security-title {
        color: #172033;
        font-size: 12px;
        font-weight: 700;
        margin: 0;
    }

    .security-divider {
        margin: 12px 0 16px;
    }

    .security-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 24px;
    }

    .btn-password-update {
        background: #202b40;
        color: #fff;
        border: 1px solid #202b40;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 11.5px;
        font-weight: 600;
    }

    .btn-password-update:hover {
        background: #111b2d;
        border-color: #111b2d;
        color: #fff;
    }

    .password-note {
        color: #71829e;
        font-size: 10.5px;
        text-align: right;
    }

    /* Responsif */

    @media (max-width: 767.98px) {
        .content-header {
            padding: 14px 16px !important;
        }

        .profile-page-title {
            font-size: 18px;
        }

        .profile-page {
            padding: 8px 0 24px;
        }

        .profile-card > .card-body,
        .security-card > .card-body {
            padding: 17px !important;
        }

        .profile-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .profile-identity {
            gap: 12px;
        }

        .profile-avatar {
            width: 64px;
            height: 64px;
            min-width: 64px;
        }

        .profile-name {
            font-size: 14px;
        }

        .profile-position {
            font-size: 11px;
        }

        .btn-edit-profile {
            align-self: flex-end;
        }

        .profile-divider {
            margin: 18px 0;
        }

        .security-footer {
            align-items: flex-start;
            flex-direction: column;
        }

        .password-note {
            text-align: left;
        }
    }

    @media (max-width: 420px) {
        .profile-identity {
            align-items: flex-start;
        }

        .profile-avatar {
            width: 54px;
            height: 54px;
            min-width: 54px;
        }

        .profile-name {
            font-size: 13px;
        }
    }
</style>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const formProfil = document.getElementById('formProfil');
    const btnEdit = document.getElementById('btnEditProfil');
    const btnBatal = document.getElementById('btnBatalEdit');

    if (!formProfil || !btnEdit || !btnBatal) {
        return;
    }

    const editableFields = formProfil.querySelectorAll(
        'input[name="nama_lengkap_gelar"], ' +
        'input[name="email"], ' +
        'input[name="nip"], ' +
        'input[name="nomor_telepon"], ' +
        'input[name="avatar"]'
    );

    const photoField = formProfil.querySelector('.profile-photo-field');
    const editActions = formProfil.querySelector('.profile-edit-actions');

    function setEditMode(enabled) {
        editableFields.forEach(function (field) {
            field.disabled = !enabled;
        });

        photoField.classList.toggle('d-none', !enabled);
        editActions.classList.toggle('d-none', !enabled);

        btnEdit.classList.toggle('d-none', enabled);
        btnEdit.setAttribute('aria-expanded', enabled ? 'true' : 'false');
    }

    btnEdit.addEventListener('click', function () {
        setEditMode(true);
    });

    btnBatal.addEventListener('click', function () {
        formProfil.reset();
        setEditMode(false);
    });
});
</script>
@endpush
```
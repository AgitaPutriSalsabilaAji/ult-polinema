@extends('adminlte::page')

@section('title', 'Layanan')

@section('content')
<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="admin-page-title mb-1">Profil & Pengaturan Akun</h4>
            <p class="text-muted mb-0">
                Kelola informasi profil dan keamanan akun Anda.
            </p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close"
                data-bs-dismiss="alert"></button>
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

    {{-- INFORMASI PROFIL --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Informasi Profil</h5>
        </div>

        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                @if ($admin->avatar)
                    <img
                        src="{{ asset('storage/' . $admin->avatar) }}"
                        alt="Foto profil admin"
                        class="rounded-circle border"
                        style="width:80px;height:80px;object-fit:cover"
                    >
                @else
                    <div class="rounded-circle bg-primary text-white
                        d-flex align-items-center justify-content-center"
                        style="width:80px;height:80px;font-size:28px">
                        {{ strtoupper(substr($admin->nama_lengkap_gelar, 0, 1)) }}
                    </div>
                @endif

                <div>
                    <h5 class="fw-bold mb-1">
                        {{ $admin->nama_lengkap_gelar }}
                    </h5>

                    <p class="text-muted mb-1">
                        {{ $admin->email }}
                    </p>

                    <span class="badge bg-primary">
                        {{ $admin->peran === 'admin_ult'
                            ? 'Admin ULT'
                            : 'Admin Unit' }}
                    </span>
                </div>
            </div>

            <hr>

            <form action="{{ route('admin.profil.update') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Nama Lengkap & Gelar
                        </label>
                        <input type="text"
                            name="nama_lengkap_gelar"
                            class="form-control"
                            value="{{ old('nama_lengkap_gelar', $admin->nama_lengkap_gelar) }}"
                            maxlength="150" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Email Kedinasan
                        </label>
                        <input type="email"
                            class="form-control bg-light"
                            value="{{ $admin->email }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Nomor Telepon / WhatsApp
                        </label>
                        <input type="text"
                            name="nomor_telepon"
                            class="form-control"
                            value="{{ old('nomor_telepon', $admin->nomor_telepon) }}"
                            maxlength="30">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            NIP / Nomor Induk Pegawai
                        </label>
                        <input type="text"
                            class="form-control bg-light"
                            value="{{ $admin->nip }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Role Pengguna
                        </label>
                        <input type="text"
                            class="form-control bg-light"
                            value="{{ $admin->peran === 'admin_ult'
                                ? 'Admin ULT (Super Access)'
                                : 'Admin Unit' }}"
                            readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Unit Terdaftar
                        </label>
                        <input type="text"
                            class="form-control bg-light"
                            value="{{ $admin->unit?->nama_unit ?? 'Belum ditentukan' }}"
                            readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Foto Profil
                        </label>
                        <input type="file"
                            name="avatar"
                            class="form-control"
                            accept=".jpg,.jpeg,.png,.webp">
                        <small class="text-muted">
                            Format JPG, PNG, atau WEBP. Maksimal 2 MB.
                        </small>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        Simpan Perubahan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- KEAMANAN AKUN --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Keamanan & Ubah Kata Sandi</h5>
        </div>

        <div class="card-body p-4">
            <p class="text-muted">
                Gunakan kata sandi yang kuat untuk menjaga keamanan akun.
            </p>

            <form
                action="{{ route('admin.profil.password') }}"
                method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            Kata Sandi Saat Ini
                        </label>
                        <input type="password"
                            name="kata_sandi_saat_ini"
                            class="form-control"
                            autocomplete="current-password"
                            required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            Kata Sandi Baru
                        </label>
                        <input type="password"
                            name="kata_sandi_baru"
                            class="form-control"
                            minlength="8"
                            autocomplete="new-password"
                            required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            Ulangi Kata Sandi Baru
                        </label>
                        <input type="password"
                            name="kata_sandi_baru_confirmation"
                            class="form-control"
                            minlength="8"
                            autocomplete="new-password"
                            required>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-dark">
                        Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

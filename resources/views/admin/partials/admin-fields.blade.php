@php($prefix = $form . '_')
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="{{ $prefix }}nama_lengkap_gelar">Nama Lengkap &amp; Gelar</label>
        <input id="{{ $prefix }}nama_lengkap_gelar" type="text" name="nama_lengkap_gelar"
               class="form-control" value="{{ $form === 'edit' ? '' : old('nama_lengkap_gelar') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $prefix }}email">Email Kedinasan</label>
        <input id="{{ $prefix }}email" type="email" name="email"
               class="form-control" value="{{ $form === 'edit' ? '' : old('email') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $prefix }}nomor_telepon">Nomor Telepon / WhatsApp</label>
        <input id="{{ $prefix }}nomor_telepon" type="text" name="nomor_telepon"
               class="form-control" value="{{ $form === 'edit' ? '' : old('nomor_telepon') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $prefix }}nip">NIP / Nomor Induk Pegawai</label>
        <input id="{{ $prefix }}nip" type="text" name="nip"
               class="form-control" value="{{ $form === 'edit' ? '' : old('nip') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $prefix }}peran">Role Pengguna</label>
        <select id="{{ $prefix }}peran" name="peran" class="form-select" required>
            <option value="">Pilih role</option>
            <option value="admin_ult" @selected(old('peran') === 'admin_ult')>Admin ULT (Super Access)</option>
            <option value="admin_unit" @selected(old('peran', 'admin_unit') === 'admin_unit')>Admin Unit</option>
        </select>
        <div class="form-text">Unit ditetapkan otomatis untuk role Admin ULT (Super Access).</div>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $prefix }}id_unit">Unit Terdaftar</label>
        <select id="{{ $prefix }}id_unit" name="id_unit" class="form-select" required>
            <option value="">Pilih unit</option>
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}" @selected(old('id_unit') == $unit->id)>{{ $unit->nama_unit }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label" for="{{ $prefix }}kata_sandi">Kata Sandi {{ $form === 'edit' ? '(opsional)' : '' }}</label>
        <input id="{{ $prefix }}kata_sandi" type="password" name="kata_sandi"
               class="form-control" {{ $form === 'edit' ? '' : 'required' }}>
    </div>
</div>
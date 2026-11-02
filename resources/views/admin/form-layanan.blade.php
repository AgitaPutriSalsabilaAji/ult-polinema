@extends('adminlte::page')

@section('title', $mode === 'edit' ? 'Edit Layanan' : 'Tambah Layanan')

@section('content_header')
    <h3 class="admin-page-title mb-1">Manajemen Informasi Layanan</h3>
    <p class="text-muted mb-0">
        Kelola Informasi Layanan yang ditampilkan kepada pengguna.
    </p>
@stop

@section('content')
@php
    $sections = old(
        'bagian',
        $mode === 'edit'
            ? $layanan->bagianLayanan->map(fn ($b) => [
                'id' => $b->id,
                'judul_bagian' => $b->judul_bagian,
                'konten' => $b->konten,
                'gambar_pendukung' => $b->gambar_pendukung,
            ])->toArray()
            : [[
                'judul_bagian' => '',
                'konten' => '',
                'gambar_pendukung' => null,
            ]]
    );

    if (empty($sections)) {
        $sections = [[
            'judul_bagian' => '',
            'konten' => '',
            'gambar_pendukung' => null,
        ]];
    }
@endphp

<div class="container-fluid py-2">

    {{-- Tombol Kembali --}}
    <div class="mb-3">
        <a href="{{ route('admin.layanan.index') }}"
        class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke daftar layanan
        </a>
    </div>

    <p class="text-secondary small mb-3">
        Lengkapi informasi layanan yang akan ditampilkan kepada pengguna.
    </p>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Periksa kembali isian berikut:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $mode === 'edit'
              ? route('admin.layanan.update', $layanan)
              : route('admin.layanan.store') }}"
          enctype="multipart/form-data">

        @csrf

        @if($mode === 'edit')
            @method('PUT')
        @endif

        {{-- INFORMASI DASAR --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0">
                    Informasi Dasar
                </h5>
            </div>

            <div class="card-body p-4">

                <div class="mb-3">
                    <label for="nama_layanan" class="form-label fw-semibold">
                        Nama Layanan <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           id="nama_layanan"
                           name="nama_layanan"
                           class="form-control"
                           maxlength="255"
                           required
                           value="{{ old('nama_layanan', $layanan->nama_layanan) }}"
                           placeholder="Contoh: Pengurusan Kartu Tanda Mahasiswa">
                </div>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label for="id_unit" class="form-label fw-semibold">
                            Unit Penanggung Jawab
                            <span class="text-danger">*</span>
                        </label>

                        <select name="id_unit" id="id_unit"
                                class="form-select" required>
                            <option value="">Pilih Unit</option>

                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}"
                                    @selected(old('id_unit', $layanan->id_unit) == $unit->id)>
                                    {{ $unit->nama_unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="estimasi_waktu" class="form-label fw-semibold">
                            Estimasi Waktu <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               id="estimasi_waktu"
                               name="estimasi_waktu"
                               class="form-control"
                               required
                               value="{{ old('estimasi_waktu', $layanan->estimasi_waktu) }}"
                               placeholder="Contoh: 3 Hari Kerja">
                    </div>

                    <div class="col-md-6">
                        <label for="tarif" class="form-label fw-semibold">
                            Tarif
                        </label>

                        <input type="text"
                               id="tarif"
                               name="tarif"
                               class="form-control"
                               value="{{ old('tarif', $layanan->tarif) }}"
                               placeholder="Contoh: Gratis">
                    </div>

                    <div class="col-md-6">
                        <label for="jam_operasional" class="form-label fw-semibold">
                            Jam Operasional
                        </label>

                        <input type="text"
                               id="jam_operasional"
                               name="jam_operasional"
                               class="form-control"
                               value="{{ old('jam_operasional', $layanan->jam_operasional) }}"
                               placeholder="Senin–Jumat, 08.00–16.00">
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-semibold">
                            Status
                        </label>

                        <select name="status" id="status"
                                class="form-select" required>
                            <option value="aktif"
                                @selected(old('status', $layanan->status ?: 'aktif') === 'aktif')>
                                Aktif
                            </option>

                            <option value="nonaktif"
                                @selected(old('status', $layanan->status) === 'nonaktif')>
                                Nonaktif
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="tautan_eksternal" class="form-label fw-semibold">
                            Tautan Eksternal
                        </label>

                        <input type="url"
                               id="tautan_eksternal"
                               name="tautan_eksternal"
                               class="form-control"
                               value="{{ old('tautan_eksternal', $layanan->tautan_eksternal) }}"
                               placeholder="https://...">
                    </div>

                    <div class="col-12">
                        <label for="deskripsi_singkat" class="form-label fw-semibold">
                            Deskripsi Singkat
                            <span class="text-danger">*</span>
                        </label>

                        <textarea id="deskripsi_singkat"
                                  name="deskripsi_singkat"
                                  class="form-control"
                                  rows="3"
                                  required
                                  placeholder="Tuliskan ringkasan layanan...">{{ old('deskripsi_singkat', $layanan->deskripsi_singkat) }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        {{-- BAGIAN INFORMASI --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0">
                    Informasi Layanan
                </h5>
            </div>

            <div class="card-body p-4">

                <p class="text-secondary small mb-3">
                    Tambahkan detail, persyaratan, prosedur, atau informasi
                    pendukung sesuai kebutuhan layanan.
                </p>

                <div id="bagian-container">

                    @foreach($sections as $i => $section)
                        <div class="bagian-item border rounded p-3 mb-3">

                            @if(!empty($section['id']))
                                <input type="hidden"
                                       name="bagian[{{ $i }}][id]"
                                       value="{{ $section['id'] }}">
                            @endif

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="bagian-title fw-bold mb-0">
                                    Bagian Informasi {{ $loop->iteration }}
                                </h6>

                                <button type="button"
                                        class="btn btn-outline-danger btn-sm remove-bagian">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Judul Informasi
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       name="bagian[{{ $i }}][judul_bagian]"
                                       class="form-control"
                                       maxlength="255"
                                       required
                                       value="{{ $section['judul_bagian'] ?? '' }}"
                                       placeholder="Contoh: Persyaratan Pengajuan">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Penjelasan
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="quill-editor">{!! $section['konten'] ?? '' !!}</div>

                                <textarea name="bagian[{{ $i }}][konten]"
                                          class="quill-content"
                                          data-field="konten"
                                          hidden></textarea>
                            </div>

                            @if(!empty($section['gambar_pendukung']))
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Gambar Saat Ini
                                    </label>

                                    <div>
                                        <img src="{{ asset('storage/'.$section['gambar_pendukung']) }}"
                                             alt="Gambar pendukung"
                                             style="max-width:220px;max-height:150px"
                                             class="img-thumbnail">
                                    </div>

                                    <small class="text-secondary">
                                        Unggah gambar baru jika ingin mengganti gambar.
                                    </small>
                                </div>
                            @endif

                            <div class="mb-2">
                                <label class="form-label fw-semibold">
                                    Gambar Pendukung (Opsional)
                                </label>

                                <input type="file"
                                       name="bagian[{{ $i }}][gambar]"
                                       class="form-control gambar-input"
                                       accept=".jpg,.jpeg,.png,.webp">

                                <small class="text-secondary">
                                    Format JPG, PNG, WEBP. Maksimal 5 MB.
                                </small>
                            </div>

                        </div>
                    @endforeach

                </div>

                <button type="button" id="tambah-bagian"
                        class="btn btn-outline-primary">
                    <i class="bi bi-plus-lg"></i>
                    Tambah Bagian Informasi
                </button>

            </div>
        </div>

        {{-- TOMBOL AKSI --}}
        <div class="d-flex justify-content-end gap-2 mb-4">

            <a href="{{ route('admin.layanan.index') }}"
               class="btn btn-outline-secondary">
                Batal
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>
                {{ $mode === 'edit' ? 'Simpan Perubahan' : 'Simpan Layanan' }}
            </button>

        </div>

    </form>
</div>

{{-- TEMPLATE BAGIAN BARU --}}
<template id="template-bagian">
    <div class="bagian-item border rounded p-3 mb-3">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="bagian-title fw-bold mb-0">
                Bagian Informasi
            </h6>

            <button type="button"
                    class="btn btn-outline-danger btn-sm remove-bagian">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">
                Judul Informasi <span class="text-danger">*</span>
            </label>

            <input type="text"
                   data-field="judul_bagian"
                   class="form-control"
                   maxlength="255"
                   required
                   placeholder="Contoh: Persyaratan Pengajuan">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">
                Penjelasan <span class="text-danger">*</span>
            </label>

            <div class="quill-editor"></div>

            <textarea data-field="konten"
                      class="quill-content"
                      hidden></textarea>
        </div>

        <div class="mb-2">
            <label class="form-label fw-semibold">
                Gambar Pendukung (Opsional)
            </label>

            <input type="file"
                   data-field="gambar"
                   class="form-control gambar-input"
                   accept=".jpg,.jpeg,.png,.webp">

            <small class="text-secondary">
                Format JPG, PNG, WEBP. Maksimal 5 MB.
            </small>
        </div>

    </div>
</template>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css"
      rel="stylesheet">

<style>
    /* Ukuran dan jarak halaman */
    .container-fluid {
        font-size: 14px;
    }

    .card {
        border-radius: 8px;
    }

    .card-header {
        border-bottom: 1px solid #edf0f4;
    }

    .card-title {
        color: #253858;
        font-size: 16px;
    }

    /* Form */
    .form-label {
        font-size: 14px;
        margin-bottom: 6px;
    }

    .form-control,
    .form-select {
        min-height: 38px;
        font-size: 14px;
        border-radius: 6px;
    }

    textarea.form-control {
        min-height: auto;
    }

    /* Bagian informasi */
    .bagian-item {
        background: #fff;
        border-color: #dee2e6 !important;
        border-radius: 8px !important;
    }

    .bagian-title {
        color: #253858;
        font-size: 15px;
    }

    #tambah-bagian {
        border-style: dashed;
    }

    /* Quill editor */
    .quill-editor {
        background: #fff;
        min-height: 150px;
        font-size: 14px;
    }

    .quill-editor .ql-editor {
        min-height: 110px;
        font-family: inherit;
        font-size: 14px;
        line-height: 1.6;
    }

    .quill-editor .ql-toolbar {
        border-radius: 7px 7px 0 0;
        background: #f8f9fa;
    }

    .quill-editor .ql-container {
        border-radius: 0 0 7px 7px;
    }

    .quill-editor.is-invalid .ql-toolbar,
    .quill-editor.is-invalid .ql-container {
        border-color: #dc3545;
    }

    /* Tombol */
    .btn {
        font-size: 14px;
        border-radius: 6px;
    }

    @media (max-width: 575.98px) {
        .card-body {
            padding: 16px !important;
        }
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('bagian-container');
    const template = document.getElementById('template-bagian');
    const form = container.closest('form');

    // INISIALISASI QUILL
    function initQuill(item) {

        const editorElement = item.querySelector('.quill-editor');

        if (!editorElement || editorElement._quill) {
            return;
        }

        const quill = new Quill(editorElement, {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic'],
                    [{ list: 'bullet' }, { list: 'ordered' }],
                    ['link']
                ]
            },
            placeholder: 'Tuliskan penjelasan secara lengkap...'
        });

        editorElement._quill = quill;

        const textarea = item.querySelector('.quill-content');

        quill.on('text-change', function () {
            textarea.value = quill.root.innerHTML;
            editorElement.classList.remove('is-invalid');
        });

        textarea.value = quill.root.innerHTML;
    }

    // RAPIIKAN BAGIAN
    function rapikanBagian() {

        container.querySelectorAll('.bagian-item').forEach((item, index) => {

            item.querySelector('.bagian-title').textContent =
                'Bagian Informasi ' + (index + 1);

            item.querySelectorAll('[name]').forEach(input => {
                input.name = input.name.replace(
                    /^bagian\[\d+\]/,
                    'bagian[' + index + ']'
                );
            });

            item.querySelectorAll('[data-field]').forEach(input => {
                input.name = 'bagian[' + index + '][' +
                    input.dataset.field + ']';
            });

            initQuill(item);
        });
    }

    rapikanBagian();

    // TAMBAH BAGIAN
    document.getElementById('tambah-bagian')
        .addEventListener('click', function () {

            container.appendChild(template.content.cloneNode(true));
            rapikanBagian();

        });

    // HAPUS BAGIAN
    container.addEventListener('click', function (event) {

        const button = event.target.closest('.remove-bagian');

        if (!button) return;

        if (container.querySelectorAll('.bagian-item').length <= 1) {
            alert('Minimal harus ada satu bagian informasi.');
            return;
        }

        button.closest('.bagian-item').remove();
        rapikanBagian();

    });

    // VALIDASI GAMBAR
    container.addEventListener('change', function (event) {

        const input = event.target;

        if (!input.matches('.gambar-input') || !input.files.length) {
            return;
        }

        const file = input.files[0];

        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!allowedTypes.includes(file.type)) {
            alert('Gunakan gambar JPG, PNG, atau WEBP.');
            input.value = '';
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran gambar maksimal 5 MB.');
            input.value = '';
        }

    });

    // SINKRONISASI DAN VALIDASI FORM
    form.addEventListener('submit', function (event) {

        let valid = true;
        let firstInvalidEditor = null;

        container.querySelectorAll('.bagian-item').forEach(item => {

            const editorElement = item.querySelector('.quill-editor');
            const textarea = item.querySelector('.quill-content');
            const quill = editorElement._quill;

            textarea.value = quill.root.innerHTML;

            const plainText = quill.getText().trim();

            if (!plainText) {

                valid = false;
                editorElement.classList.add('is-invalid');

                if (!firstInvalidEditor) {
                    firstInvalidEditor = editorElement;
                }

            } else {
                editorElement.classList.remove('is-invalid');
            }

        });

        if (!valid) {

            event.preventDefault();

            alert('Penjelasan pada setiap bagian informasi wajib diisi.');

            if (firstInvalidEditor) {

                firstInvalidEditor.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                firstInvalidEditor._quill.focus();

            }
        }

    });

});
</script>
@stop
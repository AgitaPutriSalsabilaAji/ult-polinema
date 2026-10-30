
@extends('adminlte::page')

@section('title', $mode === 'edit' ? 'Edit Layanan' : 'Tambah Layanan')

@section('content_header')
    <h1>Manajemen Informasi Layanan</h1>
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

<div class="container-fluid">
    <div class="mb-3">
        <a href="{{ route('admin.layanan.index') }}">
            Informasi Layanan
        </a>
        <span class="text-secondary">
            / {{ $mode === 'edit' ? 'Edit Layanan' : 'Tambah Layanan Baru' }}
        </span>
    </div>

    <p class="text-secondary">
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
        <div class="card">
            <div class="card-header">
                <h3 class="card-title fw-bold">Informasi Dasar</h3>
            </div>

            <div class="card-body">
                <div class="mb-3">
                    <label for="nama_layanan" class="form-label">
                        Nama Layanan <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           id="nama_layanan"
                           name="nama_layanan"
                           class="form-control"
                           maxlength="255"
                           required
                           value="{{ old(
                               'nama_layanan', $layanan->nama_layanan
                           ) }}"
                           placeholder="Contoh: Pengurusan Kartu Tanda Mahasiswa">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="id_unit" class="form-label">
                            Unit Penanggung Jawab
                            <span class="text-danger">*</span>
                        </label>

                        <select name="id_unit" id="id_unit"
                                class="form-select" required>
                            <option value="">Pilih Unit</option>

                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}"
                                    @selected(old(
                                        'id_unit', $layanan->id_unit
                                    ) == $unit->id)>
                                    {{ $unit->nama_unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="estimasi_waktu" class="form-label">
                            Estimasi Waktu
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               id="estimasi_waktu"
                               name="estimasi_waktu"
                               class="form-control"
                               required
                               value="{{ old(
                                   'estimasi_waktu',
                                   $layanan->estimasi_waktu
                               ) }}"
                               placeholder="Contoh: 3 Hari Kerja">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="tarif" class="form-label">Tarif</label>

                        <input type="text"
                               id="tarif"
                               name="tarif"
                               class="form-control"
                               value="{{ old('tarif', $layanan->tarif) }}"
                               placeholder="Contoh: Gratis">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="jam_operasional" class="form-label">
                            Jam Operasional
                        </label>

                        <input type="text"
                               id="jam_operasional"
                               name="jam_operasional"
                               class="form-control"
                               value="{{ old(
                                   'jam_operasional',
                                   $layanan->jam_operasional
                               ) }}"
                               placeholder="Senin–Jumat, 08.00–16.00">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>

                        <select name="status" id="status"
                                class="form-select" required>
                            <option value="aktif"
                                @selected(old(
                                    'status', $layanan->status ?: 'aktif'
                                ) === 'aktif')>
                                Aktif
                            </option>

                            <option value="nonaktif"
                                @selected(old(
                                    'status', $layanan->status
                                ) === 'nonaktif')>
                                Nonaktif
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="tautan_eksternal" class="form-label">
                            Tautan Eksternal
                        </label>

                        <input type="url"
                               id="tautan_eksternal"
                               name="tautan_eksternal"
                               class="form-control"
                               value="{{ old(
                                   'tautan_eksternal',
                                   $layanan->tautan_eksternal
                               ) }}"
                               placeholder="https://...">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deskripsi_singkat" class="form-label">
                        Deskripsi Singkat
                        <span class="text-danger">*</span>
                    </label>

                    <textarea id="deskripsi_singkat"
                              name="deskripsi_singkat"
                              class="form-control"
                              rows="3" required
                              placeholder="Tuliskan ringkasan layanan...">{{ old(
                                  'deskripsi_singkat',
                                  $layanan->deskripsi_singkat
                              ) }}</textarea>
                </div>
            </div>
        </div>

        {{-- BAGIAN INFORMASI --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title fw-bold">Informasi Layanan</h3>
            </div>

            <div class="card-body">
                <p class="text-secondary">
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

                            <div class="d-flex justify-content-between
                                        align-items-center mb-3">
                                <h5 class="bagian-title mb-0">
                                    Bagian Informasi {{ $loop->iteration }}
                                </h5>

                                <button type="button"
                                        class="btn btn-outline-danger btn-sm remove-bagian">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
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
                                <label class="form-label">
                                    Penjelasan
                                    <span class="text-danger">*</span>
                                </label>

                                <textarea name="bagian[{{ $i }}][konten]"
                                          class="form-control"
                                          rows="5"
                                          required
                                          placeholder="Tuliskan penjelasan secara lengkap...">{{ $section['konten'] ?? '' }}</textarea>
                            </div>

                            @if(!empty($section['gambar_pendukung']))
                                <div class="mb-3">
                                    <label class="form-label">
                                        Gambar saat ini
                                    </label>
                                    <div>
                                        <img
                                            src="{{ asset(
                                                'storage/'.$section['gambar_pendukung']
                                            ) }}"
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
                                <label class="form-label">
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

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('admin.layanan.index') }}"
               class="btn btn-secondary">
                Batal
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i>
                {{ $mode === 'edit' ? 'Simpan Perubahan' : 'Simpan Layanan' }}
            </button>
        </div>
    </form>
</div>

<template id="template-bagian">
    <div class="bagian-item border rounded p-3 mb-3">
        <div class="d-flex justify-content-between
                    align-items-center mb-3">
            <h5 class="bagian-title mb-0">Bagian Informasi</h5>

            <button type="button"
                    class="btn btn-outline-danger btn-sm remove-bagian">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </div>

        <div class="mb-3">
            <label class="form-label">
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
            <label class="form-label">
                Penjelasan <span class="text-danger">*</span>
            </label>

            <textarea data-field="konten"
                      class="form-control"
                      rows="5" required
                      placeholder="Tuliskan penjelasan secara lengkap..."></textarea>
        </div>

        <div class="mb-2">
            <label class="form-label">Gambar Pendukung (Opsional)</label>

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
<style>
    .bagian-item {
        background: #fff;
        border-color: #dee2e6 !important;
    }

    .bagian-title {
        color: #253858;
        font-size: 14px;
    }

    #tambah-bagian {
        border-style: dashed;
    }
</style>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('bagian-container');
    const template = document.getElementById('template-bagian');

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
        });
    }

    document.getElementById('tambah-bagian')
        .addEventListener('click', function () {
            container.appendChild(template.content.cloneNode(true));
            rapikanBagian();
        });

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

    rapikanBagian();
});
</script>
@stop

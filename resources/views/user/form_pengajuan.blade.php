<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengajuan Layanan | ULT Polinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/form_pengajuan.css', 'resources/js/form_pengajuan.js'])
</head>
<body>
    @include('user.navbar', ['theme' => 'purple', 'active' => 'services'])

    @php
        $selectedNamaLayanan = $selectedLayanan['nama'] ?? 'Layanan';
        $selectedSlug = $selectedLayanan['slug'] ?? null;
        $selectedUnit = $selectedLayanan['unit'] ?? 'Unit Layanan';
    @endphp

    <main class="form-pengajuan-page">
        <section class="form-pengajuan-header">
            <div class="container-xl">
                <h1>FORM PENGAJUAN LAYANAN</h1>
                <p>Lengkapi formulir berikut dengan data yang valid untuk mengajukan permohonan ke Unit Bagian {{ $selectedUnit }} Polinema.</p>
            </div>
        </section>

        <section class="form-pengajuan-shell">
            <div class="container-xl">
                <div class="form-card">
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('pengajuan.submit') }}" method="POST" class="pengajuan-form" enctype="multipart/form-data" novalidate>
                        @csrf

                        <div class="form-section">
                            <div class="section-header">
                                <span class="section-number">01.</span>
                                <h2>Data Pemohon</h2>
                            </div>
                            <p class="section-subtitle">Lengkapi data diri Anda agar proses pengajuan dapat diproses dengan cepat.</p>

                            <div class="field-grid">
                                <div class="field-group">
                                    <label for="jenis_pemohon">Jenis Pemohon <span class="required-mark">*</span></label>
                                    <select id="jenis_pemohon" name="jenis_pemohon" required>
                                        <option value="">Pilih jenis pemohon</option>
                                        <option value="Mahasiswa" {{ old('jenis_pemohon') === 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                        <option value="Dosen" {{ old('jenis_pemohon') === 'Dosen' ? 'selected' : '' }}>Dosen</option>
                                        <option value="Tenaga Kependidikan" {{ old('jenis_pemohon') === 'Tenaga Kependidikan' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                                        <option value="Alumni" {{ old('jenis_pemohon') === 'Alumni' ? 'selected' : '' }}>Alumni</option>
                                        <option value="Umum" {{ old('jenis_pemohon') === 'Umum' ? 'selected' : '' }}>Umum</option>
                                    </select>
                                </div>
                                <div class="field-group">
                                    <label for="nama_lengkap">Nama Lengkap <span class="required-mark">*</span></label>
                                    <input id="nama_lengkap" name="nama_lengkap" type="text" value="{{ old('nama_lengkap') }}" required>
                                </div>
                                <div class="field-group">
                                    <label for="nim_nip">NIM / NIP</label>
                                    <input id="nim_nip" name="nim_nip" type="text" value="{{ old('nim_nip') }}" placeholder="Opsional">
                                </div>
                                <div class="field-group">
                                    <label for="email">Email Aktif <span class="required-mark">*</span></label>
                                    <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                                </div>
                                <div class="field-group field-group--wa">
                                    <label for="nomor_telepon">Nomor WhatsApp Aktif <span class="required-mark">*</span></label>
                                    <input id="nomor_telepon" name="nomor_telepon" type="tel" value="{{ old('nomor_telepon') }}" required>
                                </div>
                                <div class="field-group field-group--empty" aria-hidden="true"></div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <span class="section-number">02.</span>
                                <h2>Detail Permohonan</h2>
                            </div>
                            <p class="section-subtitle">Informasi layanan yang dipilih dan detail kebutuhan Anda.</p>

                            <div class="detail-panel">
                                <div class="detail-panel__meta">
                                    <div class="meta-item">
                                        <span>Kategori</span>
                                        <strong>{{ $selectedLayanan['unit'] ?? 'Akademik' }}</strong>
                                    </div>
                                    <div class="meta-item meta-item--service">
                                        <span>Layanan Terpilih</span>
                                        <strong class="meta-item__value meta-item__value--service">{{ $selectedLayanan['nama'] ?? 'Pilih Layanan' }}</strong>
                                    </div>
                                    <div class="meta-item">
                                        <span>Unit Pengampu</span>
                                        <strong>{{ $selectedLayanan['unit'] ?? 'Unit Layanan' }}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="detail-panel__fields">
                                <input type="hidden" name="id_layanan" value="{{ old('id_layanan', $selectedLayanan['id'] ?? '') }}">

                                <div class="field-group field-group--full">
                                    <label for="subjek">Subjek / Keperluan Surat <span class="required-mark">*</span></label>
                                    <input id="subjek" name="subjek" type="text" value="{{ old('subjek') }}" required>
                                </div>

                                <div class="field-group field-group--full">
                                    <label for="deskripsi">Deskripsi Permohonan <span class="required-mark">*</span></label>
                                    <input id="deskripsi" name="deskripsi" type="text" value="{{ old('deskripsi') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <span class="section-number">03.</span>
                                <h2>Dokumen Pendukung <small>(Opsional)</small></h2>
                            </div>
                            <p class="section-subtitle">Lampiran dokumen pendukung (Maksimal 5 MB/file)</p>

                            <div class="upload-panel">
                                <label class="upload-zone" for="dokumen">
                                    <span class="upload-zone__icon" aria-hidden="true">📎</span>
                                    <span class="upload-zone__text">Tarik dokumen ke sini atau <span class="upload-zone__link">klik untuk memilih file</span></span>
                                    <small>Mendukung format PDF, JPG, JPEG, PNG (Maks 5 MB)</small>
                                </label>
                                <input id="dokumen" name="dokumen" type="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" data-max-size="5242880">
                            </div>

                            <div id="selectedFileList" class="selected-file-list" aria-live="polite"></div>
                        </div>

                        <div class="statement-box">
                            <input class="form-check-input" type="checkbox" id="pernyataan" name="pernyataan" required>
                            <label class="form-check-label" for="pernyataan">
                                Saya menyatakan dengan sebenarnya bahwa seluruh data dan dokumen yang saya berikan adalah benar dan dapat dipertanggungjawabkan sesuai ketentuan Politeknik Negeri Malang.
                            </label>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-back" onclick="window.history.back()"><span aria-hidden="true">←</span> Kembali ke Detail</button>
                            <button type="submit" class="btn btn-submit"><span aria-hidden="true">→</span> KIRIM PENGAJUAN &amp; DAPATKAN E-TIKET</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <div class="modal-confirm-backdrop" id="confirmModal" aria-hidden="true">
        <div class="modal-confirm" role="dialog" aria-modal="true" aria-labelledby="confirmModalTitle">
            <div class="modal-confirm__icon" aria-hidden="true">
                <i class="bi bi-shield-exclamation"></i>
            </div>
            <h3 id="confirmModalTitle">KIRIM PENGAJUAN LAYANAN?</h3>
            <p>
                Pastikan seluruh informasi data diri dan dokumen lampiran sudah benar sebelum permohonan diteruskan ke Bagian {{ $selectedUnit }}. Apakah Anda yakin ingin mengirim pengajuan ini?
            </p>
            <div class="modal-confirm__actions">
                <button type="button" class="btn btn-secondary btn-cancel" id="cancelConfirmBtn">BATAL</button>
                <button type="button" class="btn btn-primary btn-submit" id="confirmSubmitBtn">YA, KIRIM PENGAJUAN</button>
            </div>
        </div>
    </div>

    @include('user.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

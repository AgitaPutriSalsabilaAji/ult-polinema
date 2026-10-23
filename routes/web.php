<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FaqController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/admin', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/layanan', function (\Illuminate\Http\Request $request) {
    $kategori = $request->query('kategori', 'semua');

    $services = [
        ['Surat Keterangan Kuliah', 'Layanan penerbitan surat resmi yang menyatakan bahwa seseorang pernah menempuh pendidikan di Polinema.', 'Akademik'],
        ['Kunjungan Sekolah', 'Layanan informasi dan koordinasi kunjungan sekolah ke Polinema untuk mengenal lingkungan kampus dan program pendidikan.', 'Humas'],
        ['Pembayaran UKT', 'Layanan informasi dan panduan pembayaran UKT mahasiswa melalui metode pembayaran yang tersedia.', 'Keuangan'],
        ['Internal', 'Layanan yang ditujukan bagi sivitas akademika Polinema, meliputi mahasiswa, dosen, dan tenaga kependidikan.', 'Kepegawaian'],
        ['Beasiswa', 'Layanan informasi dan pengajuan beasiswa untuk membantu mahasiswa memperoleh dukungan biaya pendidikan.', 'Kemahasiswaan'],
        ['Ijazah Digital', 'Layanan penerbitan atau akses ijazah dalam format digital.', 'Akademik'],
        ['Legalisasi Digital', 'Layanan pengesahan salinan ijazah agar sesuai dengan dokumen aslinya.', 'Akademik'],
        ['Edufair', 'Layanan informasi pendidikan dan promosi program studi Polinema dalam kegiatan pameran pendidikan.', 'Humas'],
        ['Liputan Kegiatan', 'Layanan publikasi dan dokumentasi kegiatan resmi yang diselenggarakan oleh Polinema.', 'Humas'],
        ['Informasi SPMB', 'Layanan penyampaian informasi terkait Seleksi Penerimaan Mahasiswa Baru.', 'Humas'],
        ['Informasi & Pengaduan', 'Layanan penyampaian informasi, pertanyaan, kritik, dan pengaduan terkait layanan atau kegiatan Polinema.', 'Humas'],
    ];

    if ($kategori === 'humas') {
        $services = array_values(array_filter(
            $services,
            fn ($service) => $service[2] === 'Humas'
        ));
    }

    if ($request->filled('q')) {
        $keyword = strtolower($request->query('q'));

        $services = array_values(array_filter($services, function ($service) use ($keyword) {
            return str_contains(strtolower($service[0]), $keyword)
                || str_contains(strtolower($service[1]), $keyword);
        }));
    }

    return view('user.layanan.index', compact('services', 'kategori'));
})->name('layanan.index');


Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::view('/pengajuan', 'admin.pengajuan')->name('pengajuan');
    Route::view('/detail-pengajuan', 'admin.detail-pengajuan')->name('detail-pengajuan');
    Route::view('/tracking', 'admin.tracking')->name('tracking');
    Route::view('/layanan', 'admin.layanan')->name('layanan');
    Route::resource('faq', FaqController::class)->except(['create', 'show', 'edit']);
    Route::view('/rating', 'admin.rating')->name('rating');
    Route::view('/profil', 'admin.profil')->name('profil');
    Route::view('/daftar-admin', 'admin.daftar-admin')->name('daftar-admin');
});


Route::get('/layanan/{slug}', function (string $slug) {
    $daftarDetail = [
        'kunjungan-sekolah' => [
            'nama' => 'Kunjungan Sekolah',
            'unit' => 'Bagian Kerja Sama & Humas Polinema',
            'deskripsi' => 'Layanan penerimaan dan fasilitasi kunjungan sekolah ke Politeknik Negeri Malang untuk mengenalkan lingkungan kampus, program pendidikan, serta informasi penerimaan mahasiswa baru kepada siswa dan pihak sekolah.',
            'estimasi' => '1 - 3 Hari Kerja',
            'tarif' => 'Rp 0 (Tidak Dipungut Biaya / Gratis)',
            'jam' => 'Senin - Jumat, 08.00 - 16.00 WIB',
            'persyaratan' => [
                ['Data Identitas Pemohon', 'Informasi mengenai pihak atau sekolah yang mengajukan kunjungan.'],
                ['Nomor Induk Mahasiswa (NIM) Aktif', 'Informasi mengenai tujuan dan kebutuhan kunjungan ke Politeknik Negeri Malang.'],
                ['Dokumen Pendukung / Format Khusus', 'Informasi mengenai rencana kunjungan sekolah yang diajukan.'],
                ['Kartu Tanda Mahasiswa (KTM) Scan', 'Dokumen pendukung apabila diperlukan sesuai ketentuan layanan.'],
            ],
            'prosedur' => [
                'Pengguna memilih layanan Kunjungan Sekolah pada portal ULT.',
                'Pengguna membaca informasi dan persyaratan layanan.',
                'Pengguna menekan tombol Ajukan Layanan.',
                'Pengguna mengisi formulir e-ticket tanpa login.',
                'Sistem menghasilkan nomor unik E-Ticket.',
                'Permohonan diterima oleh Admin ULT dan diteruskan kepada Unit Kerja Sama & Humas.',
                'Unit pengampu memproses permohonan kunjungan.',
                'Pengguna memantau status permohonan melalui E-Ticket.',
            ],
            'dokumen' => [
                ['Informasi Pengajuan Kunjungan', 'PDF / JPG / PNG', 'Wajib'],
                ['Dokumen Pendukung Kunjungan', 'PDF / JPG', 'Wajib'],
                ['Surat / Dokumen Pendukung dari Sekolah', 'PDF / DOCX', 'Jika diperlukan'],
            ],
        ],

        'edufair' => [
            'nama' => 'Edufair',
            'unit' => 'Bagian Kerja Sama & Humas Polinema',
            'deskripsi' => 'Layanan informasi pendidikan dan promosi program studi Polinema dalam kegiatan pameran pendidikan.',
            'estimasi' => '1 - 3 Hari Kerja',
            'tarif' => 'Rp 0 (Gratis)',
            'jam' => 'Senin - Jumat, 08.00 - 16.00 WIB',
            'persyaratan' => [
                ['Identitas Pemohon', 'Informasi nama sekolah atau penyelenggara kegiatan.'],
                ['Informasi Kegiatan', 'Tanggal, lokasi, dan bentuk kegiatan Edufair.'],
                ['Surat Permohonan', 'Surat permohonan partisipasi apabila dipersyaratkan.'],
            ],
            'prosedur' => [
                'Pengguna memilih layanan Edufair.',
                'Pengguna membaca persyaratan layanan.',
                'Pengguna mengajukan permohonan melalui formulir E-Ticket.',
                'Sistem menghasilkan nomor E-Ticket.',
                'Admin meneruskan permohonan kepada unit pengampu.',
                'Unit pengampu meninjau permohonan.',
                'Pengguna memantau status permohonan melalui E-Ticket.',
            ],
            'dokumen' => [
                ['Informasi Kegiatan', 'PDF / JPG / PNG', 'Wajib'],
                ['Surat Permohonan', 'PDF', 'Jika diperlukan'],
            ],
        ],

        'liputan-kegiatan' => [
            'nama' => 'Liputan Kegiatan',
            'unit' => 'Bagian Kerja Sama & Humas Polinema',
            'deskripsi' => 'Layanan publikasi dan dokumentasi kegiatan resmi yang diselenggarakan oleh Politeknik Negeri Malang.',
            'estimasi' => '2 - 3 Hari Kerja',
            'tarif' => 'Rp 0 (Gratis)',
            'jam' => 'Senin - Jumat, 08.00 - 16.00 WIB',
            'persyaratan' => [
                ['Identitas Pemohon', 'Informasi unit atau pihak penyelenggara kegiatan.'],
                ['Informasi Kegiatan', 'Nama, waktu, tempat, dan deskripsi kegiatan.'],
                ['Dokumen Pendukung', 'Undangan, susunan acara, atau dokumen pendukung lainnya.'],
            ],
            'prosedur' => [
                'Pengguna memilih layanan Liputan Kegiatan.',
                'Pengguna membaca informasi persyaratan.',
                'Pengguna mengisi formulir pengajuan.',
                'Sistem membuat nomor E-Ticket.',
                'Admin meneruskan permohonan kepada Humas.',
                'Unit pengampu meninjau jadwal dan kebutuhan liputan.',
                'Pengguna memantau status pengajuan melalui E-Ticket.',
            ],
            'dokumen' => [
                ['Informasi Kegiatan', 'PDF / JPG / PNG', 'Wajib'],
                ['Undangan atau Susunan Acara', 'PDF / DOCX', 'Jika diperlukan'],
            ],
        ],

        'informasi-spmb' => [
            'nama' => 'Informasi SPMB',
            'unit' => 'Bagian Kerja Sama & Humas Polinema',
            'deskripsi' => 'Layanan penyampaian informasi terkait Seleksi Penerimaan Mahasiswa Baru, termasuk jalur seleksi dan prosedur pendaftaran.',
            'estimasi' => '1 - 3 Hari Kerja',
            'tarif' => 'Rp 0 (Gratis)',
            'jam' => 'Senin - Jumat, 08.00 - 16.00 WIB',
            'persyaratan' => [
                ['Identitas Pemohon', 'Informasi kontak pemohon untuk tindak lanjut.'],
                ['Pertanyaan SPMB', 'Pertanyaan mengenai jalur seleksi atau proses pendaftaran.'],
            ],
            'prosedur' => [
                'Pengguna memilih layanan Informasi SPMB.',
                'Pengguna membaca informasi layanan.',
                'Pengguna mengisi formulir pengajuan.',
                'Sistem menghasilkan nomor E-Ticket.',
                'Permohonan diteruskan kepada unit pengampu.',
                'Pengguna memantau status permohonan.',
            ],
            'dokumen' => [
                ['Rincian Pertanyaan atau Kebutuhan Informasi', 'PDF / JPG / PNG', 'Wajib'],
            ],
        ],

        'informasi-pengaduan' => [
            'nama' => 'Informasi & Pengaduan',
            'unit' => 'Bagian Kerja Sama & Humas Polinema',
            'deskripsi' => 'Layanan penyampaian informasi, pertanyaan, kritik, dan pengaduan terkait layanan atau kegiatan Polinema.',
            'estimasi' => '1 - 3 Hari Kerja',
            'tarif' => 'Rp 0 (Gratis)',
            'jam' => 'Senin - Jumat, 08.00 - 16.00 WIB',
            'persyaratan' => [
                ['Identitas dan Kontak Pemohon', 'Informasi yang diperlukan untuk tindak lanjut pengaduan.'],
                ['Rincian Informasi atau Pengaduan', 'Jelaskan pertanyaan, kritik, atau pengaduan secara jelas.'],
                ['Bukti Pendukung', 'Lampiran pendukung apabila tersedia dan relevan.'],
            ],
            'prosedur' => [
                'Pengguna memilih layanan Informasi & Pengaduan.',
                'Pengguna membaca informasi persyaratan.',
                'Pengguna mengisi formulir pengajuan.',
                'Sistem menghasilkan nomor E-Ticket.',
                'Admin meneruskan permohonan kepada unit terkait.',
                'Unit pengampu meninjau dan menindaklanjuti permohonan.',
                'Pengguna memantau status melalui E-Ticket.',
            ],
            'dokumen' => [
                ['Rincian Informasi atau Pengaduan', 'PDF / JPG / PNG', 'Wajib'],
                ['Bukti Pendukung', 'PDF / JPG / PNG', 'Jika tersedia'],
            ],
        ],
    ];

    abort_unless(isset($daftarDetail[$slug]), 404);

    $layanan = $daftarDetail[$slug];

    $terkait = collect($daftarDetail)
        ->except($slug)
        ->take(4);

    return view('user.layanan.detail', compact('layanan', 'terkait'));
})->name('layanan.detail');


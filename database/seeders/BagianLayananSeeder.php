<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BagianLayananSeeder extends Seeder
{
    public function run(): void
    {
        $sekarang = now();

        DB::table('bagian_layanan')->upsert(
            [
                // EDUFAIR - Layanan ID 1
                [
                    'id' => 1,
                    'id_layanan' => 1,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'EDUFAIR merupakan layanan informasi dan fasilitasi kegiatan pameran pendidikan yang bertujuan memperkenalkan program pendidikan Politeknik Negeri Malang kepada siswa, sekolah, dan masyarakat.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 2,
                    'id_layanan' => 1,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => "1. Pemohon mencantumkan identitas sekolah atau institusi.\n2. Pemohon menyampaikan tujuan dan bentuk partisipasi dalam kegiatan EDUFAIR.\n3. Pemohon mencantumkan jadwal dan lokasi kegiatan.\n4. Pemohon menyertakan surat permohonan resmi jika diperlukan.",
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 3,
                    'id_layanan' => 1,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => "1. Pemohon mengajukan permohonan partisipasi EDUFAIR melalui layanan yang tersedia.\n2. Petugas memeriksa kelengkapan informasi dan dokumen.\n3. Petugas melakukan koordinasi terkait jadwal dan bentuk partisipasi.\n4. Pemohon menerima informasi tindak lanjut dari petugas.",
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],

                // LIPUTAN KEGIATAN - Layanan ID 2
                [
                    'id' => 4,
                    'id_layanan' => 2,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan Liputan Kegiatan membantu koordinasi peliputan, dokumentasi, dan publikasi kegiatan yang diselenggarakan oleh unit kerja di lingkungan Politeknik Negeri Malang.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 5,
                    'id_layanan' => 2,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => "1. Pemohon mencantumkan nama dan unit penyelenggara kegiatan.\n2. Pemohon menyampaikan nama, tujuan, tanggal, waktu, dan lokasi kegiatan.\n3. Pemohon memberikan informasi mengenai susunan acara atau agenda kegiatan.\n4. Permohonan diajukan sebelum kegiatan dilaksanakan.",
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 6,
                    'id_layanan' => 2,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => "1. Pemohon mengajukan permohonan liputan dengan menyertakan informasi kegiatan.\n2. Petugas memeriksa permohonan dan ketersediaan jadwal peliputan.\n3. Petugas mengonfirmasi tindak lanjut kepada pemohon.\n4. Tim melakukan peliputan sesuai hasil koordinasi.\n5. Materi publikasi diproses sesuai kebijakan yang berlaku.",
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],

                // KUNJUNGAN SEKOLAH - Layanan ID 3
                [
                    'id' => 7,
                    'id_layanan' => 3,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan Kunjungan Sekolah memfasilitasi koordinasi kunjungan sekolah atau instansi ke Politeknik Negeri Malang untuk mengenal lingkungan kampus, fasilitas, dan program pendidikan.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 8,
                    'id_layanan' => 3,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => "1. Pemohon berasal dari sekolah atau institusi yang jelas identitasnya.\n2. Pemohon mencantumkan jumlah peserta dan pendamping.\n3. Pemohon menyampaikan tujuan, tanggal, dan waktu kunjungan.\n4. Pemohon menyertakan surat permohonan resmi yang ditandatangani pihak berwenang.",
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 9,
                    'id_layanan' => 3,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => "1. Sekolah mengirimkan surat permohonan kunjungan kepada pihak Politeknik Negeri Malang.\n2. Petugas memeriksa kelengkapan dokumen dan jadwal yang diajukan.\n3. Petugas melakukan koordinasi terkait ketersediaan waktu dan agenda kunjungan.\n4. Pemohon menerima konfirmasi mengenai pelaksanaan kunjungan.\n5. Kunjungan dilaksanakan sesuai jadwal yang telah disepakati.",
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],

                // PPID - Layanan ID 4
                [
                    'id' => 10,
                    'id_layanan' => 4,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Pejabat Pengelola Informasi dan Dokumentasi (PPID) merupakan layanan pengelolaan permohonan informasi publik dan dokumentasi sesuai ketentuan keterbukaan informasi yang berlaku.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 11,
                    'id_layanan' => 4,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => "1. Pemohon mengisi formulir permohonan informasi publik.\n2. Pemohon mencantumkan identitas yang dapat diverifikasi.\n3. Pemohon menjelaskan informasi yang dibutuhkan secara jelas.\n4. Pemohon melampirkan dokumen pendukung apabila diperlukan.",
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 12,
                    'id_layanan' => 4,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => "1. Pemohon mengajukan permohonan informasi melalui saluran pelayanan yang tersedia.\n2. Petugas memeriksa identitas dan kelengkapan permohonan.\n3. PPID menelaah permohonan sesuai ketentuan yang berlaku.\n4. Pemohon menerima pemberitahuan mengenai tindak lanjut atau jawaban permohonan.\n5. Apabila diperlukan, proses keberatan dilakukan sesuai prosedur yang berlaku.",
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],

                // SPMB - Layanan ID 5
                [
                    'id' => 13,
                    'id_layanan' => 5,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan informasi Seleksi Penerimaan Mahasiswa Baru (SPMB) menyediakan informasi mengenai jalur penerimaan, persyaratan, jadwal, tahapan seleksi, dan pengumuman resmi bagi calon mahasiswa Politeknik Negeri Malang.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 14,
                    'id_layanan' => 5,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => "1. Calon mahasiswa menyiapkan identitas diri dan dokumen akademik sesuai jalur seleksi.\n2. Calon mahasiswa membaca persyaratan pada pengumuman resmi SPMB.\n3. Calon mahasiswa menyiapkan dokumen pendukung sesuai ketentuan.\n4. Calon mahasiswa mengikuti jadwal dan tahapan seleksi yang ditetapkan.",
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 15,
                    'id_layanan' => 5,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => "1. Calon mahasiswa mengakses informasi penerimaan melalui kanal resmi SPMB.\n2. Calon mahasiswa memilih jalur seleksi yang sesuai.\n3. Calon mahasiswa melengkapi pendaftaran dan dokumen sesuai petunjuk.\n4. Calon mahasiswa mengikuti tahapan seleksi sesuai jadwal resmi.\n5. Calon mahasiswa memeriksa hasil seleksi melalui kanal pengumuman resmi.",
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
            ],
            ['id'],
            [
                'id_layanan',
                'judul_bagian',
                'konten',
                'gambar_pendukung',
                'urutan_bagian',
            ]
        );
    }
}

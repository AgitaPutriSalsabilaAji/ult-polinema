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
                [
                    'id' => 1,
                    'id_layanan' => 6,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan Pengajuan Cuti Akademik merupakan layanan akademik yang diperuntukkan bagi mahasiswa yang ingin mengajukan permohonan berhenti studi sementara (cuti akademik atau terminal) secara resmi. Melalui layanan ini, permohonan mahasiswa diproses secara administratif hingga status kemahasiswaannya diperbarui pada sistem akademik dan diterbitkan Surat Balasan Cuti Akademik resmi yang telah disahkan oleh pimpinan.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 2,
                    'id_layanan' => 6,
                    'judul_bagian' => 'Alur Tahapan Pengajuan',
                    'konten' => '1. Mahasiswa mengakses sistem ULT Polinema dan melihat beranda beserta struktur unit layanan.
                2. Mahasiswa memilih Unit Layanan Akademik (Layanan Pengajuan Cuti Akademik / Terminal) dan membaca detail layanan yang tampil.
                3. Mahasiswa mengisi form pengajuan serta mengunggah dokumen persyaratan untuk divalidasi kelengkapannya oleh sistem. Jika tidak valid, pengajuan dikembalikan untuk perbaikan.
                4. Setelah disubmit, mahasiswa memperoleh E-Ticket/nomor antrean dan pengajuan diteruskan oleh sistem ke siakad.polinema.ac.id.
                5. Mahasiswa menyiapkan surat permohonan Cuti Akademik, mengajukannya sesuai ketentuan tanda tangan dan persetujuan, lalu menyerahkannya ke Bagian Akademik dan Urusan Persuratan hingga terbit Lembar Disposisi.
                6. Setelah disposisi Direktur diterima, Admin Akademik Pusat memverifikasi kelengkapan dan ketentuan berkas. Jika tidak valid, berkas dapat dikembalikan.
                7. Admin menyetel status mahasiswa di SIAKAD dan menyusun surat balasan untuk diparaf Kasubag Akademik dan Ka.BAK serta ditandatangani Direktur.
                8. Admin mengentri nomor dan tanggal surat di SIAKAD, lalu mendistribusikan surat balasan cuti akademik hingga diterima mahasiswa dan proses dinyatakan selesai.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 3,
                    'id_layanan' => 6,
                    'judul_bagian' => 'Dokumen yang Dibutuhkan',
                    'konten' => '1. Formulir pengajuan dan dokumen persyaratan yang diisi serta diunggah melalui sistem ULT Polinema
2. E-Ticket atau nomor registrasi yang terbit setelah pengajuan daring disubmit
3. Surat Permohonan Cuti Akademik yang ditujukan kepada Direktur, ditandatangani oleh mahasiswa dan orang tua, serta diketahui oleh Kajur/KPS
4. Kelengkapan internal berupa data serta berkas permohonan dan Lembar Disposisi yang diproses selama tahap verifikasi hingga terbitnya surat balasan cuti akademik. ',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 4,
                    'id_layanan' => 7,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan Pengajuan Pengunduran Diri merupakan layanan akademik yang diperuntukkan bagi mahasiswa yang ingin mengajukan permohonan berhenti studi secara resmi.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 5,
                    'id_layanan' => 7,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => '1. Surat permohonan pengunduran diri kepada Direktur yang ditandatangani mahasiswa dan orang tua serta diketahui Kajur/KPS.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 6,
                    'id_layanan' => 7,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Mengajukan permohonan melalui ULT dan mengunggah persyaratan.
2. Menyiapkan surat permohonan kepada Direktur yang ditandatangani mahasiswa dan orang tua serta diketahui Kajur/KPS.
3. Menyerahkan surat ke Bagian Akademik dan Urusan Persuratan hingga terbit disposisi.
4. Admin memverifikasi berkas, memperbarui status di SIAKAD, dan memproses surat balasan.
5. Surat balasan dan tembusan didistribusikan kepada mahasiswa.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 7,
                    'id_layanan' => 8,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan penerbitan KTM pengganti bagi mahasiswa yang kehilangan KTM setelah verifikasi status mahasiswa, pelunasan UKT, dan dokumen kehilangan.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 8,
                    'id_layanan' => 8,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Formulir dan dokumen persyaratan ULT; E-Ticket; NIM; identitas untuk laporan kehilangan; surat keterangan kehilangan kepolisian; form permohonan yang ditandatangani Admin Prodi dan Ka.BAK; pas foto jika belum tersedia di SIAKAD; bukti pengambilan.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 9,
                    'id_layanan' => 8,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Mengajukan melalui ULT dan mengunggah persyaratan.
2. Meminta form surat permohonan kepada layanan akademik dengan menyebutkan NIM.
3. Admin memeriksa status mahasiswa dan UKT; Admin Prodi serta Ka.BAK menandatangani form.
4. Mengurus surat kehilangan dari kepolisian dan menyerahkan berkas.
5. Mengunggah foto KTM bila belum tersedia di SIAKAD.
6. Admin memvalidasi, mencetak KTM baru, dan menginformasikan pengambilan.
7. Mengambil KTM baru dengan bukti pengambilan.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 10,
                    'id_layanan' => 9,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan penggantian KTM yang rusak setelah pemeriksaan kondisi kartu, status mahasiswa, pelunasan UKT, dan kelengkapan dokumen.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 11,
                    'id_layanan' => 9,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Formulir dan dokumen persyaratan ULT; E-Ticket; KTM yang rusak; form permohonan yang ditandatangani Admin Prodi dan Ka.BAK; pas foto jika belum tersedia di SIAKAD; bukti pengambilan.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 12,
                    'id_layanan' => 9,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Mengajukan melalui ULT dan mengunggah persyaratan.
2. Meminta form surat permohonan dan menunjukkan KTM rusak.
3. Admin memeriksa status mahasiswa dan UKT; Admin Prodi serta Ka.BAK menandatangani form.
4. Menyerahkan surat permohonan yang telah ditandatangani.
5. Mengunggah foto KTM jika belum tersedia di SIAKAD.
6. Admin memvalidasi, mencetak KTM baru, dan menginformasikan pengambilan.
7. Mengambil KTM baru dengan bukti pengambilan.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 13,
                    'id_layanan' => 10,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan pembaruan atau perbaikan data mahasiswa pada sistem akademik dan pangkalan data nasional melalui verifikasi dokumen pendukung.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 14,
                    'id_layanan' => 10,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Formulir dan E-Ticket ULT; akun SIAKAD; KTP, KK, Akta Kelahiran, Ijazah; Surat Pernyataan PDM.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 15,
                    'id_layanan' => 10,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Mengajukan melalui ULT dan mengunggah persyaratan.
2. Melengkapi dokumen pendukung dan Surat PDM melalui SIAKAD.
3. Admin Akademik Pusat memvalidasi data.
4. Admin memperbarui data SIAKAD, menyiapkan surat pengantar, dan mengajukan pembaruan ke PDDIKTI.
5. PDDIKTI memverifikasi hingga perubahan data selesai.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 16,
                    'id_layanan' => 11,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan penerbitan surat resmi status perkuliahan mahasiswa aktif setelah verifikasi status aktif dan pelunasan UKT.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 17,
                    'id_layanan' => 11,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Formulir dan E-Ticket ULT; bukti pelunasan UKT; akun SIAKAD dan data mahasiswa.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 18,
                    'id_layanan' => 11,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Mengajukan melalui ULT.
2. Memastikan UKT lunas dan mengajukan permintaan SKK melalui menu Surat & Kuesioner di SIAKAD.
3. Admin memvalidasi status mahasiswa.
4. Admin memproses surat untuk ditandatangani Ka.BAK dan menginput nomor serta tanggal surat.
5. Mahasiswa mengunduh PDF melalui SIAKAD.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 19,
                    'id_layanan' => 12,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan surat keterangan kuliah bagi mahasiswa yang orang tuanya ASN, POLRI, TNI, atau pegawai BUMN untuk kebutuhan administrasi/tunjangan keluarga.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 20,
                    'id_layanan' => 12,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Formulir dan E-Ticket ULT; data mahasiswa dan data orang tua dengan status ASN/POLRI/TNI/BUMN.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 21,
                    'id_layanan' => 12,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Mengajukan melalui ULT.
2. Mengisi data orang tua melalui menu Surat dan Kuesioner di SIAKAD.
3. Admin memvalidasi data mahasiswa dan orang tua.
4. Admin memproses surat untuk ditandatangani Ka.BAK, menginput nomor dan tanggal, serta mengarsipkan.
5. Mahasiswa mengunduh surat dari SIAKAD.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 22,
                    'id_layanan' => 13,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan penerbitan surat keterangan resmi bahwa kewajiban administrasi mahasiswa telah diselesaikan.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 23,
                    'id_layanan' => 13,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Formulir dan E-Ticket ULT; akun SIAKAD dan data SKLA pada form permintaan surat.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 24,
                    'id_layanan' => 13,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Mengajukan melalui ULT.
2. Mengisi permintaan SKLA melalui SIAKAD.
3. Admin memvalidasi data.
4. Admin mencetak surat untuk ditandatangani Ka.BAK.
5. Setelah status selesai, mahasiswa mengunduh SKLA melalui SIAKAD.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 25,
                    'id_layanan' => 14,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan penerbitan surat resmi yang menyatakan mahasiswa tidak memiliki tanggungan yang belum diselesaikan.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 26,
                    'id_layanan' => 14,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Formulir dan E-Ticket ULT; data IPI/UKT, foto ijazah, SKKM, dan data alumni.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 27,
                    'id_layanan' => 14,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Mengajukan melalui ULT.
2. Menyiapkan data bebas tanggungan (IPI/UKT, foto ijazah, SKKM, dan data alumni) serta menghubungi petugas melalui WhatsApp.
3. Admin memvalidasi data dan memproses surat untuk ditandatangani Ka.BAK.
4. Surat diarsipkan dan dikirim dalam PDF melalui WhatsApp.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 28,
                    'id_layanan' => 15,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan surat resmi bagi mahasiswa atau mantan mahasiswa yang memerlukan keterangan riwayat pernah menempuh perkuliahan.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 29,
                    'id_layanan' => 15,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Formulir dan E-Ticket ULT; surat permohonan kepada Direktur; biodata; KHS.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 30,
                    'id_layanan' => 15,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Mengajukan melalui ULT.
2. Menyiapkan surat permohonan kepada Direktur, biodata, dan KHS.
3. Admin memvalidasi data dan menyusun konsep surat.
4. Surat ditandatangani Wakil Direktur I, diberi nomor dan stempel.
5. Pemohon mengambil surat secara online/offline sesuai ketentuan.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 31,
                    'id_layanan' => 16,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan pembaruan status mahasiswa yang meninggal dunia berdasarkan pengajuan wali dan verifikasi dokumen kematian.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 32,
                    'id_layanan' => 16,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Formulir dan E-Ticket ULT; Akta Kematian atau Surat Keterangan Kematian.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 33,
                    'id_layanan' => 16,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Wali mengajukan melalui ULT.
2. Menyerahkan akta atau surat keterangan kematian kepada Admin Akademik Prodi dan Pusat.
3. Admin memverifikasi dokumen.
4. Admin memperbarui status di SIAKAD dan memproses surat.
5. Surat diparaf Kasubag dan Ka.BAK, ditandatangani Direktur, lalu nomor dan tanggal dicatat.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 34,
                    'id_layanan' => 17,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan penerbitan surat keterangan resmi mengenai status kelulusan alumni setelah verifikasi dan disposisi.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 35,
                    'id_layanan' => 17,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Formulir dan E-Ticket ULT; surat permohonan kepada Direktur; data dan berkas alumni.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 36,
                    'id_layanan' => 17,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Alumni mengajukan melalui ULT.
2. Mengajukan surat permohonan kepada Direktur.
3. Direktur/Wadir I mendisposisikan ke Sub Bagian Akademik.
4. Admin memverifikasi data dan berkas, memproses surat, dan meminta legalisasi.
5. Surat diterbitkan setelah dilegalisasi.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 37,
                    'id_layanan' => 18,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan penerbitan surat keterangan bagi alumni yang kehilangan ijazah atau transkrip asli setelah verifikasi laporan kehilangan dan data kelulusan.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 38,
                    'id_layanan' => 18,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Formulir dan E-Ticket ULT; surat permohonan kepada Direktur; surat keterangan kehilangan kepolisian; data dan berkas alumni.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 39,
                    'id_layanan' => 18,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Alumni mengajukan melalui ULT.
2. Menyampaikan surat permohonan kepada Direktur beserta surat kehilangan kepolisian.
3. Direktur/Wadir I mendisposisikan ke Sub Bagian Akademik.
4. Admin memverifikasi data dan berkas, memproses surat, dan meminta legalisasi.
5. Surat diterbitkan dan disampaikan kepada alumni.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 40,
                    'id_layanan' => 19,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan surat keterangan bagi alumni yang menemukan kesalahan penulisan pada ijazah atau transkrip nilai.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 41,
                    'id_layanan' => 19,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Formulir dan E-Ticket ULT; surat permohonan kepada Direktur; dokumen pendukung terkait kesalahan penulisan.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 42,
                    'id_layanan' => 19,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Alumni mengajukan melalui ULT.
2. Menyiapkan dan mengajukan surat permohonan kepada Direktur.
3. Surat diproses melalui Bagian Akademik dan Urusan Persuratan hingga disposisi.
4. Admin memverifikasi berkas dan memproses surat untuk paraf serta tanda tangan.
5. Nomor dan tanggal surat diproses lalu surat diterima alumni.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 43,
                    'id_layanan' => 20,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan legalisasi ijazah alumni setelah pengisian Tracer Study dan verifikasi dokumen.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 44,
                    'id_layanan' => 20,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Formulir dan E-Ticket ULT; akun alumni dan bukti Tracer Study; maksimal 5 lembar fotokopi ijazah dan ijazah asli; bukti pengambilan.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 45,
                    'id_layanan' => 20,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Alumni mengajukan melalui ULT.
2. Mengisi Tracer Study dan menyerahkan fotokopi ijazah (maksimal 5 lembar), ijazah asli, serta bukti Tracer Study.
3. Admin memverifikasi berkas.
4. Dokumen diserahkan kepada Admin Wadir I untuk tanda tangan, lalu dibubuhi stempel.
5. Alumni mengambil legalisir dengan bukti pengambilan (maksimal 1 tahun).',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 46,
                    'id_layanan' => 21,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan pengiriman salinan ijazah digital resmi kepada alumni melalui email yang terdaftar setelah pemenuhan persyaratan kelulusan.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 47,
                    'id_layanan' => 21,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Formulir dan E-Ticket ULT; bukti bebas tanggungan; bukti pengambilan ijazah fisik, transkrip, dan SKPI; akun SIMFONI; email aktif.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 48,
                    'id_layanan' => 21,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Alumni mengajukan melalui ULT.
2. Menyelesaikan Bebas Tanggungan dan mengambil ijazah fisik, transkrip, serta SKPI.
3. Mengakses menu Ijazah Digital di SIMFONI untuk validasi persyaratan.
4. Mendaftarkan email tujuan dan mengonfirmasi pengiriman.
5. Mengunduh ijazah digital dari email dan mengonfirmasi penerimaan pada sistem.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 49,
                    'id_layanan' => 22,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan pendaftaran wisuda serta penyediaan perlengkapan, jadwal, nomor kursi, undangan digital, dan QR-Code wisudawan serta orang tua.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 50,
                    'id_layanan' => 22,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Formulir dan E-Ticket ULT; akun alumni; data pendaftaran dan email yang benar; jadwal dan nomor kursi, toga, undangan digital serta QR-Code.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 51,
                    'id_layanan' => 22,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Mengajukan melalui ULT.
2. Mendaftar pada alumni.polinema.ac.id melalui menu Jadwal Wisuda Saya dan mengisi data, termasuk email.
3. Admin memvalidasi data pendaftar.
4. Pendaftar mengecek jadwal, nomor kursi, dan jadwal pengambilan toga.
5. Mengunduh undangan digital dan QR-Code, mengikuti gladi bersih, dan prosesi wisuda.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 52,
                    'id_layanan' => 1,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan koordinasi dan fasilitasi kunjungan sekolah atau instansi ke Polinema melalui Unit Kerja Sama & Humas.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 53,
                    'id_layanan' => 1,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Perangkat internet; form konsultasi bila diperlukan; data instansi, jumlah peserta (maksimal 280), tanggal dan pilihan waktu; E-Ticket.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 54,
                    'id_layanan' => 1,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Pemohon membaca detail layanan dan mengajukan konsultasi bila diperlukan.
2. Mengisi form kunjungan berisi instansi, jumlah peserta (maksimal 280), tanggal, dan pilihan waktu 09.00 atau 13.00.
3. Admin ULT memverifikasi dan mendisposisikan ke Humas.
4. Humas mengecek jadwal, tempat, dan narasumber serta mengonfirmasi.
5. Surat balasan disusun dan ditandatangani Wadir IV.
6. Pemohon melakukan tracking tiket dan memberi rating.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 55,
                    'id_layanan' => 2,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan fasilitasi undangan partisipasi Polinema dalam kegiatan Edufair melalui peninjauan proposal dan persetujuan pimpinan.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 56,
                    'id_layanan' => 2,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Perangkat internet; proposal Edufair (nama acara, tanggal, lokasi, dan ketentuan partisipasi); E-Ticket.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 57,
                    'id_layanan' => 2,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Pemohon membaca informasi layanan.
                    2. Menyerahkan proposal Edufair berisi nama acara, tanggal, lokasi, dan ketentuan partisipasi melalui ULT.
                    3. Admin ULT memverifikasi dan mendisposisikan ke Humas.
                    4. Humas meneruskan proposal kepada pimpinan untuk ditinjau dan mendapat disposisi persetujuan.
                    5. Humas berpartisipasi sesuai persetujuan dan tiket diperbarui.
                    6. Pemohon melakukan tracking dan memberi rating.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 58,
                    'id_layanan' => 3,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan permohonan peliputan, dokumentasi, dan publikasi kegiatan kampus melalui Unit Kerja Sama & Humas.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 59,
                    'id_layanan' => 3,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Perangkat internet; form pengajuan berisi nama kegiatan, tanggal dan jam, lokasi, serta jenis dokumentasi; E-Ticket.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 60,
                    'id_layanan' => 3,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Pemohon membaca detail layanan.
2. Mengisi form yang berisi nama kegiatan, tanggal dan jam, lokasi, serta jenis dokumentasi.
3. Admin ULT memverifikasi dan mendisposisikan ke Humas.
4. Humas menugaskan tim liputan.
5. Tim melakukan liputan, menyusun dokumentasi, dan mempublikasikan melalui website/media sosial.
6. Status tiket diperbarui; pemohon melakukan tracking dan memberi rating.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 61,
                    'id_layanan' => 4,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan informasi dan konsultasi Penerimaan Mahasiswa Baru yang mengarahkan pemohon ke portal resmi PMB atau membantu menjawab pertanyaan.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 62,
                    'id_layanan' => 4,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Perangkat internet; form konsultasi jika memerlukan penjelasan tambahan; E-Ticket konsultasi.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 63,
                    'id_layanan' => 4,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Pemohon membaca informasi PMB pada ULT.
2. Jika informasi sudah cukup, sistem mengarahkan ke spmb.polinema.ac.id.
3. Jika membutuhkan informasi tambahan, pemohon mengisi form konsultasi.
4. Admin ULT memverifikasi dan mendisposisikan konsultasi ke Humas.
5. Humas memberikan jawaban; tiket diperbarui, kemudian pemohon melakukan tracking dan rating.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 64,
                    'id_layanan' => 5,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan akses informasi publik atau pengaduan yang mengarahkan pengguna ke portal resmi PPID Polinema.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 65,
                    'id_layanan' => 5,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Perangkat internet; data atau formulir permintaan informasi publik yang diisi melalui portal PPID.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 66,
                    'id_layanan' => 5,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Pengguna memilih layanan PPID pada ULT dan membaca informasi.
2. Sistem mengarahkan pengguna ke ppid.polinema.ac.id.
3. Pengguna melanjutkan permintaan informasi publik atau pengaduan melalui portal PPID.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 67,
                    'id_layanan' => 23,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan penelusuran alumni untuk pembaruan data pribadi dan riwayat pekerjaan melalui Sistem Informasi Alumni guna mendukung evaluasi dan peningkatan mutu Polinema.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 68,
                    'id_layanan' => 23,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Akun Sistem Informasi Alumni (NIM dan password SIAKAD); data pribadi terbaru; informasi riwayat pekerjaan.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 69,
                    'id_layanan' => 23,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Alumni masuk ke Sistem Informasi Alumni menggunakan NIM dan password alumni.
2. Memperbarui data pribadi, termasuk alamat dan nomor telepon.
3. Mengisi riwayat pekerjaan hingga Tracer Study selesai.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 70,
                    'id_layanan' => 24,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Layanan informasi, pendaftaran, seleksi, penetapan penerima, dan pencairan beasiswa yang melibatkan mahasiswa, Pokja Kemahasiswaan, pimpinan, dan mitra pemberi beasiswa.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 71,
                    'id_layanan' => 24,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Dokumen persyaratan sesuai program beasiswa; nomor rekening bank penerima setelah dinyatakan lolos.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 72,
                    'id_layanan' => 24,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => '1. Mahasiswa mengikuti pengumuman dan menyiapkan dokumen persyaratan.
2. Mendaftar; Pokja Kemahasiswaan memverifikasi dan melakukan evaluasi/seleksi.
3. Wadir III menetapkan penerima dan Direktur menerbitkan SK.
4. Penerima mengunggah nomor rekening.
5. Pokja memverifikasi rekening; mitra mengajukan dan mentransfer dana serta mengonfirmasi penerimaan.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 73,
                    'id_layanan' => 25,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Informasi organisasi mahasiswa Polinema, meliputi Lembaga Tinggi, Himpunan Mahasiswa Jurusan, dan Unit Kegiatan Mahasiswa.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 74,
                    'id_layanan' => 25,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Tidak ada dokumen pengajuan khusus; pengguna dapat melihat informasi organisasi yang tersedia.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 75,
                    'id_layanan' => 25,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => 'Informasi organisasi dikelompokkan menjadi:
                        1. Lembaga Tinggi: BEM dan DPM.
                        2. HMJ: HIMANIA, HMA, HME, HMM, HMS, HMTK, HMTI.
                        3. UKM: BKM, RISPOL/KIS, KMK St. John, KOMPEN, OPA Ganendra Giri, PASTI, PP, Radio PLFM, Menwa, Seni Theatrisic, Talitakum Family, UKM Olahraga, dan USMA Polinema.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 3,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 76,
                    'id_layanan' => 26,
                    'judul_bagian' => 'Tentang Layanan',
                    'konten' => 'Informasi program pengembangan mahasiswa Polinema melalui prestasi, soft skill/SKKM, kepemimpinan, dan Program Kreativitas Mahasiswa.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 1,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 77,
                    'id_layanan' => 26,
                    'judul_bagian' => 'Persyaratan Pengajuan',
                    'konten' => 'Tidak ada dokumen pengajuan khusus; persyaratan mengikuti program atau kegiatan masing-masing.',
                    'gambar_pendukung' => null,
                    'urutan_bagian' => 2,
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 78,
                    'id_layanan' => 26,
                    'judul_bagian' => 'Prosedur Pelayanan',
                    'konten' => 'Informasi pengembangan meliputi:
                    1. Mahasiswa Berprestasi (Pilmapres) dan fasilitasi kompetisi.
                    2. Pengembangan soft skill melalui SKKM.
                    3. Pengembangan kepemimpinan melalui LDK, LKMM, dan organisasi.
                    4. Program Kreativitas Mahasiswa (PKM), pendampingan PKM Center/UKM PP, bimbingan proposal, dan dukungan dosen.',
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

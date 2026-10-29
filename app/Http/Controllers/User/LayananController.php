<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class LayananController extends Controller
{
    private function dataLayanan(): array
    {
        return [
            [
                'nama'=>'Kunjungan Sekolah','slug'=>'kunjungan-sekolah','kategori'=>'Kerja Sama & Humas','unit'=>'Kerja Sama & Humas',
                'deskripsi'=>'Layanan informasi dan koordinasi kunjungan sekolah ke Polinema untuk mengenal lingkungan kampus dan program pendidikan.',
                'tentang'=>'Layanan ini digunakan untuk mengajukan permohonan kunjungan sekolah ke Politeknik Negeri Malang. Permohonan dilakukan melalui Unit Kerja Sama & Humas untuk mendukung kegiatan edukatif, pengenalan kampus, dan penyampaian informasi terkait Polinema.',
                'estimasi'=>'1–3 Hari Kerja','tarif'=>'Rp 0 (Tidak Dipungut Biaya / Gratis)','jam'=>'Senin–Jumat, 08.00–16.00 WIB',
                'persyaratan'=>[
                    ['judul'=>'Data Identitas Pemohon','deskripsi'=>'Informasi mengenai pihak atau sekolah yang mengajukan kunjungan.'],
                    ['judul'=>'Informasi Tujuan Kunjungan','deskripsi'=>'Informasi mengenai tujuan dan kebutuhan kunjungan ke Politeknik Negeri Malang.'],
                    ['judul'=>'Dokumen Pendukung / Format Khusus','deskripsi'=>'Informasi mengenai rencana kunjungan yang diajukan.'],
                    ['judul'=>'Surat Permohonan Sekolah','deskripsi'=>'Dokumen pendukung apabila diperlukan sesuai ketentuan layanan.']
                ],
                'prosedur'=>['Pengguna memilih layanan Kunjungan Sekolah pada portal ULT.','Pengguna membaca informasi dan persyaratan layanan.','Pengguna menekan tombol Ajukan Layanan.','Pengguna mengisi formulir e-ticket tanpa login.','Sistem menghasilkan nomor unik E-Ticket.','Permohonan diterima oleh Admin ULT dan diteruskan kepada Unit Kerja Sama & Humas.','Unit pengampu memproses permohonan kunjungan.','Pengguna memantau status permohonan melalui E-Ticket.'],
                'dokumen'=>[['nama'=>'Informasi Pengajuan Kunjungan','format'=>'PDF / JPG / PNG','ketentuan'=>'Wajib'],['nama'=>'Dokumen Pendukung Kunjungan','format'=>'PDF / JPG','ketentuan'=>'Wajib'],['nama'=>'Surat / Dokumen Pendukung dari Sekolah','format'=>'PDF / DOCX','ketentuan'=>'Jika diperlukan']]
            ],
            [
                'nama'=>'Edufair','slug'=>'edufair','kategori'=>'Kerja Sama & Humas','unit'=>'Kerja Sama & Humas',
                'deskripsi'=>'Layanan informasi pendidikan dan promosi program studi Polinema dalam kegiatan pameran pendidikan.',
                'tentang'=>'Layanan informasi dan koordinasi kegiatan Edufair untuk memperkenalkan Politeknik Negeri Malang, program studi, serta informasi pendidikan kepada masyarakat.',
                'estimasi'=>'1–3 Hari Kerja','tarif'=>'Rp 0 (Tidak Dipungut Biaya / Gratis)','jam'=>'Senin–Jumat, 08.00–16.00 WIB',
                'persyaratan'=>[['judul'=>'Informasi Acara','deskripsi'=>'Informasi mengenai penyelenggara dan kegiatan Edufair.'],['judul'=>'Kebutuhan Informasi','deskripsi'=>'Informasi mengenai program studi atau materi promosi yang dibutuhkan.'],['judul'=>'Pengajuan Kegiatan','deskripsi'=>'Informasi jadwal dan bentuk partisipasi yang diajukan.']],
                'prosedur'=>['Pengguna memilih layanan Edufair pada portal ULT.','Pengguna membaca informasi dan persyaratan layanan.','Pengguna menekan tombol Ajukan Layanan.','Pengguna mengisi formulir e-ticket tanpa login.','Sistem menghasilkan nomor unik E-Ticket.','Admin ULT meneruskan permohonan kepada Unit Kerja Sama & Humas.','Unit pengampu meninjau permohonan Edufair.','Pengguna memantau status permohonan melalui E-Ticket.'],
                'dokumen'=>[['nama'=>'Informasi Acara Edufair','format'=>'PDF / JPG / PNG','ketentuan'=>'Wajib'],['nama'=>'Surat Permohonan','format'=>'PDF','ketentuan'=>'Wajib']]
            ],
            [
                'nama'=>'Liputan Kegiatan','slug'=>'liputan-kegiatan','kategori'=>'Kerja Sama & Humas','unit'=>'Kerja Sama & Humas',
                'deskripsi'=>'Layanan publikasi dan dokumentasi kegiatan resmi yang diselenggarakan oleh Polinema.',
                'tentang'=>'Layanan untuk menyampaikan permohonan publikasi dan dokumentasi kegiatan resmi Politeknik Negeri Malang melalui Unit Kerja Sama & Humas.',
                'estimasi'=>'2–3 Hari Kerja','tarif'=>'Rp 0 (Tidak Dipungut Biaya / Gratis)','jam'=>'Senin–Jumat, 08.00–16.00 WIB',
                'persyaratan'=>[['judul'=>'Informasi Kegiatan','deskripsi'=>'Informasi mengenai nama, waktu, tempat, dan tujuan kegiatan.'],['judul'=>'Jadwal Kegiatan','deskripsi'=>'Jadwal pelaksanaan kegiatan yang akan diliput.'],['judul'=>'Lokasi Kegiatan','deskripsi'=>'Lokasi kegiatan yang akan diliput oleh tim Humas.'],['judul'=>'Jenis Dokumentasi','deskripsi'=>'Informasi mengenai kebutuhan foto, video, atau publikasi berita.']],
                'prosedur'=>['Pengguna memilih layanan Liputan Kegiatan pada portal ULT.','Pengguna membaca informasi dan persyaratan layanan.','Pengguna menekan tombol Ajukan Layanan.','Pengguna mengisi formulir e-ticket tanpa login.','Sistem menghasilkan nomor unik E-Ticket.','Admin ULT meneruskan permohonan kepada Unit Kerja Sama & Humas.','Unit pengampu meninjau jadwal dan kebutuhan liputan.','Pengguna memantau status permohonan melalui E-Ticket.'],
                'dokumen'=>[['nama'=>'Informasi Kegiatan','format'=>'PDF / DOCX','ketentuan'=>'Wajib'],['nama'=>'Jadwal dan Lokasi Kegiatan','format'=>'PDF / JPG','ketentuan'=>'Wajib']]
            ],
            [
                'nama'=>'Informasi SPMB','slug'=>'informasi-spmb','kategori'=>'Kerja Sama & Humas','unit'=>'Kerja Sama & Humas',
                'deskripsi'=>'Layanan penyediaan informasi terkait Seleksi Penerimaan Mahasiswa Baru (SPMB), termasuk jalur seleksi dan prosedur pendaftaran.',
                'tentang'=>'Layanan penyampaian informasi kepada masyarakat mengenai Seleksi Penerimaan Mahasiswa Baru Politeknik Negeri Malang, termasuk jalur seleksi, persyaratan, jadwal, dan prosedur pendaftaran.',
                'estimasi'=>'1–3 Hari Kerja','tarif'=>'Rp 0 (Tidak Dipungut Biaya / Gratis)','jam'=>'Senin–Jumat, 08.00–16.00 WIB',
                'persyaratan'=>[['judul'=>'Identitas Pemohon','deskripsi'=>'Informasi identitas pihak yang mengajukan pertanyaan.'],['judul'=>'Informasi Jalur Seleksi','deskripsi'=>'Jalur atau program penerimaan mahasiswa baru yang ingin diketahui.'],['judul'=>'Pertanyaan SPMB','deskripsi'=>'Rincian informasi pendaftaran yang dibutuhkan.']],
                'prosedur'=>['Pengguna memilih layanan Informasi SPMB pada portal ULT.','Pengguna membaca informasi dan persyaratan layanan.','Pengguna menekan tombol Ajukan Layanan.','Pengguna mengisi formulir e-ticket tanpa login.','Sistem menghasilkan nomor unik E-Ticket.','Admin ULT meneruskan pertanyaan kepada unit terkait.','Unit pengampu menyiapkan informasi yang dibutuhkan.','Pengguna memantau status permohonan melalui E-Ticket.'],
                'dokumen'=>[['nama'=>'Rincian Pertanyaan SPMB','format'=>'PDF / DOCX','ketentuan'=>'Jika diperlukan']]
            ],
            [
                'nama'=>'Informasi & Pengaduan','slug'=>'informasi-pengaduan','kategori'=>'Kerja Sama & Humas','unit'=>'Kerja Sama & Humas',
                'deskripsi'=>'Layanan penyampaian informasi, pertanyaan, kritik, dan pengaduan terkait layanan atau kegiatan Polinema.',
                'tentang'=>'Layanan untuk menyampaikan permintaan informasi, pertanyaan, kritik, saran, atau pengaduan terkait pelayanan dan kegiatan Politeknik Negeri Malang.',
                'estimasi'=>'1–3 Hari Kerja','tarif'=>'Rp 0 (Tidak Dipungut Biaya / Gratis)','jam'=>'Senin–Jumat, 08.00–16.00 WIB',
                'persyaratan'=>[['judul'=>'Data Identitas Pemohon','deskripsi'=>'Informasi identitas yang diperlukan untuk tindak lanjut permohonan.'],['judul'=>'Informasi Kontak Pemohon','deskripsi'=>'Kontak yang dapat digunakan untuk menyampaikan tindak lanjut.'],['judul'=>'Dokumen Pendukung','deskripsi'=>'Bukti atau informasi pendukung yang berkaitan dengan permohonan.']],
                'prosedur'=>['Pengguna memilih layanan Informasi & Pengaduan pada portal ULT.','Pengguna membaca informasi dan persyaratan layanan.','Pengguna menekan tombol Ajukan Layanan.','Pengguna mengisi formulir e-ticket tanpa login.','Sistem menghasilkan nomor unik E-Ticket.','Admin ULT meninjau dan meneruskan permohonan kepada unit terkait.','Unit pengampu menindaklanjuti permohonan.','Pengguna memantau status permohonan melalui E-Ticket.'],
                'dokumen'=>[['nama'=>'Informasi atau Rincian Pengaduan','format'=>'PDF / DOCX','ketentuan'=>'Wajib'],['nama'=>'Dokumen Pendukung','format'=>'PDF / JPG / PNG','ketentuan'=>'Jika diperlukan']]
            ],
            [
                'nama'=>'Surat Keterangan Kuliah','slug'=>'surat-keterangan-kuliah','kategori'=>'Akademik','unit'=>'Akademik',
                'deskripsi'=>'Layanan penerbitan surat resmi yang menyatakan bahwa seseorang merupakan mahasiswa aktif di Polinema.',
                'tentang'=>'Layanan penerbitan surat keterangan kuliah bagi mahasiswa yang membutuhkan dokumen resmi dari Politeknik Negeri Malang.',
                'estimasi'=>'1–3 Hari Kerja','tarif'=>'Tidak dipungut biaya','jam'=>'Senin–Jumat, 08.00–16.00 WIB',
                'persyaratan'=>[['judul'=>'Identitas Mahasiswa','deskripsi'=>'Data diri mahasiswa yang mengajukan surat.'],['judul'=>'Nomor Induk Mahasiswa','deskripsi'=>'NIM yang dapat diverifikasi oleh unit akademik.']],
                'prosedur'=>['Pengguna memilih layanan Surat Keterangan Kuliah.','Pengguna membaca persyaratan.','Pengguna mengisi formulir pengajuan E-Ticket.','Admin memeriksa kelengkapan permohonan.','Unit akademik memproses surat.','Pengguna memantau status melalui E-Ticket.'],
                'dokumen'=>[['nama'=>'Identitas Mahasiswa','format'=>'PDF / JPG','ketentuan'=>'Wajib'],['nama'=>'Dokumen Pendukung','format'=>'PDF','ketentuan'=>'Jika diperlukan']]
            ],
            [
                'nama'=>'Pembayaran UKT','slug'=>'pembayaran-ukt','kategori'=>'Keuangan','unit'=>'Keuangan',
                'deskripsi'=>'Layanan informasi dan panduan pembayaran UKT mahasiswa melalui metode pembayaran yang tersedia.',
                'tentang'=>'Layanan informasi terkait tagihan, jadwal, dan prosedur pembayaran Uang Kuliah Tunggal mahasiswa Polinema.',
                'estimasi'=>'1–3 Hari Kerja','tarif'=>'Sesuai tagihan UKT','jam'=>'Senin–Jumat, 08.00–16.00 WIB',
                'persyaratan'=>[['judul'=>'Identitas Mahasiswa','deskripsi'=>'NIM dan data mahasiswa untuk pengecekan informasi.'],['judul'=>'Rincian Pertanyaan','deskripsi'=>'Jelaskan kendala atau informasi pembayaran yang dibutuhkan.']],
                'prosedur'=>['Pengguna memilih layanan Pembayaran UKT.','Pengguna membaca informasi layanan.','Pengguna mengirimkan pertanyaan melalui E-Ticket.','Admin meneruskan pertanyaan ke unit keuangan.','Unit terkait memeriksa informasi pembayaran.','Pengguna memantau status melalui E-Ticket.'],
                'dokumen'=>[['nama'=>'Identitas Mahasiswa','format'=>'PDF / JPG','ketentuan'=>'Wajib'],['nama'=>'Bukti Tagihan atau Pembayaran','format'=>'PDF / JPG / PNG','ketentuan'=>'Jika diperlukan']]
            ],
            [
                'nama'=>'Internal','slug'=>'internal','kategori'=>'Kepegawaian','unit'=>'Kepegawaian',
                'deskripsi'=>'Layanan yang ditujukan bagi sivitas akademika Polinema, meliputi mahasiswa, dosen, dan tenaga kependidikan.',
                'tentang'=>'Layanan internal untuk membantu penyampaian permintaan informasi dan kebutuhan administrasi sivitas akademika sesuai kewenangan unit terkait.',
                'estimasi'=>'1–3 Hari Kerja','tarif'=>'Sesuai ketentuan layanan','jam'=>'Senin–Jumat, 08.00–16.00 WIB',
                'persyaratan'=>[['judul'=>'Identitas Pemohon','deskripsi'=>'Informasi identitas sivitas akademika.'],['judul'=>'Rincian Kebutuhan','deskripsi'=>'Uraikan permintaan atau kebutuhan layanan internal.']],
                'prosedur'=>['Pengguna memilih layanan Internal.','Pengguna membaca persyaratan.','Pengguna mengisi formulir E-Ticket.','Admin memverifikasi permohonan.','Permohonan diteruskan ke unit terkait.','Pengguna memantau status melalui E-Ticket.'],
                'dokumen'=>[['nama'=>'Identitas Pemohon','format'=>'PDF / JPG','ketentuan'=>'Wajib'],['nama'=>'Dokumen Pendukung','format'=>'PDF / DOCX','ketentuan'=>'Jika diperlukan']]
            ],
            [
                'nama'=>'Kenaikan Jabatan & Pangkat','slug'=>'kenaikan-jabatan-pangkat','kategori'=>'Kepegawaian','unit'=>'Kepegawaian',
                'deskripsi'=>'Layanan pengajuan dan informasi proses kenaikan jabatan serta pangkat bagi pegawai Polinema.',
                'tentang'=>'Layanan ini membantu pegawai dalam memahami prosedur, persyaratan, dan alur pengajuan kenaikan jabatan serta pangkat sesuai ketentuan yang berlaku di Polinema.',
                'estimasi'=>'3–5 Hari Kerja','tarif'=>'Sesuai ketentuan instansi','jam'=>'Senin–Jumat, 08.00–16.00 WIB',
                'persyaratan'=>[['judul'=>'Identitas Pegawai','deskripsi'=>'Data diri pegawai yang melakukan pengajuan kenaikan jabatan atau pangkat.'],['judul'=>'Dokumen Pendukung','deskripsi'=>'Dokumen prestasi, SK jabatan, dan bukti penilaian kinerja yang relevan.'],['judul'=>'Rincian Pengajuan','deskripsi'=>'Uraikan jenis kenaikan, masa kerja, dan kebutuhan administrasi yang diajukan.']],
                'prosedur'=>['Pengguna memilih layanan Kenaikan Jabatan & Pangkat.','Pengguna membaca persyaratan dan tata cara pengajuan.','Pengguna mengirim permohonan melalui E-Ticket.','Admin meninjau kelengkapan dokumen.','Unit Kepegawaian memproses pengajuan.','Pengguna memantau status melalui E-Ticket.'],
                'dokumen'=>[['nama'=>'Identitas Pegawai','format'=>'PDF / JPG','ketentuan'=>'Wajib'],['nama'=>'SK Jabatan / Pangkat Terakhir','format'=>'PDF','ketentuan'=>'Wajib'],['nama'=>'Dokumen Pendukung Lainnya','format'=>'PDF / DOCX','ketentuan'=>'Jika diperlukan']]
            ],
            [
                'nama'=>'Prosedur Cuti','slug'=>'prosedur-cuti','kategori'=>'Kepegawaian','unit'=>'Kepegawaian',
                'deskripsi'=>'Layanan informasi dan pengajuan cuti pegawai sesuai ketentuan dan prosedur yang berlaku di Polinema.',
                'tentang'=>'Layanan ini memfasilitasi pegawai dalam memahami proses pengajuan cuti, persyaratan administrasi, dan alur persetujuan di Unit Kepegawaian.',
                'estimasi'=>'2–4 Hari Kerja','tarif'=>'Sesuai ketentuan instansi','jam'=>'Senin–Jumat, 08.00–16.00 WIB',
                'persyaratan'=>[['judul'=>'Identitas Pegawai','deskripsi'=>'Data diri pegawai yang mengajukan cuti.'],['judul'=>'Jenis Cuti','deskripsi'=>'Jenis cuti yang diajukan dan alasan cuti yang relevan.'],['judul'=>'Dokumen Pendukung','deskripsi'=>'Dokumen pendukung seperti surat keterangan atau bukti kebutuhan cuti.']],
                'prosedur'=>['Pengguna memilih layanan Prosedur Cuti.','Pengguna membaca persyaratan dan ketentuan cuti.','Pengguna mengisi formulir E-Ticket.','Admin meninjau kelengkapan berkas.','Unit Kepegawaian memproses persetujuan.','Pengguna memantau status melalui E-Ticket.'],
                'dokumen'=>[['nama'=>'Identitas Pegawai','format'=>'PDF / JPG','ketentuan'=>'Wajib'],['nama'=>'Surat Permohonan Cuti','format'=>'PDF','ketentuan'=>'Wajib'],['nama'=>'Dokumen Pendukung','format'=>'PDF / DOCX','ketentuan'=>'Jika diperlukan']]
            ],
            [
                'nama'=>'Permohonan Tugas Belajar Dosen','slug'=>'permohonan-tugas-belajar-dosen','kategori'=>'Kepegawaian','unit'=>'Kepegawaian',
                'deskripsi'=>'Layanan informasi dan pengajuan tugas belajar dosen sesuai ketentuan dan prosedur Polinema.',
                'tentang'=>'Layanan ini membantu dosen dalam memahami tata cara pengajuan tugas belajar, serta persyaratan administrasi yang dibutuhkan untuk proses persetujuan.',
                'estimasi'=>'3–5 Hari Kerja','tarif'=>'Sesuai ketentuan instansi','jam'=>'Senin–Jumat, 08.00–16.00 WIB',
                'persyaratan'=>[['judul'=>'Identitas Dosen','deskripsi'=>'Data diri dosen yang mengajukan tugas belajar.'],['judul'=>'Dokumen Pendidikan','deskripsi'=>'Izin belajar, surat pengantar, dan dokumen akademik yang relevan.'],['judul'=>'Rencana Studi','deskripsi'=>'Informasi program studi, universitas, dan durasi tugas belajar yang diajukan.']],
                'prosedur'=>['Pengguna memilih layanan Permohonan Tugas Belajar Dosen.','Pengguna membaca persyaratan dan ketentuan.','Pengguna mengisi formulir E-Ticket.','Admin menilai kelengkapan dokumen.','Unit Kepegawaian meninjau pengajuan.','Pengguna memantau status melalui E-Ticket.'],
                'dokumen'=>[['nama'=>'Identitas Dosen','format'=>'PDF / JPG','ketentuan'=>'Wajib'],['nama'=>'Surat Pengantar / Persetujuan','format'=>'PDF','ketentuan'=>'Wajib'],['nama'=>'Dokumen Program Studi','format'=>'PDF / DOCX','ketentuan'=>'Jika diperlukan']]
            ],
            [
                'nama'=>'Pengaktifan Kembali dari Tugas Belajar','slug'=>'pengaktifan-kembali-dari-tugas-belajar','kategori'=>'Kepegawaian','unit'=>'Kepegawaian',
                'deskripsi'=>'Layanan informasi dan pengajuan pengaktifan kembali pegawai atau dosen setelah menyelesaikan tugas belajar.',
                'tentang'=>'Layanan ini mendukung proses pengembalian pegawai atau dosen ke unit kerja setelah menyelesaikan masa tugas belajar sesuai ketentuan dan persetujuan.',
                'estimasi'=>'3–5 Hari Kerja','tarif'=>'Sesuai ketentuan instansi','jam'=>'Senin–Jumat, 08.00–16.00 WIB',
                'persyaratan'=>[['judul'=>'Identitas Pegawai','deskripsi'=>'Data diri pegawai atau dosen yang akan diaktifkan kembali.'],['judul'=>'Dokumen Tugas Belajar','deskripsi'=>'Dokumen penugasan, kelulusan, dan persetujuan yang relevan.'],['judul'=>'Rencana Penempatan','deskripsi'=>'Informasi kebutuhan penempatan kerja setelah pengaktifan kembali.']],
                'prosedur'=>['Pengguna memilih layanan Pengaktifan Kembali dari Tugas Belajar.','Pengguna membaca ketentuan dan persyaratan.','Pengguna mengirim permohonan melalui E-Ticket.','Admin memeriksa dokumen berkas.','Unit Kepegawaian menilai kesiapan penempatan.','Pengguna memantau status melalui E-Ticket.'],
                'dokumen'=>[['nama'=>'Identitas Pegawai','format'=>'PDF / JPG','ketentuan'=>'Wajib'],['nama'=>'Dokumen Tugas Belajar / Kelulusan','format'=>'PDF','ketentuan'=>'Wajib'],['nama'=>'Surat Pengantar Penempatan','format'=>'PDF / DOCX','ketentuan'=>'Jika diperlukan']]
            ],
            [
                'nama'=>'Kontrak Kerja Pegawai','slug'=>'kontrak-kerja-pegawai','kategori'=>'Kepegawaian','unit'=>'Kepegawaian',
                'deskripsi'=>'Layanan informasi dan pengajuan kontrak kerja pegawai sesuai ketentuan administrasi kepegawaian Polinema.',
                'tentang'=>'Layanan ini memfasilitasi proses administrasi kontrak kerja pegawai termasuk pemahaman persyaratan, alur pengajuan, dan penetapan masa kerja.',
                'estimasi'=>'3–5 Hari Kerja','tarif'=>'Sesuai ketentuan instansi','jam'=>'Senin–Jumat, 08.00–16.00 WIB',
                'persyaratan'=>[['judul'=>'Identitas Pegawai','deskripsi'=>'Data diri pegawai yang mengajukan kontrak kerja.'],['judul'=>'Dokumen Pengelolaan SDM','deskripsi'=>'Berkas administrasi dan lampiran yang relevan untuk kontrak kerja.'],['judul'=>'Rincian Masa Kontrak','deskripsi'=>'Jelaskan jenis kontrak, jangka waktu, dan kebutuhan pengajuan.']],
                'prosedur'=>['Pengguna memilih layanan Kontrak Kerja Pegawai.','Pengguna membaca persyaratan dan ketentuan.','Pengguna mengisi formulir E-Ticket.','Admin meninjau kelengkapan berkas.','Unit Kepegawaian memproses kontrak kerja.','Pengguna memantau status melalui E-Ticket.'],
                'dokumen'=>[['nama'=>'Identitas Pegawai','format'=>'PDF / JPG','ketentuan'=>'Wajib'],['nama'=>'Dokumen Administrasi Kontrak','format'=>'PDF / DOCX','ketentuan'=>'Wajib'],['nama'=>'Lampiran Pendukung','format'=>'PDF / JPG / PNG','ketentuan'=>'Jika diperlukan']]
            ],
            [
                'nama'=>'Kenaikan Pangkat PNS','slug'=>'kenaikan-pangkat-pns','kategori'=>'Kepegawaian','unit'=>'Kepegawaian',
                'deskripsi'=>'Layanan informasi dan pengajuan kenaikan pangkat Pegawai Negeri Sipil sesuai ketentuan administrasi dan evaluasi.',
                'tentang'=>'Layanan ini membantu PNS Polinema dalam memahami tata cara, persyaratan, dan dokumen yang diperlukan untuk pengajuan kenaikan pangkat.',
                'estimasi'=>'3–5 Hari Kerja','tarif'=>'Sesuai ketentuan instansi','jam'=>'Senin–Jumat, 08.00–16.00 WIB',
                'persyaratan'=>[['judul'=>'Identitas Pegawai','deskripsi'=>'Data diri Pegawai Negeri Sipil yang mengajukan kenaikan pangkat.'],['judul'=>'Dokumen Kepangkatan','deskripsi'=>'Dokumen bukti pangkat terakhir, penilaian kinerja, dan data kepangkatan yang relevan.'],['judul'=>'Rincian Pengajuan','deskripsi'=>'Jelaskan masa kerja, jenis kenaikan pangkat, dan kebutuhan administrasi.']],
                'prosedur'=>['Pengguna memilih layanan Kenaikan Pangkat PNS.','Pengguna membaca persyaratan dan tata cara.','Pengguna mengisi formulir E-Ticket.','Admin menilai kelengkapan berkas.','Unit Kepegawaian memproses pengajuan.','Pengguna memantau status melalui E-Ticket.'],
                'dokumen'=>[['nama'=>'Identitas Pegawai','format'=>'PDF / JPG','ketentuan'=>'Wajib'],['nama'=>'SK Pangkat Terakhir','format'=>'PDF','ketentuan'=>'Wajib'],['nama'=>'Dokumen Pendukung KP / Penilaian','format'=>'PDF / DOCX','ketentuan'=>'Jika diperlukan']]
            ],
            [
                'nama'=>'Beasiswa','slug'=>'beasiswa','kategori'=>'Kemahasiswaan','unit'=>'Kemahasiswaan',
                'deskripsi'=>'Layanan informasi dan pengajuan beasiswa untuk membantu mahasiswa memperoleh dukungan biaya pendidikan.',
                'tentang'=>'Layanan informasi mengenai program beasiswa, persyaratan, jadwal, dan prosedur pengajuan yang tersedia bagi mahasiswa Polinema.',
                'estimasi'=>'1–3 Hari Kerja','tarif'=>'Tidak dipungut biaya','jam'=>'Senin–Jumat, 08.00–16.00 WIB',
                'persyaratan'=>[['judul'=>'Identitas Mahasiswa','deskripsi'=>'Data mahasiswa yang ingin memperoleh informasi beasiswa.'],['judul'=>'Nama Program Beasiswa','deskripsi'=>'Sebutkan program beasiswa yang ingin ditanyakan.']],
                'prosedur'=>['Pengguna memilih layanan Beasiswa.','Pengguna membaca persyaratan dan informasi.','Pengguna mengirimkan pertanyaan atau permohonan.','Admin meneruskan permohonan ke unit kemahasiswaan.','Unit terkait memberikan tindak lanjut.','Pengguna memantau status melalui E-Ticket.'],
                'dokumen'=>[['nama'=>'Identitas Mahasiswa','format'=>'PDF / JPG','ketentuan'=>'Wajib'],['nama'=>'Dokumen Persyaratan Beasiswa','format'=>'PDF','ketentuan'=>'Jika diperlukan']]
            ],
            [
                'nama'=>'Ijazah Digital','slug'=>'ijazah-digital','kategori'=>'Akademik','unit'=>'Akademik',
                'deskripsi'=>'Layanan penerbitan atau akses ijazah dalam format digital.',
                'tentang'=>'Layanan bantuan informasi terkait akses, pemeriksaan, atau tindak lanjut dokumen ijazah digital lulusan Polinema.',
                'estimasi'=>'3–5 Hari Kerja','tarif'=>'Sesuai ketentuan yang berlaku','jam'=>'Senin–Jumat, 08.00–16.00 WIB',
                'persyaratan'=>[['judul'=>'Identitas Lulusan','deskripsi'=>'Nama lengkap dan NIM atau nomor identitas lulusan.'],['judul'=>'Informasi Tahun Lulus','deskripsi'=>'Tahun kelulusan untuk membantu pemeriksaan data.']],
                'prosedur'=>['Pengguna memilih layanan Ijazah Digital.','Pengguna membaca persyaratan.','Pengguna mengisi formulir E-Ticket.','Admin memeriksa kelengkapan informasi.','Unit akademik memeriksa data lulusan.','Pengguna memantau status melalui E-Ticket.'],
                'dokumen'=>[['nama'=>'Identitas Lulusan','format'=>'PDF / JPG','ketentuan'=>'Wajib'],['nama'=>'Dokumen Pendukung','format'=>'PDF','ketentuan'=>'Jika diperlukan']]
            ],
            [
                'nama'=>'Legalisasi Digital','slug'=>'legalisasi-digital','kategori'=>'Akademik','unit'=>'Akademik',
                'deskripsi'=>'Layanan pengesahan salinan ijazah agar sesuai dengan dokumen aslinya.',
                'tentang'=>'Layanan informasi dan permohonan legalisasi dokumen akademik sesuai prosedur dan ketentuan Polinema.',
                'estimasi'=>'3–5 Hari Kerja','tarif'=>'Sesuai ketentuan yang berlaku','jam'=>'Senin–Jumat, 08.00–16.00 WIB',
                'persyaratan'=>[['judul'=>'Identitas Pemohon','deskripsi'=>'Informasi identitas pemohon legalisasi.'],['judul'=>'Dokumen yang Dilegalisasi','deskripsi'=>'Dokumen akademik yang akan diajukan untuk legalisasi.']],
                'prosedur'=>['Pengguna memilih layanan Legalisasi Digital.','Pengguna memeriksa persyaratan.','Pengguna mengirim permohonan melalui E-Ticket.','Admin memeriksa kelengkapan dokumen.','Unit akademik memproses permohonan.','Pengguna memantau status melalui E-Ticket.'],
                'dokumen'=>[['nama'=>'Salinan Ijazah atau Dokumen Akademik','format'=>'PDF','ketentuan'=>'Wajib'],['nama'=>'Identitas Pemohon','format'=>'PDF / JPG','ketentuan'=>'Wajib']]
            ],
            [
                'nama'=>'Undangan Wisuda','slug'=>'undangan-wisuda','kategori'=>'Akademik','unit'=>'Akademik',
                'deskripsi'=>'Layanan informasi dan bantuan terkait undangan pelaksanaan wisuda Polinema.',
            ],
            [
                'nama'=>'Ijazah Hilang','slug'=>'ijazah-hilang','kategori'=>'Akademik','unit'=>'Akademik',
                'deskripsi'=>'Layanan informasi mengenai prosedur tindak lanjut apabila ijazah hilang.',
            ],
            [
                'nama'=>'Ijazah Salah','slug'=>'ijazah-salah','kategori'=>'Akademik','unit'=>'Akademik',
                'deskripsi'=>'Layanan informasi mengenai perbaikan data atau kesalahan pada ijazah.',
            ],
            [
                'nama'=>'Legalisasi Ijazah','slug'=>'legalisasi-ijazah','kategori'=>'Akademik','unit'=>'Akademik',
                'deskripsi'=>'Layanan informasi legalisasi salinan ijazah sesuai ketentuan Polinema.',
            ],
            [
                'nama'=>'Surat Keterangan Alumni','slug'=>'surat-keterangan-alumni','kategori'=>'Akademik','unit'=>'Akademik',
                'deskripsi'=>'Layanan penerbitan informasi dan surat keterangan bagi alumni Polinema.',
            ],
            [
                'nama'=>'Cuti atau Pengunduran Diri','slug'=>'cuti-pengunduran-diri','kategori'=>'Akademik','unit'=>'Akademik',
                'deskripsi'=>'Layanan informasi prosedur pengajuan cuti akademik atau pengunduran diri.',
            ],
            [
                'nama'=>'KTM Hilang atau Rusak','slug'=>'ktm-hilang-atau-rusak','kategori'=>'Akademik','unit'=>'Akademik',
                'deskripsi'=>'Layanan informasi penggantian kartu tanda mahasiswa yang hilang atau rusak.',
            ],
            [
                'nama'=>'Perubahan Data Mahasiswa','slug'=>'perubahan-data-mahasiswa','kategori'=>'Akademik','unit'=>'Akademik',
                'deskripsi'=>'Layanan informasi pengajuan perubahan data mahasiswa pada sistem akademik.',
            ],
            [
                'nama'=>'Surat Keterangan Pernah Kuliah','slug'=>'surat-keterangan-pernah-kuliah','kategori'=>'Akademik','unit'=>'Akademik',
                'deskripsi'=>'Layanan penerbitan informasi surat keterangan pernah menempuh pendidikan di Polinema.',
            ],
            [
                'nama'=>'Bebas Tanggungan','slug'=>'bebas-tanggungan','kategori'=>'Akademik','unit'=>'Akademik',
                'deskripsi'=>'Layanan informasi pemenuhan dan pemeriksaan bebas tanggungan akademik.',
            ],
            [
                'nama'=>'Surat Keterangan Lulus (SKL)','slug'=>'surat-keterangan-lulus-skl','kategori'=>'Akademik','unit'=>'Akademik',
                'deskripsi'=>'Layanan informasi penerbitan Surat Keterangan Lulus bagi mahasiswa yang memenuhi ketentuan.',
            ],
            [
                'nama'=>'Surat Model C','slug'=>'surat-model-c','kategori'=>'Akademik','unit'=>'Akademik',
                'deskripsi'=>'Layanan informasi pengurusan surat Model C sesuai prosedur akademik yang berlaku.',
            ],
            [
                'nama'=>'Pembaruan Status Karena Kematian','slug'=>'pembaruan-status-karena-kematian','kategori'=>'Akademik','unit'=>'Akademik',
                'deskripsi'=>'Layanan informasi pembaruan status administrasi mahasiswa sesuai dokumen pendukung.',
            ],
            [
                'nama'=>'Pengajuan Uang UKT','slug'=>'pengajuan-uang-ukt','kategori'=>'Keuangan','unit'=>'Keuangan',
                'deskripsi'=>'Layanan informasi pengajuan terkait Uang Kuliah Tunggal (UKT) sesuai ketentuan.',
            ],
            [
                'nama'=>'Tagihan UKT','slug'=>'tagihan-ukt','kategori'=>'Keuangan','unit'=>'Keuangan',
                'deskripsi'=>'Layanan informasi pemeriksaan tagihan Uang Kuliah Tunggal mahasiswa.',
            ],
            [
                'nama'=>'Pengangsuran UKT','slug'=>'pengangsuran-ukt','kategori'=>'Keuangan','unit'=>'Keuangan',
                'deskripsi'=>'Layanan informasi prosedur pengajuan angsuran pembayaran UKT.',
            ],
            [
                'nama'=>'Tracer Study','slug'=>'tracer-study','kategori'=>'Kemahasiswaan','unit'=>'Kemahasiswaan',
                'deskripsi'=>'Layanan informasi tracer study untuk pendataan alumni dan penelusuran lulusan.',
            ],
            [
                'nama'=>'Organisasi Mahasiswa','slug'=>'organisasi-mahasiswa','kategori'=>'Kemahasiswaan','unit'=>'Kemahasiswaan',
                'deskripsi'=>'Layanan informasi terkait organisasi dan kegiatan kemahasiswaan Polinema.',
            ],
            [
                'nama'=>'Pengembangan Mahasiswa','slug'=>'pengembangan-mahasiswa','kategori'=>'Kemahasiswaan','unit'=>'Kemahasiswaan',
                'deskripsi'=>'Layanan informasi program pengembangan minat, bakat, dan kompetensi mahasiswa.',
            ],
            [
                'nama'=>'Informasi & Pengaduan PPID','slug'=>'informasi-pengaduan-ppid','kategori'=>'Kerja Sama & Humas','unit'=>'Kerja Sama & Humas',
                'deskripsi'=>'Layanan penyampaian informasi, pertanyaan, kritik, dan pengaduan terkait layanan atau kegiatan Polinema.',
                'tentang'=>'Layanan untuk menyampaikan permintaan informasi publik atau pengaduan yang perlu ditindaklanjuti oleh unit berwenang di Polinema.',
                'estimasi'=>'1–3 Hari Kerja','tarif'=>'Tidak dipungut biaya','jam'=>'Senin–Jumat, 08.00–16.00 WIB',
                'persyaratan'=>[['judul'=>'Data Identitas Pemohon','deskripsi'=>'Identitas pemohon untuk kebutuhan tindak lanjut.'],['judul'=>'Rincian Informasi atau Pengaduan','deskripsi'=>'Jelaskan informasi yang diminta atau pengaduan yang disampaikan.'],['judul'=>'Dokumen Pendukung','deskripsi'=>'Lampirkan dokumen pendukung jika diperlukan.']],
                'prosedur'=>['Pengguna memilih layanan Informasi & Pengaduan PPID.','Pengguna membaca persyaratan.','Pengguna mengisi formulir E-Ticket.','Admin memeriksa dan meneruskan permohonan.','Unit terkait menindaklanjuti permohonan.','Pengguna memantau status melalui E-Ticket.'],
                'dokumen'=>[['nama'=>'Rincian Permintaan Informasi atau Pengaduan','format'=>'PDF / DOCX','ketentuan'=>'Wajib'],['nama'=>'Dokumen Pendukung','format'=>'PDF / JPG / PNG','ketentuan'=>'Jika diperlukan']]
            ],
        ];
    }

    private function slugLayananDenganHalamanDetail(): array
    {
        return [
            'kunjungan-sekolah',
            'edufair',
            'liputan-kegiatan',
            'informasi-spmb',
            'informasi-pengaduan',
        ];
    }

    public function beranda(Request $request): View
    {
        $kategoriLayanan = [
            'akademik' => 'Akademik',
            'keuangan' => 'Keuangan',
            'kemahasiswaan' => 'Kemahasiswaan',
            'humas' => 'Kerja Sama & Humas',
            'kepegawaian' => 'Kepegawaian',
        ];

        $kategori = $request->query('kategori', 'humas');
        $kategori = is_string($kategori) ? strtolower($kategori) : 'humas';

        if (!array_key_exists($kategori, $kategoriLayanan)) {
            $kategori = 'humas';
        }

        $slugsAktif = $this->slugLayananDenganHalamanDetail();
        $all = collect($this->dataLayanan())
            ->reject(fn ($item) => $item['slug'] === 'informasi-pengaduan-ppid')
            ->values();
        $searchServices = $all;

        $jumlahKategori = [];
        foreach ($kategoriLayanan as $key => $namaKategori) {
            $jumlahKategori[$key] = $all->where('kategori', $namaKategori)->count();
        }

        $services = $all
            ->when($kategori === 'humas', fn ($items) => $items->filter(
                fn ($item) => in_array($item['slug'], $slugsAktif, true)
            ))
            ->where('kategori', $kategoriLayanan[$kategori])
            ->values();

        return view('user.beranda', compact(
            'services',
            'searchServices',
            'kategori',
            'jumlahKategori',
            'slugsAktif'
        ));
    }

    public function index(Request $request)
    {
        // PPID tidak ditampilkan pada daftar Humas agar jumlahnya tetap
        // sesuai desain yang disepakati: lima layanan Humas.
        $all = collect($this->dataLayanan())
            ->reject(fn ($item) => $item['slug'] === 'informasi-pengaduan-ppid')
            ->values();

        $map = [
            'semua' => null,
            'akademik' => 'Akademik',
            'keuangan' => 'Keuangan',
            'kemahasiswaan' => 'Kemahasiswaan',
            'humas' => 'Kerja Sama & Humas',
            'kepegawaian' => 'Kepegawaian',
        ];

        $kategori = strtolower($request->query('kategori', 'semua'));
        $keyword = trim($request->query('q', ''));

        if (!array_key_exists($kategori, $map)) {
            $kategori = 'semua';
        }

        // Jumlah tab dihitung dari data yang benar-benar tersedia,
        // bukan angka statis yang dapat berbeda dari isi daftar.
        $jumlahKategori = ['semua' => $all->count()];
        foreach ($map as $key => $namaKategori) {
            if ($key === 'semua') {
                continue;
            }

            $jumlahKategori[$key] = $all->where('kategori', $namaKategori)->count();
        }

        $totalLayanan = $kategori === 'semua'
            ? $all->count()
            : $all->where('kategori', $map[$kategori])->count();

        $hasil = $all
            ->when($kategori !== 'semua', function ($items) use ($map, $kategori) {
                return $items->where('kategori', $map[$kategori]);
            })
            ->when($keyword !== '', function ($items) use ($keyword) {
                return $items->filter(function ($item) use ($keyword) {
                    $teksCari = Str::lower($item['nama'] . ' ' . $item['deskripsi']);
                    return Str::contains($teksCari, Str::lower($keyword));
                });
            })
            ->values();

        $autocompleteServices = $all
            ->when($kategori !== 'semua', function ($items) use ($map, $kategori) {
                return $items->where('kategori', $map[$kategori]);
            })
            ->values();

        // Sepuluh kartu per halaman. Halaman yang tersedia mengikuti jumlah data.
        $perPage = 10;
        $currentPage = max(1, $request->integer('page', 1));
        $lastPage = max(1, (int) ceil($hasil->count() / $perPage));
        $currentPage = min($currentPage, $lastPage);

        $services = new LengthAwarePaginator(
            $hasil->forPage($currentPage, $perPage)->values(),
            $hasil->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('user.layanan.daftar_layanan', compact(
            'services',
            'kategori',
            'keyword',
            'jumlahKategori',
            'totalLayanan',
            'autocompleteServices'
        ));
    }

    public function detail(string $slug)
    {
        $all = collect($this->dataLayanan())
            ->reject(fn ($item) => $item['slug'] === 'informasi-pengaduan-ppid')
            ->values();
        $layanan = $all->firstWhere('slug', $slug);

        if (! $layanan) {
            abort(404);
        }

        $namaLayananDatabase = match ($slug) {
            'informasi-spmb' => 'SPMB',
            'informasi-pengaduan' => 'PPID',
            default => $layanan['nama'],
        };

        $layananDatabase = Layanan::query()
            ->with([
                'unit',
                'bagianLayanan' => function ($query) {
                    $query->whereNotNull('judul_bagian')
                        ->where('judul_bagian', '<>', '')
                        ->whereNotNull('konten')
                        ->where('konten', '<>', '')
                        ->orderBy('urutan_bagian')
                        ->orderBy('id');
                },
            ])
            ->whereRaw('LOWER(nama_layanan) = ?', [
                mb_strtolower($namaLayananDatabase),
            ])
            ->first();

        $bagian = $layananDatabase?->bagianLayanan ?? collect();

        if ($layananDatabase) {
            $layanan['nama'] = $layananDatabase->nama_layanan;
            $layanan['deskripsi'] = $layananDatabase->deskripsi_singkat;
            $layanan['unit'] = $layananDatabase->unit?->nama_unit;
            $layanan['estimasi'] = $layananDatabase->estimasi_waktu;
            $layanan['tarif'] = $layananDatabase->tarif;
            $layanan['jam'] = $layananDatabase->jam_operasional;
        }

        return view('user.layanan.detail_layanan', compact('layanan', 'bagian'));
    }
}

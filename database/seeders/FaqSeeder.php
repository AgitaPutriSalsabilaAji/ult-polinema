<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $sekarang = now();

        DB::table('faq')->insert([
            [
                'pertanyaan' => 'Berapa lama proses legalisir ijazah dan transkrip di loket ULT?',
                'jawaban' => 'Proses legalisir ijazah dan transkrip nilai membutuhkan waktu 1 x 24 jam kerja sejak berkas fisik atau digital diverifikasi oleh petugas loket ULT. Pemohon dapat memantau status secara langsung melalui menu Tracking E-Ticket.',
                'id_unit' => 1,
                'status' => 'aktif',
                'dibuat_pada' => $sekarang,
                'diperbarui_pada' => '2026-02-24 08:00:00',
            ],
            [
                'pertanyaan' => 'Apa saja syarat pengajuan penundaan atau penurunan UKT?',
                'jawaban' => 'Pengajuan penundaan atau penurunan UKT umumnya memerlukan surat permohonan, bukti pembayaran UKT terakhir, dan dokumen pendukung yang relevan seperti surat keterangan keuangan, bukti kehilangan pekerjaan, atau kondisi kesehatan yang memengaruhi kemampuan membayar. Dokumen tersebut perlu diverifikasi oleh petugas sebelum pengajuan diproses lebih lanjut.',
                'id_unit' => 2,
                'status' => 'aktif',
                'dibuat_pada' => $sekarang,
                'diperbarui_pada' => '2026-02-18 08:00:00',
            ],
            [
                'pertanyaan' => 'Bagaimana prosedur pengajuan surat rekomendasi beasiswa eksternal?',
                'jawaban' => 'Pengajuan surat rekomendasi beasiswa eksternal umumnya dimulai dengan mengajukan permohonan ke unit kemahasiswaan dengan melampirkan dokumen pendaftaran beasiswa, transkrip nilai terbaru, dan surat keterangan aktif kuliah. Setelah dokumen diverifikasi, unit kemahasiswaan akan memproses rekomendasi sesuai dengan kebijakan dan kuota yang tersedia.',
                'id_unit' => 3,
                'status' => 'aktif',
                'dibuat_pada' => $sekarang,
                'diperbarui_pada' => '2026-02-12 08:00:00',
            ],
        ]);
    }
}

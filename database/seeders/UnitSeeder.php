<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $sekarang = now();

        DB::table('unit')->upsert(
            [
                [
                    'id' => 1,
                    'nama_unit' => 'Unit Akademik',
                    'kode_unit' => 'AKD',
                    'deskripsi' => 'Melayani kebutuhan administrasi akademik, perkuliahan, dan layanan akademik lainnya.',
                    'dibuat_pada' => $sekarang,
                    'diperbarui_pada' => $sekarang,
                ],
                [
                    'id' => 2,
                    'nama_unit' => 'Unit Kemahasiswaan',
                    'kode_unit' => 'KMS',
                    'deskripsi' => 'Melayani kebutuhan administrasi kemahasiswaan, kegiatan mahasiswa, dan layanan terkait kemahasiswaan.',
                    'dibuat_pada' => $sekarang,
                    'diperbarui_pada' => $sekarang,
                ],
                [
                    'id' => 3,
                    'nama_unit' => 'Unit Humas dan Kerjasama',
                    'kode_unit' => 'HKS',
                    'deskripsi' => 'Melayani informasi publik, hubungan masyarakat, kerja sama, dan layanan informasi institusi.',
                    'dibuat_pada' => $sekarang,
                    'diperbarui_pada' => $sekarang,
                ],
                [
                    'id' => 4,
                    'nama_unit' => 'Unit Keuangan',
                    'kode_unit' => 'KEU',
                    'deskripsi' => 'Melayani administrasi keuangan, pembayaran, dan informasi layanan keuangan.',
                    'dibuat_pada' => $sekarang,
                    'diperbarui_pada' => $sekarang,
                ],
                [
                    'id' => 5,
                    'nama_unit' => 'Unit Kepegawaian',
                    'kode_unit' => 'PEG',
                    'deskripsi' => 'Melayani administrasi kepegawaian, data pegawai, dan layanan terkait kepegawaian.',
                    'dibuat_pada' => $sekarang,
                    'diperbarui_pada' => $sekarang,
                ],
            ],
            ['id'],
            [
                'nama_unit',
                'kode_unit',
                'deskripsi',
                'diperbarui_pada',
            ]
        );
    }
}

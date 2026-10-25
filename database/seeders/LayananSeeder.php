<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LayananSeeder extends Seeder
{
    public function run(): void
    {
        $sekarang = now();

        DB::table('layanan')->upsert(
            [
                [
                    'id' => 1,
                    'nama_layanan' => 'EDUFAIR',
                    'id_unit' => 3,
                    'estimasi_waktu' => '3 Hari Kerja',
                    'deskripsi_singkat' => 'Layanan informasi dan fasilitasi kegiatan EDUFAIR untuk mendukung penyampaian informasi pendidikan kepada masyarakat.',
                    'tarif' => 'Gratis',
                    'jam_operasional' => 'Senin–Jumat, 08.00–16.00 WIB',
                    'status' => 'aktif',
                    'tautan_eksternal' => null,
                    'dibuat_pada' => $sekarang,
                    'diperbarui_pada' => $sekarang,
                ],
                [
                    'id' => 2,
                    'nama_layanan' => 'LIPUTAN KEGIATAN',
                    'id_unit' => 3,
                    'estimasi_waktu' => '3 Hari Kerja',
                    'deskripsi_singkat' => 'Layanan permohonan peliputan dan publikasi kegiatan untuk mendukung dokumentasi serta penyebaran informasi kegiatan.',
                    'tarif' => 'Gratis',
                    'jam_operasional' => 'Senin–Jumat, 08.00–16.00 WIB',
                    'status' => 'aktif',
                    'tautan_eksternal' => null,
                    'dibuat_pada' => $sekarang,
                    'diperbarui_pada' => $sekarang,
                ],
                [
                    'id' => 3,
                    'nama_layanan' => 'KUNJUNGAN SEKOLAH',
                    'id_unit' => 3,
                    'estimasi_waktu' => '5 Hari Kerja',
                    'deskripsi_singkat' => 'Layanan koordinasi kunjungan sekolah atau instansi ke Politeknik Negeri Malang.',
                    'tarif' => 'Gratis',
                    'jam_operasional' => 'Senin–Jumat, 08.00–16.00 WIB',
                    'status' => 'aktif',
                    'tautan_eksternal' => null,
                    'dibuat_pada' => $sekarang,
                    'diperbarui_pada' => $sekarang,
                ],
                [
                    'id' => 4,
                    'nama_layanan' => 'PPID',
                    'id_unit' => 3,
                    'estimasi_waktu' => '10 Hari Kerja',
                    'deskripsi_singkat' => 'Layanan pengelolaan permohonan informasi publik dan pelayanan informasi sesuai ketentuan yang berlaku.',
                    'tarif' => 'Gratis',
                    'jam_operasional' => 'Senin–Jumat, 08.00–16.00 WIB',
                    'status' => 'aktif',
                    'tautan_eksternal' => null,
                    'dibuat_pada' => $sekarang,
                    'diperbarui_pada' => $sekarang,
                ],
                [
                    'id' => 5,
                    'nama_layanan' => 'SPMB',
                    'id_unit' => 3,
                    'estimasi_waktu' => 'Sesuai jadwal resmi',
                    'deskripsi_singkat' => 'Layanan informasi mengenai Seleksi Penerimaan Mahasiswa Baru (SPMB), termasuk informasi jadwal dan tahapan seleksi.',
                    'tarif' => 'Sesuai ketentuan',
                    'jam_operasional' => 'Sesuai jadwal resmi',
                    'status' => 'aktif',
                    'tautan_eksternal' => null,
                    'dibuat_pada' => $sekarang,
                    'diperbarui_pada' => $sekarang,
                ],
            ],
            ['id'],
            [
                'nama_layanan',
                'id_unit',
                'estimasi_waktu',
                'deskripsi_singkat',
                'tarif',
                'jam_operasional',
                'status',
                'tautan_eksternal',
                'diperbarui_pada',
            ]
        );
    }
}

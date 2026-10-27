<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RatingLayananSeeder extends Seeder
{
    public function run(): void
    {
        $sekarang = now();

        DB::table('rating_layanan')->upsert(
            [
                [
                    'id' => 1,
                    'id_pengajuan' => 1,
                    'id_pengguna' => 1,
                    'id_layanan' => 1,
                    'rating' => 5,
                    'komentar' => 'Pelayanan sangat baik, informasi yang diberikan jelas dan mudah dipahami.',
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 2,
                    'id_pengajuan' => 2,
                    'id_pengguna' => 2,
                    'id_layanan' => 2,
                    'rating' => 4,
                    'komentar' => 'Petugas cukup responsif dan proses pelayanan berjalan dengan baik.',
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 3,
                    'id_pengajuan' => 3,
                    'id_pengguna' => 3,
                    'id_layanan' => 3,
                    'rating' => 5,
                    'komentar' => 'Proses koordinasi kunjungan mudah dan informasi yang diberikan lengkap.',
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 4,
                    'id_pengajuan' => 4,
                    'id_pengguna' => 4,
                    'id_layanan' => 4,
                    'rating' => 3,
                    'komentar' => 'Pelayanan cukup baik, tetapi waktu respons masih bisa ditingkatkan.',
                    'dibuat_pada' => $sekarang,
                ],
                [
                    'id' => 5,
                    'id_pengajuan' => 5,
                    'id_pengguna' => 5,
                    'id_layanan' => 5,
                    'rating' => 4,
                    'komentar' => 'Informasi penerimaan mahasiswa baru cukup lengkap dan membantu.',
                    'dibuat_pada' => $sekarang,
                ],
            ],
            ['id'],
            [
                'id_pengajuan',
                'id_pengguna',
                'id_layanan',
                'rating',
                'komentar',
            ]
        );
    }
}

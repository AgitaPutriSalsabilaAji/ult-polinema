<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $sekarang = now();

        // Password awal untuk seluruh akun admin.
        // Ganti setelah login pertama.
        $kataSandi = Hash::make('Admin123!');

        DB::table('admin')->upsert(
            [
                [
                    'id' => 2,
                    'nama_lengkap_gelar' => 'Admin ULT',
                    'email' => 'admin@polinema.ac.id',
                    'kata_sandi' => $kataSandi,
                    'nip' => '1987654321',
                    'peran' => 'admin_ult',
                    'id_unit' => 1,
                    'nomor_telepon' => null,
                    'avatar' => null,
                    'dibuat_pada' => $sekarang,
                    'diperbarui_pada' => $sekarang,
                ],
                [
                    'id' => 3,
                    'nama_lengkap_gelar' => 'Admin Kehumasan',
                    'email' => 'admin.kehumasan@polinema.ac.id',
                    'kata_sandi' => $kataSandi,
                    'nip' => '198765432101',
                    'peran' => 'admin_unit',
                    'id_unit' => 3,
                    'nomor_telepon' => null,
                    'avatar' => null,
                    'dibuat_pada' => $sekarang,
                    'diperbarui_pada' => $sekarang,
                ],
                [
                    'id' => 4,
                    'nama_lengkap_gelar' => 'Admin Akademik',
                    'email' => 'admin.akademik@polinema.ac.id',
                    'kata_sandi' => $kataSandi,
                    'nip' => '198765432102',
                    'peran' => 'admin_unit',
                    'id_unit' => 1,
                    'nomor_telepon' => null,
                    'avatar' => null,
                    'dibuat_pada' => $sekarang,
                    'diperbarui_pada' => $sekarang,
                ],
                [
                    'id' => 5,
                    'nama_lengkap_gelar' => 'Admin Kemahasiswaan',
                    'email' => 'admin.kemahasiswaan@polinema.ac.id',
                    'kata_sandi' => $kataSandi,
                    'nip' => '198765432103',
                    'peran' => 'admin_unit',
                    'id_unit' => 2,
                    'nomor_telepon' => null,
                    'avatar' => null,
                    'dibuat_pada' => $sekarang,
                    'diperbarui_pada' => $sekarang,
                ],
                [
                    'id' => 6,
                    'nama_lengkap_gelar' => 'Admin Keuangan',
                    'email' => 'admin.keuangan@polinema.ac.id',
                    'kata_sandi' => $kataSandi,
                    'nip' => '198765432104',
                    'peran' => 'admin_unit',
                    'id_unit' => 4,
                    'nomor_telepon' => null,
                    'avatar' => null,
                    'dibuat_pada' => $sekarang,
                    'diperbarui_pada' => $sekarang,
                ],
                [
                    'id' => 7,
                    'nama_lengkap_gelar' => 'Admin Kepegawaian',
                    'email' => 'admin.kepegawaian@polinema.ac.id',
                    'kata_sandi' => $kataSandi,
                    'nip' => '198765432105',
                    'peran' => 'admin_unit',
                    'id_unit' => 5,
                    'nomor_telepon' => null,
                    'avatar' => null,
                    'dibuat_pada' => $sekarang,
                    'diperbarui_pada' => $sekarang,
                ],
            ],
            ['id'],
            [
                'nama_lengkap_gelar',
                'email',
                'kata_sandi',
                'nip',
                'peran',
                'id_unit',
                'nomor_telepon',
                'avatar',
                'diperbarui_pada',
            ]
        );
    }
}

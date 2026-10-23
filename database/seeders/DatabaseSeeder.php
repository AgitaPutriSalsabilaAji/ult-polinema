<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'id' => 2,
                'nama_lengkap_gelar' => 'Admin ULT',
                'email' => 'admin@polinema.ac.id',
                'nip' => '1987654321',
                'peran' => 'admin_ult',
                'id_unit' => null,
            ],
            [
                'id' => 3,
                'nama_lengkap_gelar' => 'Admin Kehumasan',
                'email' => 'admin.kehumasan@polinema.ac.id',
                'nip' => '198765432101',
                'peran' => 'admin_unit',
                'id_unit' => null,
            ],
            [
                'id' => 4,
                'nama_lengkap_gelar' => 'Admin Akademik',
                'email' => 'admin.akademik@polinema.ac.id',
                'nip' => '198765432102',
                'peran' => 'admin_unit',
                'id_unit' => null,
            ],
            [
                'id' => 5,
                'nama_lengkap_gelar' => 'Admin Kemahasiswaan',
                'email' => 'admin.kemahasiswaan@polinema.ac.id',
                'nip' => '198765432103',
                'peran' => 'admin_unit',
                'id_unit' => null,
            ],
            [
                'id' => 6,
                'nama_lengkap_gelar' => 'Admin Keuangan',
                'email' => 'admin.keuangan@polinema.ac.id',
                'nip' => '198765432104',
                'peran' => 'admin_unit',
                'id_unit' => null,
            ],
            [
                'id' => 7,
                'nama_lengkap_gelar' => 'Admin Kepegawaian',
                'email' => 'admin.kepegawaian@polinema.ac.id',
                'nip' => '198765432105',
                'peran' => 'admin_unit',
                'id_unit' => null,
            ],
        ];

        foreach ($admins as $admin) {
            DB::table('admin')->updateOrInsert(
                ['email' => $admin['email']],
                array_merge($admin, [
                    'kata_sandi' => Hash::make('admin123'),
                    'nomor_telepon' => null,
                    'avatar' => null,
                    'dibuat_pada' => now(),
                    'diperbarui_pada' => now(),
                ])
            );
        }
    }
}

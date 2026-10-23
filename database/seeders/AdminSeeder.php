<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            // Tambahkan seeder data awal di sini.
            // Contoh:
            // AdminSeeder::class,
            // UnitSeeder::class,
            // LayananSeeder::class,
            // FaqSeeder::class,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
        AdminSeeder::class,
        UnitSeeder::class,
        LayananSeeder::class,
        BagianLayananSeeder::class,
        FaqSeeder::class,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProfilKatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            ProfilBangJaiSeeder::class,
            KatalogBantuanSeeder::class,
        ]);
    }
}
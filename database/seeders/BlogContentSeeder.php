<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BlogContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            KategoriArtikelSeeder::class,
            ArtikelSeeder::class,
            PengumumanSeeder::class,
            HeroBannerSeeder::class,
        ]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriArtikel;

class KategoriArtikelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriArtikels = [
            [
                'nama_kategori' => 'Berita Terkini',
                'slug' => 'berita-terkini',
                'deskripsi' => 'Berita terkini seputar pertanian dan kebijakan pemerintah',
                'is_active' => true,
            ],
            [
                'nama_kategori' => 'Tips & Trik Pertanian',
                'slug' => 'tips-trik-pertanian',
                'deskripsi' => 'Tips dan trik dalam dunia pertanian',
                'is_active' => true,
            ],
            [
                'nama_kategori' => 'Inovasi Pertanian',
                'slug' => 'inovasi-pertanian',
                'deskripsi' => 'Inovasi teknologi dalam bidang pertanian',
                'is_active' => true,
            ],
            [
                'nama_kategori' => 'Program Bantuan',
                'slug' => 'program-bantuan',
                'deskripsi' => 'Informasi mengenai program bantuan pertanian',
                'is_active' => true,
            ],
            [
                'nama_kategori' => 'Sukses Petani',
                'slug' => 'sukses-petani',
                'deskripsi' => 'Kisah sukses para petani dalam mengembangkan usaha',
                'is_active' => true,
            ],
        ];

        foreach ($kategoriArtikels as $kategori) {
            KategoriArtikel::updateOrCreate(
                ['nama_kategori' => $kategori['nama_kategori']],
                $kategori
            );
        }
    }
}
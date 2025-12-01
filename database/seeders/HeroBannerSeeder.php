<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HeroBanner;

class HeroBannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $heroBanners = [
            [
                'judul' => 'Peningkatan Produktivitas Pertanian',
                'subjudul' => 'Dapatkan bantuan alsintan dan bibit berkualitas',
                'deskripsi_singkat' => 'Dapatkan bantuan alsintan dan bibit berkualitas untuk meningkatkan produktivitas pertanian Anda',
                'gambar' => 'hero-banners/produktivitas-pertanian.jpg',
                'cta_text' => 'Lihat Bantuan Tersedia',
                'cta_link' => '/katalog-bantuan',
                'is_active' => true,
                'urutan' => 1,
            ],
            [
                'judul' => 'Program Bantuan Tahun 2025',
                'subjudul' => 'Pendaftaran telah dibuka',
                'deskripsi_singkat' => 'Pendaftaran program bantuan alsintan dan benih unggul untuk kelompok tani tahun 2025 telah dibuka',
                'gambar' => 'hero-banners/program-bantuan-2025.jpg',
                'cta_text' => 'Daftar Sekarang',
                'cta_link' => '/pengumuman',
                'is_active' => true,
                'urutan' => 2,
            ],
            [
                'judul' => 'Inovasi Pertanian Masa Kini',
                'subjudul' => 'Teknologi pertanian terbaru',
                'deskripsi_singkat' => 'Temukan inovasi teknologi pertanian terbaru yang bisa Anda terapkan untuk meningkatkan hasil panen',
                'gambar' => 'hero-banners/inovasi-pertanian.jpg',
                'cta_text' => 'Baca Artikel',
                'cta_link' => '/artikel',
                'is_active' => true,
                'urutan' => 3,
            ],
        ];

        foreach ($heroBanners as $banner) {
            HeroBanner::updateOrCreate(
                ['judul' => $banner['judul']],
                $banner
            );
        }
    }
}
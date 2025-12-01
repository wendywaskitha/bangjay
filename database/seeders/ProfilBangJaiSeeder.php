<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProfilBangJai;

class ProfilBangJaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profilBangJais = [
            [
                'judul' => 'Profil Bang Jai - Petani Sejati',
                'konten_profil' => '<h2>Tentang Bang Jai</h2>
                <p>Bang Jai adalah seorang tokoh yang sangat dikenal dan dihormati di kalangan petani wilayah Jawa Tengah. Dengan pengalaman lebih dari 30 tahun di bidang pertanian, beliau telah banyak memberikan kontribusi untuk kemajuan pertanian di Indonesia.</p>
                
                <h3>Perjalanan Karier</h3>
                <p>Bang Jai mulai terjun dalam dunia pertanian sejak usia muda. Beliau memulai dari seorang petani biasa hingga menjadi seorang konsultan pertanian yang banyak memberikan pelatihan dan bimbingan kepada petani-petani muda.</p>
                
                <h3>Program dan Kontribusi</h3>
                <p>Bang Jai telah menjalankan berbagai program pertanian seperti:</p>
                <ul>
                    <li>Pelatihan teknik bercocok tanam modern</li>
                    <li>Pengembangan varietas padi unggul</li>
                    <li>Pembinaan kelompok tani</li>
                    <li>Penyuluhan pertanian berbasis teknologi</li>
                </ul>
                
                <h3>Visi dan Misi</h3>
                <p>Visi Bang Jai adalah meningkatkan kesejahteraan petani dan memajukan pertanian Indonesia melalui pendidikan, teknologi, dan kolaborasi. Misi beliau adalah memberdayakan petani dengan ilmu pengetahuan dan teknologi terkini.</p>',
                'foto_profil' => 'profil-bangjai/foto-profil.jpg',
                'foto_banner' => 'profil-bangjai/banner-profil.jpg',
                'is_active' => true,
            ],
        ];

        foreach ($profilBangJais as $profil) {
            ProfilBangJai::updateOrCreate(
                ['judul' => $profil['judul']],
                $profil
            );
        }
    }
}
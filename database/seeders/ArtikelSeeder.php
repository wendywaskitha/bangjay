<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Artikel;
use App\Models\KategoriArtikel;
use App\Models\User; // Assuming you have a User model for author

class ArtikelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing categories and users
        $kategoriIds = KategoriArtikel::pluck('id')->toArray();
        if (empty($kategoriIds)) {
            $this->command->info('No kategori artikel found. Please run KategoriArtikelSeeder first.');
            return;
        }

        // Create a default user if none exists
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $artikels = [
            [
                'judul' => 'Inovasi Teknologi dalam Dunia Pertanian Modern',
                'slug' => 'inovasi-teknologi-dalam-dunia-pertanian-modern',
                'ringkasan' => 'Perkembangan teknologi dalam dunia pertanian semakin pesat. Kini, para petani bisa memanfaatkan berbagai inovasi untuk meningkatkan produktivitas lahan pertanian.',
                'konten' => '<p>Perkembangan teknologi dalam dunia pertanian semakin pesat. Kini, para petani bisa memanfaatkan berbagai inovasi untuk meningkatkan produktivitas lahan pertanian.</p><p>Beberapa inovasi yang populer antara lain:</p><ul><li>Drone untuk pemantauan lahan</li><li>Smart irrigation system</li><li>Aplikasi manajemen pertanian</li></ul>',
                'thumbnail' => 'artikel-thumbnails/teknologi-pertanian.jpg',
                'kategori_id' => $kategoriIds[2], // Inovasi Pertanian
                'author_id' => $user->id,
                'status' => 'published',
                'published_at' => now()->subDays(5),
            ],
            [
                'judul' => 'Tips Mengatasi Hama Tanaman Padi',
                'slug' => 'tips-mengatasi-hama-tanaman-padi',
                'ringkasan' => 'Hama tanaman padi merupakan salah satu masalah utama yang sering dihadapi para petani. Berikut beberapa tips mengatasi hama tanaman padi secara efektif.',
                'konten' => '<p>Hama tanaman padi merupakan salah satu masalah utama yang sering dihadapi para petani. Berikut beberapa tips mengatasi hama tanaman padi secara efektif:</p><ol><li>Menggunakan varietas tahan hama</li><li>Pengendalian hayati</li><li>Rotasi tanaman</li><li>Penanaman serempak</li></ol>',
                'thumbnail' => 'artikel-thumbnails/hama-padi.jpg',
                'kategori_id' => $kategoriIds[1], // Tips & Trik Pertanian
                'author_id' => $user->id,
                'status' => 'published',
                'published_at' => now()->subDays(3),
            ],
            [
                'judul' => 'Program Bantuan Alsintan Tahun 2025',
                'slug' => 'program-bantuan-alsintan-tahun-2025',
                'ringkasan' => 'Pemerintah kembali menggelar program bantuan alat mesin pertanian (Alsintan) tahun 2025. Program ini ditujukan untuk meningkatkan produktivitas pertanian dan membantu para petani.',
                'konten' => '<p>Pemerintah kembali menggelar program bantuan alat mesin pertanian (Alsintan) tahun 2025. Program ini ditujukan untuk meningkatkan produktivitas pertanian dan membantu para petani.</p><p>Beberapa jenis bantuan yang tersedia meliputi:</p><ul><li>Traktor tangan</li><li>Combine harvester</li><li>Thresher (perontok padi)</li><li>Hand sprayer</li></ul>',
                'thumbnail' => 'artikel-thumbnails/bantuan-alsintan.jpg',
                'kategori_id' => $kategoriIds[3], // Program Bantuan
                'author_id' => $user->id,
                'status' => 'published',
                'published_at' => now()->subDays(1),
            ],
            [
                'judul' => 'Kisah Sukses Petani Muda dari Jawa Barat',
                'slug' => 'kisah-sukses-petani-muda-dari-jawa-barat',
                'ringkasan' => 'Berawal dari lahan pertanian 1 hektar, seorang petani muda dari Jawa Barat berhasil mengembangkan usahanya hingga mencakup 15 hektar lahan.',
                'konten' => '<p>Berawal dari lahan pertanian 1 hektar, seorang petani muda dari Jawa Barat berhasil mengembangkan usahanya hingga mencakup 15 hektar lahan. Dengan memanfaatkan teknologi dan inovasi, ia mampu meningkatkan hasil panen hingga 40% dari rata-rata petani lain.</p><p>Kuncinya adalah pemanfaatan pupuk organik dan sistem irigasi modern yang efisien.</p>',
                'thumbnail' => 'artikel-thumbnails/sukses-petani-muda.jpg',
                'kategori_id' => $kategoriIds[4], // Sukses Petani
                'author_id' => $user->id,
                'status' => 'published',
                'published_at' => now()->subDays(10),
            ],
            [
                'judul' => 'Perubahan Iklim dan Dampaknya pada Pertanian',
                'slug' => 'perubahan-iklim-dan-dampaknya-pada-pertanian',
                'ringkasan' => 'Perubahan iklim menjadi tantangan besar bagi dunia pertanian. Pola curah hujan yang tidak menentu dan suhu yang meningkat berdampak langsung pada hasil pertanian.',
                'konten' => '<p>Perubahan iklim menjadi tantangan besar bagi dunia pertanian. Pola curah hujan yang tidak menentu dan suhu yang meningkat berdampak langsung pada hasil pertanian.</p><p>Beberapa adaptasi yang bisa dilakukan:</p><ul><li>Memilih varietas tahan kekeringan</li><li>Perubahan waktu tanam</li><li>Penggunaan teknologi ramah lingkungan</li></ul>',
                'thumbnail' => 'artikel-thumbnails/perubahan-iklim.jpg',
                'kategori_id' => $kategoriIds[0], // Berita Terkini
                'author_id' => $user->id,
                'status' => 'published',
                'published_at' => now()->subDays(2),
            ],
        ];

        foreach ($artikels as $artikel) {
            Artikel::updateOrCreate(
                ['judul' => $artikel['judul']],
                $artikel
            );
        }
    }
}
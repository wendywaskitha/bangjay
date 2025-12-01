<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengumuman;

class PengumumanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pengumumen = [
            [
                'judul' => 'Pendaftaran Program Bantuan Alsintan Tahun 2025 Dibuka',
                'slug' => 'pendaftaran-program-bantuan-alsintan-tahun-2025',
                'isi' => '<p>Kami informasikan bahwa pendaftaran program bantuan alat mesin pertanian (Alsintan) tahun 2025 telah dibuka.</p><p>Persyaratan:</p><ul><li>Merupakan kelompok tani aktif</li><li>Domisili di wilayah yang ditentukan</li><li>Memiliki lahan pertanian produktif</li></ul><p>Waktu pendaftaran: 1 Januari - 28 Februari 2025</p>',
                'mulai_tayang' => now()->subDays(5),
                'selesai_tayang' => now()->addDays(25),
                'is_active' => true,
            ],
            [
                'judul' => 'Pelatihan Teknologi Pertanian Modern',
                'slug' => 'pelatihan-teknologi-pertanian-modern',
                'isi' => '<p>Dinas Pertanian akan menyelenggarakan pelatihan teknologi pertanian modern bagi para petani di wilayah Jawa Tengah.</p><p>Jadwal: 15 - 17 Maret 2025</p><p>Lokasi: Balai Penyuluhan Pertanian Kec. Salatiga</p><p>Peserta akan diberikan sertifikat pelatihan dan modul teknologi pertanian.</p>',
                'mulai_tayang' => now()->subDays(2),
                'selesai_tayang' => now()->addDays(20),
                'is_active' => true,
            ],
            [
                'judul' => 'Pengumuman Jadwal Pemerataan Bantuan Tahun Ini',
                'slug' => 'pengumuman-jadwal-pemerataan-bantuan-tahun-ini',
                'isi' => '<p>Kami sampaikan bahwa penyaluran bantuan tahun ini akan dilakukan secara bertahap dan merata di seluruh wilayah sesuai dengan kebutuhan.</p><p>Petani dan kelompok tani diharapkan mempersiapkan dokumen pendukung seperti SK Pengurus, Rekening Tabungan Kelompok, dan Dokumen Aset Kelompok.</p>',
                'mulai_tayang' => now()->subDays(10),
                'selesai_tayang' => now()->addDays(15),
                'is_active' => true,
            ],
            [
                'judul' => 'Penutupan Sementara Sistem Pendaftaran',
                'slug' => 'penutupan-sementara-sistem-pendaftaran',
                'isi' => '<p>Dengan hormat, sistem pendaftaran bantuan online akan ditutup sementara pada tanggal 30 November 2025 untuk pemeliharaan sistem.</p><p>Penutupan sementara akan berlangsung selama 3 hari, terhitung mulai 30 November hingga 2 Desember 2025. Mohon maaf atas ketidaknyamanan ini.</p>',
                'mulai_tayang' => now()->subDays(1),
                'selesai_tayang' => now()->addDays(5),
                'is_active' => true,
            ],
            [
                'judul' => 'Pembukaan Program Subsidi Pupuk Organik',
                'slug' => 'pembukaan-program-subsidi-pupuk-organik',
                'isi' => '<p>Pemerintah membuka program subsidi pupuk organik untuk petani di 5 kabupaten prioritas.</p><p>Kabupaten yang termasuk dalam program ini adalah: Wonogiri, Kebumen, Purworejo, Kulon Progo, dan Magelang.</p><p>Petani dapat mengajukan permohonan melalui sistem yang telah disediakan atau datang langsung ke kantor Dinas Pertanian setempat.</p>',
                'mulai_tayang' => now()->subDays(3),
                'selesai_tayang' => now()->addDays(30),
                'is_active' => true,
            ],
        ];

        foreach ($pengumumen as $pengumuman) {
            Pengumuman::updateOrCreate(
                ['judul' => $pengumuman['judul']],
                $pengumuman
            );
        }
    }
}
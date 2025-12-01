<?php

namespace Database\Seeders\Wilayah;

use Illuminate\Database\Seeder;
use App\Models\KategoriBantuan;
use App\Models\JenisBantuan;

class JenisBantuanSeeder extends Seeder
{
    public function run()
    {
        // Buat kategori bantuan
        $kategoriBantuan = [
            [
                'nama_kategori' => 'Alsintan',
                'deskripsi' => 'Alat Mesin Pertanian',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Bibit',
                'deskripsi' => 'Bibit dan Benih Unggul',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Pupuk',
                'deskripsi' => 'Pupuk Subsidi',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Pelatihan',
                'deskripsi' => 'Pelatihan dan Penyuluhan',
                'is_active' => true
            ],
        ];

        foreach ($kategoriBantuan as $kategori) {
            KategoriBantuan::firstOrCreate(
                ['nama_kategori' => $kategori['nama_kategori']],
                $kategori
            );
        }

        // Buat jenis bantuan
        $jenisBantuan = [
            [
                'kategori_nama' => 'Alsintan',
                'nama_bantuan' => 'Traktor Roda Dua',
                'periode_tahun' => 2024,
                'deskripsi' => 'Bantuan traktor roda dua untuk pertanian lahan kering',
                'is_active' => true
            ],
            [
                'kategori_nama' => 'Alsintan',
                'nama_bantuan' => 'Corn Sheller',
                'periode_tahun' => 2024,
                'deskripsi' => 'Mesin perontok jagung untuk meningkatkan efisiensi pasca panen',
                'is_active' => true
            ],
            [
                'kategori_nama' => 'Bibit',
                'nama_bantuan' => 'Bibit Padi Unggul',
                'periode_tahun' => 2024,
                'deskripsi' => 'Bibit padi varietas unggul tahan hama dan cuaca ekstrem',
                'is_active' => true
            ],
            [
                'kategori_nama' => 'Bibit',
                'nama_bantuan' => 'Bibit Kakao',
                'periode_tahun' => 2024,
                'deskripsi' => 'Bibit kakao hasil klon terbaik dengan produktivitas tinggi',
                'is_active' => true
            ],
            [
                'kategori_nama' => 'Pupuk',
                'nama_bantuan' => 'Pupuk Organik',
                'periode_tahun' => 2024,
                'deskripsi' => 'Pupuk organik untuk meningkatkan kesuburan tanah',
                'is_active' => true
            ],
            [
                'kategori_nama' => 'Pelatihan',
                'nama_bantuan' => 'Pelatihan Pengolahan Hasil Pertanian',
                'periode_tahun' => 2024,
                'deskripsi' => 'Pelatihan pengolahan hasil pertanian menjadi produk bernilai tambah',
                'is_active' => true
            ],
        ];

        foreach ($jenisBantuan as $bantuan) {
            // Ambil ID kategori secara aman
            $kategori = KategoriBantuan::where('nama_kategori', $bantuan['kategori_nama'])->first();
            if ($kategori) {
                $bantuan['kategori_bantuan_id'] = $kategori->id;
                unset($bantuan['kategori_nama']); // Hapus kolom yang tidak diperlukan

                JenisBantuan::firstOrCreate(
                    ['nama_bantuan' => $bantuan['nama_bantuan']],
                    $bantuan
                );
            }
        }
    }
}
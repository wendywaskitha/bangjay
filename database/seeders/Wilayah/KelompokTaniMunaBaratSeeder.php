<?php

namespace Database\Seeders\Wilayah;

use Illuminate\Database\Seeder;
use App\Models\Kabupaten;
use App\Models\KelompokTani;
use App\Models\KelompokTaniAnggota;
use App\Models\JenisKomoditas;
use App\Models\Desa;

class KelompokTaniMunaBaratSeeder extends Seeder
{
    public function run()
    {
        // Ambil kabupaten Muna Barat
        $kabupaten = Kabupaten::where('nama_kabupaten', 'Kabupaten Muna Barat')->first();
        
        if (!$kabupaten) {
            $this->command->error('Kabupaten Muna Barat tidak ditemukan. Pastikan MunaBaratSeeder telah dijalankan.');
            return;
        }

        // Ambil desa-desa dari Kabupaten Muna Barat
        $desaIds = Desa::whereHas('kecamatan', function($query) use ($kabupaten) {
            $query->where('kabupaten_id', $kabupaten->id);
        })->pluck('id');

        if ($desaIds->isEmpty()) {
            $this->command->error('Tidak ada desa ditemukan di Kabupaten Muna Barat.');
            return;
        }

        // Buat beberapa jenis komoditas
        $komoditasList = [
            ['nama_komoditas' => 'Padi', 'deskripsi' => 'Komoditas pangan utama berupa beras'],
            ['nama_komoditas' => 'Jagung', 'deskripsi' => 'Komoditas pangan serealia'],
            ['nama_komoditas' => 'Kakao', 'deskripsi' => 'Komoditas perkebunan'],
            ['nama_komoditas' => 'Kopi', 'deskripsi' => 'Komoditas perkebunan'],
            ['nama_komoditas' => 'Sagu', 'deskripsi' => 'Komoditas pangan pokok daerah'],
            ['nama_komoditas' => 'Cengkeh', 'deskripsi' => 'Komoditas perkebunan rempah'],
        ];

        foreach ($komoditasList as $komoditasData) {
            JenisKomoditas::firstOrCreate(
                ['nama_komoditas' => $komoditasData['nama_komoditas']],
                $komoditasData
            );
        }

        // Data contoh kelompok tani
        $kelompokTaniData = [
            [
                'nama_kelompok' => 'Tani Makmur',
                'desa_id' => $desaIds->random(),
                'profil' => 'Kelompok tani yang berfokus pada pengembangan pertanian padi dan palawija',
                'jumlah_anggota' => 18,
                'latitude' => -4.0667, // Koordinat untuk wilayah Muna Barat
                'longitude' => 122.1333,
            ],
            [
                'nama_kelompok' => 'Sumber Rejeki',
                'desa_id' => $desaIds->random(),
                'profil' => 'Kelompok tani yang berfokus pada pengembangan perkebunan kakao dan kopi',
                'jumlah_anggota' => 22,
                'latitude' => -4.1,
                'longitude' => 122.1,
            ],
            [
                'nama_kelompok' => 'Sahabat Tani',
                'desa_id' => $desaIds->random(),
                'profil' => 'Kelompok tani yang bergerak di bidang pertanian padi dan jagung',
                'jumlah_anggota' => 15,
                'latitude' => -4.05,
                'longitude' => 122.15,
            ],
            [
                'nama_kelompok' => 'Tunas Bangsa',
                'desa_id' => $desaIds->random(),
                'profil' => 'Kelompok tani muda yang inovatif dalam pengembangan pertanian modern',
                'jumlah_anggota' => 25,
                'latitude' => -4.08,
                'longitude' => 122.08,
            ],
            [
                'nama_kelompok' => 'Bakti Nusa',
                'desa_id' => $desaIds->random(),
                'profil' => 'Kelompok tani yang berfokus pada pengembangan komoditas lokal',
                'jumlah_anggota' => 19,
                'latitude' => -4.12,
                'longitude' => 122.12,
            ],
        ];

        foreach ($kelompokTaniData as $data) {
            $kelompokTani = KelompokTani::firstOrCreate([
                'nama_kelompok' => $data['nama_kelompok'],
                'desa_id' => $data['desa_id']
            ], [
                'profil' => $data['profil'],
                'jumlah_anggota' => $data['jumlah_anggota'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
            ]);

            // Buat anggota kelompok tani
            $anggotaList = [
                ['nama_anggota' => 'Ahmad Supriadi', 'jabatan' => 'Ketua', 'no_hp' => '081234567890', 'luas_lahan' => 2.5],
                ['nama_anggota' => 'Siti Nurhaliza', 'jabatan' => 'Sekretaris', 'no_hp' => '082234567891', 'luas_lahan' => 1.8],
                ['nama_anggota' => 'Budi Santoso', 'jabatan' => 'Bendahara', 'no_hp' => '083234567892', 'luas_lahan' => 2.0],
                ['nama_anggota' => 'Laila Wati', 'jabatan' => 'Anggota', 'no_hp' => '084234567893', 'luas_lahan' => 1.5],
                ['nama_anggota' => 'Joko Prasetyo', 'jabatan' => 'Anggota', 'no_hp' => '085234567894', 'luas_lahan' => 3.0],
            ];

            foreach ($anggotaList as $anggotaData) {
                $komoditas = JenisKomoditas::inRandomOrder()->first();

                KelompokTaniAnggota::firstOrCreate([
                    'kelompok_tani_id' => $kelompokTani->id,
                    'nama_anggota' => $anggotaData['nama_anggota']
                ], [
                    'jabatan' => $anggotaData['jabatan'],
                    'no_hp' => $anggotaData['no_hp'],
                    'luas_lahan' => $anggotaData['luas_lahan'],
                    'jenis_komoditas_id' => $komoditas->id,
                ]);
            }
        }
    }
}
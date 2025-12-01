<?php

namespace Database\Seeders\Wilayah;

use Illuminate\Database\Seeder;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;

class MunaBaratSeeder extends Seeder
{
    public function run()
    {
        // Membuat entri Kabupaten Muna Barat jika belum ada
        $kabupaten = Kabupaten::firstOrCreate([
            'nama_kabupaten' => 'Kabupaten Muna Barat',
        ], [
            'kode_kabupaten' => '74.13'
        ]);

        // Data kecamatan dan desa/kelurahan untuk Kabupaten Muna Barat
        $kecamatanDesaData = [
            [
                'kecamatan' => 'Barangka',
                'kode_kecamatan' => '74.13.02',
                'desa_kelurahan' => [
                    ['nama' => 'Barangka', 'tipe' => 'Desa'],
                    ['nama' => 'Bungkolo', 'tipe' => 'Desa'],
                    ['nama' => 'Lafinde', 'tipe' => 'Desa'],
                    ['nama' => 'Lapolea', 'tipe' => 'Desa'],
                    ['nama' => 'Sawerigadi', 'tipe' => 'Desa'],
                    ['nama' => 'Walelei', 'tipe' => 'Desa'],
                    ['nama' => 'Waulai', 'tipe' => 'Desa'],
                    ['nama' => 'Wuna', 'tipe' => 'Desa'],
                ]
            ],
            [
                'kecamatan' => 'Kusambi',
                'kode_kecamatan' => '74.13.10',
                'desa_kelurahan' => [
                    ['nama' => 'Bakeramba', 'tipe' => 'Desa'],
                    ['nama' => 'Guali', 'tipe' => 'Desa'],
                    ['nama' => 'Kasakamu', 'tipe' => 'Desa'],
                    ['nama' => 'Kusambi', 'tipe' => 'Desa'],
                    ['nama' => 'Lakawoghe', 'tipe' => 'Desa'],
                    ['nama' => 'Lapokainse', 'tipe' => 'Desa'],
                    ['nama' => 'Lemoambo', 'tipe' => 'Desa'],
                    ['nama' => 'Sidamangura', 'tipe' => 'Desa'],
                    ['nama' => 'Tanjung Pinang', 'tipe' => 'Desa'],
                    ['nama' => 'Konawe', 'tipe' => 'Kelurahan'],
                ]
            ],
            [
                'kecamatan' => 'Lawa',
                'kode_kecamatan' => '74.13.03',
                'desa_kelurahan' => [
                    ['nama' => 'Lagadi', 'tipe' => 'Desa'],
                    ['nama' => 'Lalemba', 'tipe' => 'Desa'],
                    ['nama' => 'Latompe', 'tipe' => 'Desa'],
                    ['nama' => 'Latugho', 'tipe' => 'Desa'],
                    ['nama' => 'Madampi', 'tipe' => 'Desa'],
                    ['nama' => 'Watumela', 'tipe' => 'Desa'],
                    ['nama' => 'Lapadaku', 'tipe' => 'Kelurahan'],
                    ['nama' => 'Wamelai', 'tipe' => 'Kelurahan'],
                ]
            ],
            [
                'kecamatan' => 'Maginti',
                'kode_kecamatan' => '74.13.06',
                'desa_kelurahan' => [
                    ['nama' => 'Abadi Jaya', 'tipe' => 'Desa'],
                    ['nama' => 'Bangko', 'tipe' => 'Desa'],
                    ['nama' => 'Gala', 'tipe' => 'Desa'],
                    ['nama' => 'Kangkunawe', 'tipe' => 'Desa'],
                    ['nama' => 'Kembar Maminasa', 'tipe' => 'Desa'],
                    ['nama' => 'Maginti', 'tipe' => 'Desa'],
                    ['nama' => 'Pajala', 'tipe' => 'Desa'],
                    ['nama' => 'Pasipadangan', 'tipe' => 'Desa'],
                ]
            ],
            [
                'kecamatan' => 'Napano Kusambi',
                'kode_kecamatan' => '74.13.11',
                'desa_kelurahan' => [
                    ['nama' => 'Kombikuno', 'tipe' => 'Desa'],
                    ['nama' => 'Lahaji', 'tipe' => 'Desa'],
                    ['nama' => 'Latawe', 'tipe' => 'Desa'],
                    ['nama' => 'Masara', 'tipe' => 'Desa'],
                    ['nama' => 'Tangkumaho', 'tipe' => 'Desa'],
                    ['nama' => 'Umba', 'tipe' => 'Desa'],
                ]
            ],
            [
                'kecamatan' => 'Sawerigadi',
                'kode_kecamatan' => '74.13.01',
                'desa_kelurahan' => [
                    ['nama' => 'Kampobalano', 'tipe' => 'Desa'],
                    ['nama' => 'Lakalamba', 'tipe' => 'Desa'],
                    ['nama' => 'Lawada Jaya', 'tipe' => 'Desa'],
                    ['nama' => 'Lombu Jaya', 'tipe' => 'Desa'],
                    ['nama' => 'Maperaha', 'tipe' => 'Desa'],
                    ['nama' => 'Marobea', 'tipe' => 'Desa'],
                    ['nama' => 'Nihi', 'tipe' => 'Desa'],
                    ['nama' => 'Ondoke', 'tipe' => 'Desa'],
                    ['nama' => 'Wakoila', 'tipe' => 'Desa'],
                    ['nama' => 'Waukuni', 'tipe' => 'Desa'],
                ]
            ],
            [
                'kecamatan' => 'Tiworo Kepulauan',
                'kode_kecamatan' => '74.13.09',
                'desa_kelurahan' => [
                    ['nama' => 'Katela', 'tipe' => 'Desa'],
                    ['nama' => 'Lasama', 'tipe' => 'Desa'],
                    ['nama' => 'Laworo', 'tipe' => 'Desa'],
                    ['nama' => 'Sidomakmur', 'tipe' => 'Desa'],
                    ['nama' => 'Wandoke', 'tipe' => 'Desa'],
                    ['nama' => 'Waturempe', 'tipe' => 'Desa'],
                    ['nama' => 'Wulanga Jaya', 'tipe' => 'Desa'],
                    ['nama' => 'Tiworo', 'tipe' => 'Kelurahan'],
                    ['nama' => 'Waumere', 'tipe' => 'Kelurahan'],
                ]
            ],
            [
                'kecamatan' => 'Tiworo Selatan',
                'kode_kecamatan' => '74.13.05',
                'desa_kelurahan' => [
                    ['nama' => 'Barakkah', 'tipe' => 'Desa'],
                    ['nama' => 'Kasimpa Jaya', 'tipe' => 'Desa'],
                    ['nama' => 'Katangana', 'tipe' => 'Desa'],
                    ['nama' => 'Parura Jaya', 'tipe' => 'Desa'],
                    ['nama' => 'Sangia Tiworo', 'tipe' => 'Desa'],
                ]
            ],
            [
                'kecamatan' => 'Tiworo Tengah',
                'kode_kecamatan' => '74.13.07',
                'desa_kelurahan' => [
                    ['nama' => 'Labokolo', 'tipe' => 'Desa'],
                    ['nama' => 'Lakabu', 'tipe' => 'Desa'],
                    ['nama' => 'Langku Langku', 'tipe' => 'Desa'],
                    ['nama' => 'Mekar Jaya', 'tipe' => 'Desa'],
                    ['nama' => 'Momuntu', 'tipe' => 'Desa'],
                    ['nama' => 'Suka Damai', 'tipe' => 'Desa'],
                    ['nama' => 'Wanseriwu', 'tipe' => 'Desa'],
                    ['nama' => 'Wapae', 'tipe' => 'Desa'],
                ]
            ],
            [
                'kecamatan' => 'Tiworo Utara',
                'kode_kecamatan' => '74.13.08',
                'desa_kelurahan' => [
                    ['nama' => 'Bero', 'tipe' => 'Desa'],
                    ['nama' => 'Mandike', 'tipe' => 'Desa'],
                    ['nama' => 'Santigi', 'tipe' => 'Desa'],
                    ['nama' => 'Santiri', 'tipe' => 'Desa'],
                    ['nama' => 'Tasipi', 'tipe' => 'Desa'],
                    ['nama' => 'Tiga', 'tipe' => 'Desa'],
                    ['nama' => 'Tondasi', 'tipe' => 'Desa'],
                ]
            ],
            [
                'kecamatan' => 'Wadaga',
                'kode_kecamatan' => '74.13.04',
                'desa_kelurahan' => [
                    ['nama' => 'Kampani', 'tipe' => 'Desa'],
                    ['nama' => 'Katobu', 'tipe' => 'Desa'],
                    ['nama' => 'Lailangga', 'tipe' => 'Desa'],
                    ['nama' => 'Lakanaha', 'tipe' => 'Desa'],
                    ['nama' => 'Lasosodo', 'tipe' => 'Desa'],
                    ['nama' => 'Lindo', 'tipe' => 'Desa'],
                    ['nama' => 'Wakontu', 'tipe' => 'Desa'],
                ]
            ],
        ];

        // Membuat data kecamatan dan desa/kelurahan
        foreach ($kecamatanDesaData as $data) {
            $kecamatan = Kecamatan::firstOrCreate([
                'nama_kecamatan' => $data['kecamatan'],
            ], [
                'kabupaten_id' => $kabupaten->id,
                'kode_kecamatan' => $data['kode_kecamatan']
            ]);

            foreach ($data['desa_kelurahan'] as $desaData) {
                Desa::firstOrCreate([
                    'nama_desa' => $desaData['nama'],
                    'kecamatan_id' => $kecamatan->id
                ], [
                    'tipe' => $desaData['tipe']
                ]);
            }
        }
    }
}
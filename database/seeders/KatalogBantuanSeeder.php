<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KatalogBantuan;
use App\Models\JenisBantuan;

class KatalogBantuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing jenis bantuan IDs
        $jenisBantuanIds = JenisBantuan::pluck('id')->toArray();
        if (empty($jenisBantuanIds)) {
            $this->command->info('No jenis bantuan found. Please run JenisBantuanSeeder first.');
            return;
        }

        $katalogBantuans = [
            [
                'jenis_bantuan_id' => $jenisBantuanIds[0] ?? null,
                'judul' => 'Traktor Tangan 10,5 HP',
                'slug' => 'traktor-tangan-10-5-hp',
                'deskripsi' => 'Traktor tangan dengan kapasitas 10,5 HP yang cocok untuk pengolahan lahan sawah dan ladang. Dilengkapi dengan sistem starter manual dan electric, serta berbagai pilihan aksesori pendukung.',
                'foto' => 'katalog-bantuan/traktor-tangan.jpg',
                'tanggal_mulai' => now()->subDays(30),
                'tanggal_selesai' => now()->addDays(60),
                'is_active' => true,
            ],
            [
                'jenis_bantuan_id' => $jenisBantuanIds[1] ?? null,
                'judul' => 'Combine Harvester',
                'slug' => 'combine-harvester',
                'deskripsi' => 'Mesin panen gabah modern yang dapat memotong, memisahkan butir padi dari malai, dan membersihkan gabah sekaligus. Meningkatkan efisiensi panen hingga 90%.',
                'foto' => 'katalog-bantuan/combine-harvester.jpg',
                'tanggal_mulai' => now()->subDays(15),
                'tanggal_selesai' => now()->addDays(45),
                'is_active' => true,
            ],
            [
                'jenis_bantuan_id' => $jenisBantuanIds[2] ?? null,
                'judul' => 'Benih Padi Inpari 42',
                'slug' => 'benih-padi-inpari-42',
                'deskripsi' => 'Benih padi unggulan dengan hasil tinggi, tahan terhadap hama wereng coklat, dan memiliki umur panen sekitar 105-110 hari setelah tanam.',
                'foto' => 'katalog-bantuan/benih-padi-inpari42.jpg',
                'tanggal_mulai' => now()->subDays(10),
                'tanggal_selesai' => now()->addDays(50),
                'is_active' => true,
            ],
            [
                'jenis_bantuan_id' => $jenisBantuanIds[3] ?? null,
                'judul' => 'Pompa Air Listrik 1 HP',
                'slug' => 'pompa-air-listrik-1-hp',
                'deskripsi' => 'Pompa air dengan kapasitas 1 HP yang digunakan untuk irigasi lahan pertanian. Tahan lama, hemat energi, dan mampu mengalirkan air hingga jarak 50 meter.',
                'foto' => 'katalog-bantuan/pompa-air.jpg',
                'tanggal_mulai' => now()->subDays(5),
                'tanggal_selesai' => now()->addDays(30),
                'is_active' => true,
            ],
            [
                'jenis_bantuan_id' => $jenisBantuanIds[4] ?? null,
                'judul' => 'Pupuk Organik Cair',
                'slug' => 'pupuk-organik-cair',
                'deskripsi' => 'Pupuk organik cair yang membantu meningkatkan kesuburan tanah dan hasil pertanian secara alami tanpa meninggalkan residu berbahaya.',
                'foto' => 'katalog-bantuan/pupuk-organik-cair.jpg',
                'tanggal_mulai' => now()->subDays(20),
                'tanggal_selesai' => now()->addDays(70),
                'is_active' => true,
            ],
        ];

        foreach ($katalogBantuans as $katalog) {
            KatalogBantuan::updateOrCreate(
                ['judul' => $katalog['judul']],
                $katalog
            );
        }
    }
}
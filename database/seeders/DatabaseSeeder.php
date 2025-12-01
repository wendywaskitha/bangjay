<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Hanya buat user jika belum ada
        if (!\App\Models\User::where('email', 'test@example.com')->exists()) {
            \App\Models\User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        $this->call([
            \Database\Seeders\Wilayah\MunaBaratSeeder::class,
            \Database\Seeders\Wilayah\JenisBantuanSeeder::class,
            \Database\Seeders\Wilayah\KelompokTaniMunaBaratSeeder::class,
        ]);
    }
}

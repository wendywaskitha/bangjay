<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\SebaranBantuan;
use App\Models\JenisBantuan;
use App\Models\KelompokTani;

class SebaranBantuanOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Sebaran Bantuan', SebaranBantuan::count())
                ->description('Jumlah penyaluran bantuan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Stat::make('Total Jenis Bantuan', JenisBantuan::count())
                ->description('Jenis bantuan yang tersedia')
                ->descriptionIcon('heroicon-m-gift')
                ->color('warning'),

            Stat::make('Total Kelompok Tani', KelompokTani::count())
                ->description('Kelompok tani penerima')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];
    }
}
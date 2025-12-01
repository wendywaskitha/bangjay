<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\KelompokTani;
use App\Models\Kabupaten;
use App\Models\Kecamatan;

class KelompokTaniOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Kelompok Tani', KelompokTani::count())
                ->description('Jumlah kelompok tani terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([10, 15, 8, 12, 14, 9, 11])
                ->color('success'),

            Stat::make('Total Kabupaten', Kabupaten::count())
                ->description('Wilayah cakupan')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('primary'),

            Stat::make('Total Kecamatan', Kecamatan::count())
                ->description('Kecamatan tercakup')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info'),
        ];
    }
}
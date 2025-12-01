<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Artikel;
use App\Models\KategoriArtikel;

class ArtikelOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Artikel', Artikel::count())
                ->description('Jumlah artikel yang dipublikasikan')
                ->descriptionIcon('heroicon-m-document-text')
                ->chart([4, 12, 8, 15, 9, 13, 7])
                ->color('primary'),

            Stat::make('Kategori Artikel', KategoriArtikel::count())
                ->description('Kategori konten yang tersedia')
                ->descriptionIcon('heroicon-m-tag')
                ->color('warning'),

            Stat::make('Artikel Aktif', Artikel::where('status', 'published')->count())
                ->description('Artikel dalam status published')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
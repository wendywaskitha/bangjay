<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets;
use App\Filament\Widgets\SebaranBantuanOverview;
use App\Filament\Widgets\ArtikelOverview;
use App\Filament\Widgets\KelompokTaniOverview;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            Widgets\AccountWidget::class,
            // Widgets\FilamentInfoWidget::class,
            SebaranBantuanOverview::class,
            ArtikelOverview::class,
            KelompokTaniOverview::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }
}

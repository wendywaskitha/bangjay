<?php

namespace App\Filament\Resources\KatalogBantuanResource\Pages;

use App\Filament\Resources\KatalogBantuanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKatalogBantuan extends ViewRecord
{
    protected static string $resource = KatalogBantuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

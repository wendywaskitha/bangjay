<?php

namespace App\Filament\Resources\KatalogBantuanResource\Pages;

use App\Filament\Resources\KatalogBantuanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKatalogBantuan extends EditRecord
{
    protected static string $resource = KatalogBantuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

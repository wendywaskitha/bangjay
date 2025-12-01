<?php

namespace App\Filament\Resources\KategoriBantuanResource\Pages;

use App\Filament\Resources\KategoriBantuanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategoriBantuan extends EditRecord
{
    protected static string $resource = KategoriBantuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

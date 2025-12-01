<?php

namespace App\Filament\Resources\KategoriBantuanResource\Pages;

use App\Filament\Resources\KategoriBantuanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoriBantuans extends ListRecords
{
    protected static string $resource = KategoriBantuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

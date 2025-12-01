<?php

namespace App\Filament\Resources\JenisKomoditasResource\Pages;

use App\Filament\Resources\JenisKomoditasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJenisKomoditas extends ListRecords
{
    protected static string $resource = JenisKomoditasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

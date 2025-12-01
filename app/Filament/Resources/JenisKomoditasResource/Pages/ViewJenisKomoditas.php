<?php

namespace App\Filament\Resources\JenisKomoditasResource\Pages;

use App\Filament\Resources\JenisKomoditasResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewJenisKomoditas extends ViewRecord
{
    protected static string $resource = JenisKomoditasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

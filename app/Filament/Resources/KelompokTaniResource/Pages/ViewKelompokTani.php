<?php

namespace App\Filament\Resources\KelompokTaniResource\Pages;

use App\Filament\Resources\KelompokTaniResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKelompokTani extends ViewRecord
{
    protected static string $resource = KelompokTaniResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

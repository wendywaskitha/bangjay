<?php

namespace App\Filament\Resources\SebaranBantuanResource\Pages;

use App\Filament\Resources\SebaranBantuanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSebaranBantuan extends ViewRecord
{
    protected static string $resource = SebaranBantuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

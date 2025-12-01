<?php

namespace App\Filament\Resources\ProfilBangJaiResource\Pages;

use App\Filament\Resources\ProfilBangJaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProfilBangJai extends ViewRecord
{
    protected static string $resource = ProfilBangJaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

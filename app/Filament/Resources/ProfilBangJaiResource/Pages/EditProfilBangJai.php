<?php

namespace App\Filament\Resources\ProfilBangJaiResource\Pages;

use App\Filament\Resources\ProfilBangJaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProfilBangJai extends EditRecord
{
    protected static string $resource = ProfilBangJaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

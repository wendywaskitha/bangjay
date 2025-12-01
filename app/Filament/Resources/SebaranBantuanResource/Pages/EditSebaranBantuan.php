<?php

namespace App\Filament\Resources\SebaranBantuanResource\Pages;

use App\Filament\Resources\SebaranBantuanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSebaranBantuan extends EditRecord
{
    protected static string $resource = SebaranBantuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

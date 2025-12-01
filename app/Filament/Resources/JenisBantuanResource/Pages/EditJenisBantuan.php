<?php

namespace App\Filament\Resources\JenisBantuanResource\Pages;

use App\Filament\Resources\JenisBantuanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJenisBantuan extends EditRecord
{
    protected static string $resource = JenisBantuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

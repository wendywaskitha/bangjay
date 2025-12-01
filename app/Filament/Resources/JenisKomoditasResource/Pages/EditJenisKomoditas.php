<?php

namespace App\Filament\Resources\JenisKomoditasResource\Pages;

use App\Filament\Resources\JenisKomoditasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJenisKomoditas extends EditRecord
{
    protected static string $resource = JenisKomoditasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

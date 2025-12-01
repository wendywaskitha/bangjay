<?php

namespace App\Filament\Resources\ProfilBangJaiResource\Pages;

use App\Filament\Resources\ProfilBangJaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProfilBangJais extends ListRecords
{
    protected static string $resource = ProfilBangJaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Settings\Forms;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class AppearanceForm
{
    public static function getTab(): Tab
    {
        return Tab::make('appearance')
            ->label('Tampilan')
            ->icon('heroicon-o-swatch')
            ->schema([
                self::getAppearanceSection(),
            ])
            ->columns()
            ->statePath('appearance')
            ->visible(self::canAccess());
    }

    public static function canAccess(): bool
    {
        // Implementasi akses jika diperlukan
        return true;
    }

    public static function getAppearanceSection(): Section
    {
        return Section::make('Pengaturan Tampilan')
            ->label('Tampilan')
            ->description('Pengaturan tampilan untuk halaman depan')
            ->schema([
                TextInput::make('footer_text')
                    ->label('Teks Footer')
                    ->maxLength(500)
                    ->columnSpanFull()
                    ->helperText('Teks yang akan ditampilkan di footer halaman publik'),
                TextInput::make('hero_default_title')
                    ->label('Judul Hero Default')
                    ->maxLength(255)
                    ->helperText('Judul yang akan ditampilkan jika tidak ada hero banner'),
            ])
            ->columns(1)->collapsible();
    }

    public static function getSortOrder(): int
    {
        return 3;
    }
}
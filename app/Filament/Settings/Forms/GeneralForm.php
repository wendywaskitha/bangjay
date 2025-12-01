<?php

namespace App\Filament\Settings\Forms;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class GeneralForm
{
    public static function getTab(): Tab
    {
        return Tab::make('general')
            ->label('Umum')
            ->icon('heroicon-o-cog')
            ->schema([
                self::getAppSection(),
            ])
            ->columns()
            ->statePath('general')
            ->visible(self::canAccess());
    }

    public static function canAccess(): bool
    {
        // Implementasi akses jika diperlukan
        return true;
    }

    public static function getAppSection(): Section
    {
        return Section::make('Pengaturan Umum')
            ->label('Pengaturan Umum')
            ->description('Pengaturan dasar untuk aplikasi Rumah Aspirasi Bang Jai')
            ->schema([
                TextInput::make('app_name')
                    ->label('Nama Aplikasi')
                    ->default('Rumah Aspirasi Bang Jai')
                    ->maxLength(255)
                    ->required()
                    ->columnSpanFull(),
                Grid::make()->schema([
                    FileUpload::make('app_logo')
                        ->label('Logo Aplikasi')
                        ->image()
                        ->directory('app-logos')
                        ->visibility('public')
                        ->moveFiles()
                        ->imageEditor()
                        ->getUploadedFileNameForStorageUsing(fn () => 'app_logo.png'),
                    FileUpload::make('app_favicon')
                        ->label('Favicon')
                        ->image()
                        ->directory('app-favicons')
                        ->visibility('public')
                        ->moveFiles()
                        ->getUploadedFileNameForStorageUsing(fn () => 'app_favicon.ico')
                        ->acceptedFileTypes(['image/x-icon', 'image/vnd.microsoft.icon']),
                ])->columns(2),
            ])
            ->columns(3)->collapsible();
    }

    public static function getSortOrder(): int
    {
        return 1;
    }
}
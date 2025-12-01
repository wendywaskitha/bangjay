<?php

namespace App\Filament\Settings\Forms;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Http;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class ContactForm
{
    public static function getTab(): Tab
    {
        return Tab::make('contact')
            ->label('Kontak')
            ->icon('heroicon-o-envelope')
            ->schema([
                self::getContactSection(),
            ])
            ->columns()
            ->statePath('contact')
            ->visible(self::canAccess());
    }

    public static function canAccess(): bool
    {
        // Implementasi akses jika diperlukan
        return true;
    }

    public static function getContactSection(): Section
    {
        return Section::make('Kontak Bang Jai')
            ->label('Kontak')
            ->description('Informasi kontak untuk Bang Jai')
            ->schema([
                Grid::make()->schema([
                    TextInput::make('alamat_kantor')
                        ->label('Alamat Kantor')
                        ->maxLength(500)
                        ->columnSpanFull(),
                    TextInput::make('no_telepon')
                        ->label('Nomor Telepon')
                        ->tel()
                        ->maxLength(20),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255),
                    TextInput::make('link_whatsapp')
                        ->label('Link WhatsApp')
                        ->maxLength(255)
                        ->url(),
                    TextInput::make('link_facebook')
                        ->label('Link Facebook')
                        ->maxLength(255)
                        ->url(),
                    TextInput::make('link_youtube')
                        ->label('Link YouTube')
                        ->maxLength(255)
                        ->url(),
                ])->columns(2),
            ])
            ->columns(3)->collapsible();
    }

    public static function getSortOrder(): int
    {
        return 2;
    }
}
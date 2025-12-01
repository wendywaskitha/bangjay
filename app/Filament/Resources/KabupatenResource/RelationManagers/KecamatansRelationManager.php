<?php

namespace App\Filament\Resources\KabupatenResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KecamatansRelationManager extends RelationManager
{
    protected static string $relationship = 'kecamatans';

    protected static ?string $title = 'Daftar Kecamatan';

    protected static ?string $icon = 'heroicon-o-building-office';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_kecamatan')
                    ->label('Nama Kecamatan')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: Kusambi')
                    ->prefixIcon('heroicon-o-building-office')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('kode_kecamatan')
                    ->label('Kode Kecamatan')
                    ->maxLength(255)
                    ->placeholder('Contoh: KSM')
                    ->prefixIcon('heroicon-o-hashtag')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_kecamatan')
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->rowIndex()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('nama_kecamatan')
                    ->label('Nama Kecamatan')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-building-office')
                    ->iconColor('primary')
                    ->weight('medium')
                    ->description(fn ($record): string =>
                        $record->kode_kecamatan ? "Kode: {$record->kode_kecamatan}" : 'Belum ada kode'
                    ),

                Tables\Columns\TextColumn::make('kode_kecamatan')
                    ->label('Kode')
                    ->badge()
                    ->color('info')
                    ->default('-')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('desas_count')
                    ->label('Desa/Kel')
                    ->counts('desas')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-home-modern')
                    ->alignCenter()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Kecamatan')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Tambah Kecamatan Baru')
                    ->successNotificationTitle('Kecamatan berhasil ditambahkan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Edit Kecamatan'),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Kecamatan')
                    ->modalDescription('Data desa/kelurahan terkait mungkin terpengaruh.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Kecamatan')
            ->emptyStateDescription('Tambahkan kecamatan untuk kabupaten ini.')
            ->emptyStateIcon('heroicon-o-building-office')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Kecamatan')
                    ->icon('heroicon-o-plus'),
            ]);
    }
}

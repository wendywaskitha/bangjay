<?php

namespace App\Filament\Resources\SebaranBantuanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JenisBantuanRelationManager extends RelationManager
{
    protected static string $relationship = 'jenisBantuans';
    protected static ?string $title = 'Jenis Bantuan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('jenis_bantuan_id')
                    ->options(\App\Models\JenisBantuan::pluck('nama_bantuan', 'id'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Jenis Bantuan'),
                Forms\Components\TextInput::make('pivot.volume')
                    ->numeric()
                    ->label('Volume'),
                Forms\Components\TextInput::make('pivot.satuan')
                    ->maxLength(50)
                    ->label('Satuan'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_bantuan')
            ->columns([
                Tables\Columns\TextColumn::make('nama_bantuan')
                    ->label('Nama Bantuan'),
                Tables\Columns\TextColumn::make('pivot.volume')
                    ->label('Volume')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pivot.satuan')
                    ->label('Satuan'),
                Tables\Columns\TextColumn::make('kategoriBantuan.nama_kategori')
                    ->label('Kategori Bantuan'),
                Tables\Columns\TextColumn::make('periode_tahun')
                    ->label('Periode Tahun')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->recordSelect(
                        fn($select) => $select->placeholder('Pilih jenis bantuan')
                    )
                    ->recordSelectOptionsQuery(function ($query) {
                        return $query->with('kategoriBantuan');
                    })
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('volume')
                            ->numeric()
                            ->label('Volume'),
                        Forms\Components\TextInput::make('satuan')
                            ->maxLength(50)
                            ->label('Satuan'),
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label('Hapus'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}

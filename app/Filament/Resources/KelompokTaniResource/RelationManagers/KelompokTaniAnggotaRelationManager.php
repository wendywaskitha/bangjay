<?php

namespace App\Filament\Resources\KelompokTaniResource\RelationManagers;

use App\Models\JenisKomoditas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KelompokTaniAnggotaRelationManager extends RelationManager
{
    protected static string $relationship = 'kelompokTaniAnggotas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_anggota')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Anggota'),
                Forms\Components\Select::make('jabatan')
                    ->options([
                        'Ketua' => 'Ketua',
                        'Sekretaris' => 'Sekretaris',
                        'Bendahara' => 'Bendahara',
                        'Anggota' => 'Anggota',
                    ])
                    ->required()
                    ->label('Jabatan'),
                Forms\Components\TextInput::make('no_hp')
                    ->maxLength(15)
                    ->label('No. HP'),
                Forms\Components\TextInput::make('luas_lahan')
                    ->numeric()
                    ->step(0.01)
                    ->label('Luas Lahan (Ha)'),
                Forms\Components\Select::make('jenis_komoditas_id')
                    ->relationship('jenisKomoditas', 'nama_komoditas')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Jenis Komoditas'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_anggota')
            ->columns([
                Tables\Columns\TextColumn::make('nama_anggota')
                    ->label('Nama Anggota'),
                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Jabatan'),
                Tables\Columns\TextColumn::make('no_hp')
                    ->label('No. HP'),
                Tables\Columns\TextColumn::make('luas_lahan')
                    ->label('Luas Lahan (Ha)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenisKomoditas.nama_komoditas')
                    ->label('Jenis Komoditas'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
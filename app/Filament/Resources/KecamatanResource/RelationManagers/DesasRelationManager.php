<?php

namespace App\Filament\Resources\KecamatanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DesasRelationManager extends RelationManager
{
    protected static string $relationship = 'desas';

    protected static ?string $title = 'Daftar Desa/Kelurahan';

    protected static ?string $icon = 'heroicon-o-home-modern';

    protected static ?string $recordTitleAttribute = 'nama_desa';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Desa/Kelurahan')
                    ->description('Isi data desa atau kelurahan dengan lengkap')
                    ->icon('heroicon-o-home-modern')
                    ->schema([
                        Forms\Components\TextInput::make('nama_desa')
                            ->label('Nama Desa/Kelurahan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Bangkali')
                            ->helperText('Masukkan nama desa/kelurahan tanpa kata "Desa" atau "Kelurahan"')
                            ->prefixIcon('heroicon-o-home')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                if ($state && !$get('kode_desa')) {
                                    $set('kode_desa', strtoupper(substr($state, 0, 4)));
                                }
                            })
                            ->columnSpanFull(),

                        Forms\Components\Select::make('tipe')
                            ->label('Tipe')
                            ->options([
                                'Desa' => 'Desa',
                                'Kelurahan' => 'Kelurahan',
                            ])
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-o-building-library')
                            ->default('Desa')
                            ->helperText('Pilih apakah ini Desa atau Kelurahan'),

                        Forms\Components\TextInput::make('kode_desa')
                            ->label('Kode Desa/Kelurahan')
                            ->maxLength(255)
                            ->placeholder('Contoh: BANG')
                            ->helperText('Kode akan otomatis dibuat, atau Anda bisa ubah manual')
                            ->prefixIcon('heroicon-o-hashtag')
                            ->alphaDash(),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_desa')
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->rowIndex()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('nama_desa')
                    ->label('Nama Desa/Kelurahan')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-home-modern')
                    ->iconColor('success')
                    ->weight('bold')
                    ->copyable()
                    ->description(fn ($record): string =>
                        $record->kode_desa ? "Kode: {$record->kode_desa}" : 'Belum ada kode'
                    ),

                Tables\Columns\TextColumn::make('tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'Desa' => 'success',
                        'Kelurahan' => 'info',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match($state) {
                        'Desa' => 'heroicon-o-home',
                        'Kelurahan' => 'heroicon-o-building-library',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('kode_desa')
                    ->label('Kode')
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-o-hashtag')
                    ->default('-')
                    ->alignCenter()
                    ->searchable(),

                Tables\Columns\TextColumn::make('kelompok_tanis_count')
                    ->label('Kelompok Tani')
                    ->counts('kelompokTanis')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-user-group')
                    ->suffix(' Kelompok')
                    ->alignCenter()
                    ->sortable()
                    ->tooltip(fn ($state): string => $state . ' kelompok tani terdaftar'),
            ])
            ->defaultSort('nama_desa', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('tipe')
                    ->label('Tipe')
                    ->options([
                        'Desa' => 'Desa',
                        'Kelurahan' => 'Kelurahan',
                    ])
                    ->native(false)
                    ->multiple(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Desa/Kelurahan')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Tambah Desa/Kelurahan Baru')
                    ->modalWidth('2xl')
                    ->successNotificationTitle('Desa/Kelurahan berhasil ditambahkan')
                    ->color('primary'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning')
                        ->modalHeading('Edit Desa/Kelurahan')
                        ->modalWidth('2xl'),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Desa/Kelurahan')
                        ->modalDescription('Data kelompok tani terkait mungkin terpengaruh.'),
                ])
                ->icon('heroicon-m-ellipsis-horizontal')
                ->tooltip('Aksi'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Desa/Kelurahan')
            ->emptyStateDescription('Tambahkan desa atau kelurahan untuk kecamatan ini.')
            ->emptyStateIcon('heroicon-o-home-modern')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Desa/Kelurahan')
                    ->icon('heroicon-o-plus'),
            ])
            ->striped();
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KabupatenResource\Pages;
use App\Filament\Resources\KabupatenResource\RelationManagers;
use App\Models\Kabupaten;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class KabupatenResource extends Resource
{
    protected static ?string $model = Kabupaten::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Kabupaten';

    protected static ?string $modelLabel = 'Kabupaten';

    protected static ?string $pluralModelLabel = 'Data Kabupaten';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'nama_kabupaten';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kabupaten')
                    ->description('Masukkan data kabupaten dengan lengkap dan benar')
                    ->icon('heroicon-o-map-pin')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('nama_kabupaten')
                            ->label('Nama Kabupaten')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Muna Barat')
                            ->helperText('Masukkan nama kabupaten tanpa kata "Kabupaten"')
                            ->prefixIcon('heroicon-o-building-office-2')
                            ->autocomplete(false)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                // Auto-generate kode jika kosong
                                if (empty($set)) {
                                    $set('kode_kabupaten', strtoupper(substr($state, 0, 3)));
                                }
                            })
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('kode_kabupaten')
                            ->label('Kode Kabupaten')
                            ->maxLength(255)
                            ->placeholder('Contoh: MNB atau 7404')
                            ->helperText('Kode singkatan kabupaten (opsional)')
                            ->prefixIcon('heroicon-o-hashtag')
                            ->alphaDash()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Tambahan')
                    ->description('Data statistik dan relasi terkait')
                    ->icon('heroicon-o-information-circle')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Dibuat pada')
                            ->content(fn (?Kabupaten $record): string => $record?->created_at?->diffForHumans() ?? '-')
                            ->visible(fn (?Kabupaten $record) => $record !== null),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Terakhir diubah')
                            ->content(fn (?Kabupaten $record): string => $record?->updated_at?->diffForHumans() ?? '-')
                            ->visible(fn (?Kabupaten $record) => $record !== null),

                        Forms\Components\Placeholder::make('jumlah_kecamatan')
                            ->label('Jumlah Kecamatan')
                            ->content(fn (?Kabupaten $record): string => $record ? number_format($record->kecamatans()->count()) . ' Kecamatan' : '-')
                            ->visible(fn (?Kabupaten $record) => $record !== null),
                    ])
                    ->columns(3)
                    ->visible(fn ($operation) => $operation === 'edit' || $operation === 'view'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->rowIndex()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('nama_kabupaten')
                    ->label('Nama Kabupaten')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-map-pin')
                    ->iconColor('primary')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nama kabupaten berhasil disalin!')
                    ->copyMessageDuration(1500)
                    ->tooltip('Klik untuk menyalin')
                    ->description(fn (Kabupaten $record): string =>
                        $record->kode_kabupaten ? "Kode: {$record->kode_kabupaten}" : 'Belum ada kode'
                    ),

                Tables\Columns\TextColumn::make('kode_kabupaten')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-hashtag')
                    ->default('-')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('kecamatans_count')
                    ->label('Kecamatan')
                    ->counts('kecamatans')
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state == 0 => 'gray',
                        $state <= 5 => 'warning',
                        $state > 5 => 'success',
                    })
                    ->icon('heroicon-o-building-office')
                    ->suffix(' Kecamatan')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn ($state): string => $state . ' kecamatan terdaftar'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-calendar')
                    ->tooltip(fn ($state): string => $state->diffForHumans()),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock')
                    ->tooltip(fn ($state): string => $state->diffForHumans())
                    ->since(),
            ])
            ->defaultSort('nama_kabupaten', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('kode_kabupaten')
                    ->label('Memiliki Kode')
                    ->placeholder('Semua Kabupaten')
                    ->trueLabel('Dengan Kode')
                    ->falseLabel('Tanpa Kode')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('kode_kabupaten'),
                        false: fn (Builder $query) => $query->whereNull('kode_kabupaten'),
                        blank: fn (Builder $query) => $query,
                    )
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Kabupaten')
                        ->modalDescription('Apakah Anda yakin ingin menghapus data kabupaten ini? Data kecamatan dan desa terkait mungkin terpengaruh.')
                        ->modalSubmitActionLabel('Ya, Hapus'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Aksi')
                ->button()
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Kabupaten Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua kabupaten yang dipilih?')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Data Kabupaten')
            ->emptyStateDescription('Mulai dengan menambahkan kabupaten pertama Anda.')
            ->emptyStateIcon('heroicon-o-map')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Kabupaten')
                    ->icon('heroicon-o-plus')
                    ->color('primary'),
            ])
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Kabupaten')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Infolists\Components\TextEntry::make('nama_kabupaten')
                            ->label('Nama Kabupaten')
                            ->icon('heroicon-o-map-pin')
                            ->iconColor('primary')
                            ->size('lg')
                            ->weight('bold')
                            ->copyable()
                            ->copyMessage('Tersalin!')
                            ->color('primary'),

                        Infolists\Components\TextEntry::make('kode_kabupaten')
                            ->label('Kode Kabupaten')
                            ->icon('heroicon-o-hashtag')
                            ->badge()
                            ->color('success')
                            ->default('-'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Statistik & Data Terkait')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Infolists\Components\TextEntry::make('kecamatans_count')
                            ->label('Jumlah Kecamatan')
                            ->state(fn (Kabupaten $record): int => $record->kecamatans()->count())
                            ->badge()
                            ->icon('heroicon-o-building-office')
                            ->color('info')
                            ->suffix(' Kecamatan'),

                        Infolists\Components\TextEntry::make('desas_count')
                            ->label('Jumlah Desa/Kelurahan')
                            ->state(fn (Kabupaten $record): int => $record->kecamatans()->withCount('desas')->get()->sum('desas_count'))
                            ->badge()
                            ->icon('heroicon-o-home-modern')
                            ->color('warning')
                            ->suffix(' Desa/Kel'),

                        Infolists\Components\TextEntry::make('kelompok_tani_count')
                            ->label('Jumlah Kelompok Tani')
                            ->state(fn (Kabupaten $record): int =>
                                \App\Models\KelompokTani::whereHas('desa.kecamatan', function($q) use ($record) {
                                    $q->where('kabupaten_id', $record->id);
                                })->count()
                            )
                            ->badge()
                            ->icon('heroicon-o-user-group')
                            ->color('success')
                            ->suffix(' Kelompok'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Riwayat Data')
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Dibuat pada')
                            ->dateTime('d F Y, H:i:s')
                            ->icon('heroicon-o-calendar-days')
                            ->since(),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Terakhir diperbarui')
                            ->dateTime('d F Y, H:i:s')
                            ->icon('heroicon-o-arrow-path')
                            ->since(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\KecamatansRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKabupatens::route('/'),
            'create' => Pages\CreateKabupaten::route('/create'),
            'view' => Pages\ViewKabupaten::route('/{record}'),
            'edit' => Pages\EditKabupaten::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'success' : 'gray';
    }
}

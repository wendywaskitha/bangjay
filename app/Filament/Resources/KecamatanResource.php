<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KecamatanResource\Pages;
use App\Filament\Resources\KecamatanResource\RelationManagers;
use App\Models\Kecamatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class KecamatanResource extends Resource
{
    protected static ?string $model = Kecamatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Kecamatan';

    protected static ?string $modelLabel = 'Kecamatan';

    protected static ?string $pluralModelLabel = 'Data Kecamatan';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'nama_kecamatan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Wilayah')
                    ->description('Pilih kabupaten dan isi data kecamatan')
                    ->icon('heroicon-o-map')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Select::make('kabupaten_id')
                            ->relationship('kabupaten', 'nama_kabupaten')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Kabupaten')
                            ->placeholder('Pilih Kabupaten')
                            ->helperText('Pilih kabupaten tempat kecamatan ini berada')
                            ->prefixIcon('heroicon-o-map-pin')
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                // Auto-generate kode jika nama kecamatan sudah diisi
                                if ($state && $get('nama_kecamatan')) {
                                    $kabupaten = \App\Models\Kabupaten::find($state);
                                    $namaKecamatan = $get('nama_kecamatan');
                                    $kodeKab = $kabupaten?->kode_kabupaten ?? substr($kabupaten?->nama_kabupaten ?? '', 0, 2);
                                    $kodeKec = substr($namaKecamatan, 0, 3);
                                    $set('kode_kecamatan', strtoupper($kodeKab . '-' . $kodeKec));
                                }
                            })
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('nama_kecamatan')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Kecamatan')
                            ->placeholder('Contoh: Kusambi')
                            ->helperText('Masukkan nama kecamatan tanpa kata "Kecamatan"')
                            ->prefixIcon('heroicon-o-building-office')
                            ->autocomplete(false)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                // Auto-generate kode
                                $kabupatenId = $get('kabupaten_id');
                                if ($state && $kabupatenId) {
                                    $kabupaten = \App\Models\Kabupaten::find($kabupatenId);
                                    $kodeKab = $kabupaten?->kode_kabupaten ?? substr($kabupaten?->nama_kabupaten ?? '', 0, 2);
                                    $kodeKec = substr($state, 0, 3);
                                    $set('kode_kecamatan', strtoupper($kodeKab . '-' . $kodeKec));
                                }
                            })
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('kode_kecamatan')
                            ->maxLength(255)
                            ->label('Kode Kecamatan')
                            ->placeholder('Contoh: MNB-KUS')
                            ->helperText('Kode akan otomatis dibuat, atau Anda bisa ubah manual')
                            ->prefixIcon('heroicon-o-hashtag')
                            ->alphaDash()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Tambahan')
                    ->description('Data statistik dan relasi terkait')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('jumlah_desa')
                            ->label('Jumlah Desa/Kelurahan')
                            ->content(fn (?Kecamatan $record): string =>
                                $record ? number_format($record->desas()->count()) . ' Desa/Kelurahan' : '-'
                            )
                            ->visible(fn (?Kecamatan $record) => $record !== null),

                        Forms\Components\Placeholder::make('jumlah_kelompok')
                            ->label('Jumlah Kelompok Tani')
                            ->content(fn (?Kecamatan $record): string =>
                                $record ? number_format(\App\Models\KelompokTani::whereHas('desa', function($q) use ($record) {
                                    $q->where('kecamatan_id', $record->id);
                                })->count()) . ' Kelompok' : '-'
                            )
                            ->visible(fn (?Kecamatan $record) => $record !== null),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Dibuat pada')
                            ->content(fn (?Kecamatan $record): string =>
                                $record?->created_at?->diffForHumans() ?? '-'
                            )
                            ->visible(fn (?Kecamatan $record) => $record !== null),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Terakhir diubah')
                            ->content(fn (?Kecamatan $record): string =>
                                $record?->updated_at?->diffForHumans() ?? '-'
                            )
                            ->visible(fn (?Kecamatan $record) => $record !== null),
                    ])
                    ->columns(4)
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

                Tables\Columns\TextColumn::make('kabupaten.nama_kabupaten')
                    ->searchable()
                    ->sortable()
                    ->label('Kabupaten')
                    ->icon('heroicon-o-map-pin')
                    ->iconColor('primary')
                    ->weight('medium')
                    ->copyable()
                    ->copyMessage('Nama kabupaten tersalin!')
                    ->copyMessageDuration(1500)
                    ->description(fn (Kecamatan $record): string =>
                        $record->kabupaten?->kode_kabupaten
                            ? "Kode: {$record->kabupaten->kode_kabupaten}"
                            : 'Belum ada kode'
                    )
                    ->tooltip('Klik untuk menyalin'),

                Tables\Columns\TextColumn::make('nama_kecamatan')
                    ->searchable()
                    ->label('Nama Kecamatan')
                    ->icon('heroicon-o-building-office')
                    ->iconColor('success')
                    ->weight('bold')
                    ->copyable()
                    ->description(fn (Kecamatan $record): string =>
                        $record->kode_kecamatan ? "Kode: {$record->kode_kecamatan}" : 'Belum ada kode'
                    ),

                Tables\Columns\TextColumn::make('kode_kecamatan')
                    ->searchable()
                    ->label('Kode')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-hashtag')
                    ->default('-')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('desas_count')
                    ->label('Desa/Kel')
                    ->counts('desas')
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state == 0 => 'gray',
                        $state <= 5 => 'warning',
                        $state <= 10 => 'info',
                        $state > 10 => 'success',
                    })
                    ->icon('heroicon-o-home-modern')
                    ->suffix(' Desa/Kel')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn ($state): string => $state . ' desa/kelurahan terdaftar'),

                Tables\Columns\TextColumn::make('kelompok_tani_count')
                    ->label('Kelompok')
                    ->state(function (Kecamatan $record): int {
                        return \App\Models\KelompokTani::whereHas('desa', function($q) use ($record) {
                            $q->where('kecamatan_id', $record->id);
                        })->count();
                    })
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-user-group')
                    ->alignCenter()
                    ->tooltip(fn ($state): string => $state . ' kelompok tani'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->label('Dibuat')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-calendar')
                    ->tooltip(fn ($state): string => $state->diffForHumans()),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d M Y, H:i')
                    ->label('Diperbarui')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock')
                    ->tooltip(fn ($state): string => $state->diffForHumans())
                    ->since(),
            ])
            ->defaultSort('nama_kecamatan', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('kabupaten_id')
                    ->label('Kabupaten')
                    ->relationship('kabupaten', 'nama_kabupaten')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('Semua Kabupaten')
                    ->native(false)
                    ->indicator('Kabupaten'),

                Tables\Filters\TernaryFilter::make('has_desa')
                    ->label('Memiliki Desa')
                    ->placeholder('Semua Kecamatan')
                    ->trueLabel('Dengan Desa/Kelurahan')
                    ->falseLabel('Tanpa Desa/Kelurahan')
                    ->queries(
                        true: fn (Builder $query) => $query->has('desas'),
                        false: fn (Builder $query) => $query->doesntHave('desas'),
                    )
                    ->native(false),

                Tables\Filters\TernaryFilter::make('kode_kecamatan')
                    ->label('Memiliki Kode')
                    ->placeholder('Semua Kecamatan')
                    ->trueLabel('Dengan Kode')
                    ->falseLabel('Tanpa Kode')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('kode_kecamatan'),
                        false: fn (Builder $query) => $query->whereNull('kode_kecamatan'),
                    )
                    ->native(false),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->persistFiltersInSession()
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info')
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\EditAction::make()
                        ->color('warning')
                        ->icon('heroicon-o-pencil'),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Kecamatan')
                        ->modalDescription('Apakah Anda yakin ingin menghapus kecamatan ini? Data desa/kelurahan dan kelompok tani terkait mungkin terpengaruh.')
                        ->modalSubmitActionLabel('Ya, Hapus')
                        ->icon('heroicon-o-trash'),
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
                        ->modalHeading('Hapus Kecamatan Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua kecamatan yang dipilih? Data terkait mungkin terpengaruh.')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Data Kecamatan')
            ->emptyStateDescription('Mulai dengan menambahkan kecamatan pertama Anda.')
            ->emptyStateIcon('heroicon-o-building-office')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Kecamatan')
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
                Infolists\Components\Section::make('Informasi Wilayah')
                    ->icon('heroicon-o-map')
                    ->schema([
                        Infolists\Components\TextEntry::make('kabupaten.nama_kabupaten')
                            ->label('Kabupaten')
                            ->icon('heroicon-o-map-pin')
                            ->iconColor('primary')
                            ->badge()
                            ->color('primary')
                            ->size('lg')
                            ->copyable()
                            ->copyMessage('Tersalin!')
                            ->url(fn (Kecamatan $record) =>
                                $record->kabupaten ? route('filament.admin.resources.kabupatens.view', $record->kabupaten) : null
                            ),

                        Infolists\Components\TextEntry::make('nama_kecamatan')
                            ->label('Nama Kecamatan')
                            ->icon('heroicon-o-building-office')
                            ->iconColor('success')
                            ->size('lg')
                            ->weight('bold')
                            ->copyable()
                            ->color('success'),

                        Infolists\Components\TextEntry::make('kode_kecamatan')
                            ->label('Kode Kecamatan')
                            ->icon('heroicon-o-hashtag')
                            ->badge()
                            ->color('info')
                            ->default('-'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Statistik & Data Terkait')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('desas_count')
                                    ->label('Jumlah Desa/Kelurahan')
                                    ->state(fn (Kecamatan $record): int => $record->desas()->count())
                                    ->badge()
                                    ->icon('heroicon-o-home-modern')
                                    ->color('warning')
                                    ->suffix(' Desa/Kel')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('kelompok_tani_count')
                                    ->label('Jumlah Kelompok Tani')
                                    ->state(fn (Kecamatan $record): int =>
                                        \App\Models\KelompokTani::whereHas('desa', function($q) use ($record) {
                                            $q->where('kecamatan_id', $record->id);
                                        })->count()
                                    )
                                    ->badge()
                                    ->icon('heroicon-o-user-group')
                                    ->color('success')
                                    ->suffix(' Kelompok')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('anggota_count')
                                    ->label('Total Anggota Kelompok Tani')
                                    ->state(fn (Kecamatan $record): int =>
                                        \App\Models\KelompokTani::whereHas('desa', function($q) use ($record) {
                                            $q->where('kecamatan_id', $record->id);
                                        })->sum('jumlah_anggota')
                                    )
                                    ->badge()
                                    ->icon('heroicon-o-users')
                                    ->color('info')
                                    ->suffix(' Anggota')
                                    ->size('lg'),
                            ]),
                    ]),

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
            RelationManagers\DesasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKecamatans::route('/'),
            'create' => Pages\CreateKecamatan::route('/create'),
            'view' => Pages\ViewKecamatan::route('/{record}'),
            'edit' => Pages\EditKecamatan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::count();
        return match(true) {
            $count == 0 => 'gray',
            $count < 10 => 'warning',
            default => 'success',
        };
    }
}

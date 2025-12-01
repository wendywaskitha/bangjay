<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DesaResource\Pages;
use App\Filament\Resources\DesaResource\RelationManagers;
use App\Models\Desa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Forms\Get;

class DesaResource extends Resource
{
    protected static ?string $model = Desa::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationLabel = 'Desa/Kelurahan';

    protected static ?string $modelLabel = 'Desa/Kelurahan';

    protected static ?string $pluralModelLabel = 'Data Desa/Kelurahan';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'nama_desa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Wilayah')
                    ->description('Pilih wilayah dan isi data desa/kelurahan')
                    ->icon('heroicon-o-map')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('kabupaten_id')
                                    ->label('Kabupaten')
                                    ->options(\App\Models\Kabupaten::pluck('nama_kabupaten', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set) {
                                        $set('kecamatan_id', null);
                                    })
                                    ->placeholder('Pilih Kabupaten')
                                    ->helperText('Pilih kabupaten terlebih dahulu')
                                    ->prefixIcon('heroicon-o-map-pin')
                                    ->native(false)
                                    ->columnSpan(1),

                                Forms\Components\Select::make('kecamatan_id')
                                    ->relationship('kecamatan', 'nama_kecamatan')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Kecamatan')
                                    ->placeholder(fn (Get $get): string =>
                                        $get('kabupaten_id')
                                            ? 'Pilih Kecamatan'
                                            : 'Pilih Kabupaten dulu'
                                    )
                                    ->helperText('Kecamatan akan tampil setelah kabupaten dipilih')
                                    ->prefixIcon('heroicon-o-building-office')
                                    ->native(false)
                                    ->disabled(fn (Get $get): bool => !$get('kabupaten_id'))
                                    ->options(fn (Get $get) =>
                                        $get('kabupaten_id')
                                            ? \App\Models\Kecamatan::where('kabupaten_id', $get('kabupaten_id'))
                                                ->pluck('nama_kecamatan', 'id')
                                            : []
                                    )
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        if ($state && $get('nama_desa')) {
                                            $kecamatan = \App\Models\Kecamatan::find($state);
                                            $namaDesa = $get('nama_desa');
                                            $kodeKec = $kecamatan?->kode_kecamatan ?? substr($kecamatan?->nama_kecamatan ?? '', 0, 3);
                                            $kodeDesa = substr($namaDesa, 0, 4);
                                            $set('kode_desa', strtoupper($kodeKec . '-' . $kodeDesa));
                                        }
                                    })
                                    ->columnSpan(1),
                            ]),
                    ]),

                Forms\Components\Section::make('Informasi Desa/Kelurahan')
                    ->description('Data detail desa atau kelurahan')
                    ->icon('heroicon-o-home')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('nama_desa')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Desa/Kelurahan')
                            ->placeholder('Contoh: Bangkali')
                            ->helperText('Masukkan nama desa/kelurahan tanpa kata "Desa" atau "Kelurahan"')
                            ->prefixIcon('heroicon-o-home')
                            ->autocomplete(false)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                $kecamatanId = $get('kecamatan_id');
                                if ($state && $kecamatanId) {
                                    $kecamatan = \App\Models\Kecamatan::find($kecamatanId);
                                    $kodeKec = $kecamatan?->kode_kecamatan ?? substr($kecamatan?->nama_kecamatan ?? '', 0, 3);
                                    $kodeDesa = substr($state, 0, 4);
                                    $set('kode_desa', strtoupper($kodeKec . '-' . $kodeDesa));
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\Select::make('tipe')
                            ->options([
                                'Desa' => 'Desa',
                                'Kelurahan' => 'Kelurahan',
                            ])
                            ->required()
                            ->label('Tipe')
                            ->native(false)
                            ->default('Desa')
                            ->prefixIcon('heroicon-o-building-library')
                            ->helperText('Pilih tipe wilayah administrasi')
                            ->live()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('kode_desa')
                            ->maxLength(255)
                            ->label('Kode Desa/Kelurahan')
                            ->placeholder('Contoh: KUS-BANG')
                            ->helperText('Kode akan otomatis dibuat, atau Anda bisa ubah manual')
                            ->prefixIcon('heroicon-o-hashtag')
                            ->alphaDash()
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Tambahan')
                    ->description('Data statistik dan relasi terkait')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('wilayah_lengkap')
                            ->label('Wilayah Lengkap')
                            ->content(fn (?Desa $record): string =>
                                $record
                                    ? "{$record->nama_desa}, Kec. {$record->kecamatan->nama_kecamatan}, Kab. {$record->kecamatan->kabupaten->nama_kabupaten}"
                                    : '-'
                            )
                            ->visible(fn (?Desa $record) => $record !== null),

                        Forms\Components\Placeholder::make('jumlah_kelompok')
                            ->label('Jumlah Kelompok Tani')
                            ->content(fn (?Desa $record): string =>
                                $record
                                    ? number_format($record->kelompokTanis()->count()) . ' Kelompok'
                                    : '-'
                            )
                            ->visible(fn (?Desa $record) => $record !== null),

                        Forms\Components\Placeholder::make('total_anggota')
                            ->label('Total Anggota Kelompok Tani')
                            ->content(fn (?Desa $record): string =>
                                $record
                                    ? number_format($record->kelompokTanis()->sum('jumlah_anggota')) . ' Anggota'
                                    : '-'
                            )
                            ->visible(fn (?Desa $record) => $record !== null),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Dibuat pada')
                            ->content(fn (?Desa $record): string =>
                                $record?->created_at?->diffForHumans() ?? '-'
                            )
                            ->visible(fn (?Desa $record) => $record !== null),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Terakhir diubah')
                            ->content(fn (?Desa $record): string =>
                                $record?->updated_at?->diffForHumans() ?? '-'
                            )
                            ->visible(fn (?Desa $record) => $record !== null),
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

                Tables\Columns\TextColumn::make('kecamatan.kabupaten.nama_kabupaten')
                    ->label('Kabupaten')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-map-pin')
                    ->iconColor('primary')
                    ->weight('medium')
                    ->toggleable()
                    ->copyable()
                    ->tooltip('Klik untuk menyalin'),

                Tables\Columns\TextColumn::make('kecamatan.nama_kecamatan')
                    ->searchable()
                    ->sortable()
                    ->label('Kecamatan')
                    ->icon('heroicon-o-building-office')
                    ->iconColor('success')
                    ->weight('medium')
                    ->description(fn (Desa $record): string =>
                        $record->kecamatan?->kode_kecamatan
                            ? "Kode: {$record->kecamatan->kode_kecamatan}"
                            : 'Belum ada kode'
                    )
                    ->copyable(),

                Tables\Columns\TextColumn::make('nama_desa')
                    ->searchable()
                    ->label('Nama Desa/Kelurahan')
                    ->icon('heroicon-o-home-modern')
                    ->iconColor('warning')
                    ->weight('bold')
                    ->copyable()
                    ->description(fn (Desa $record): string =>
                        $record->kode_desa ? "Kode: {$record->kode_desa}" : 'Belum ada kode'
                    )
                    ->sortable(),

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
                    ->searchable()
                    ->label('Kode')
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-o-hashtag')
                    ->default('-')
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('kelompok_tanis_count')
                    ->label('Kelompok')
                    ->counts('kelompokTanis')
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state == 0 => 'gray',
                        $state <= 3 => 'warning',
                        $state <= 6 => 'info',
                        $state > 6 => 'success',
                    })
                    ->icon('heroicon-o-user-group')
                    ->suffix(' Kelompok')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn ($state): string => $state . ' kelompok tani terdaftar'),

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
            ->defaultSort('nama_desa', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('kabupaten')
                    ->label('Kabupaten')
                    ->relationship('kecamatan.kabupaten', 'nama_kabupaten')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('Semua Kabupaten')
                    ->native(false)
                    ->indicator('Kabupaten'),

                Tables\Filters\SelectFilter::make('kecamatan_id')
                    ->label('Kecamatan')
                    ->relationship('kecamatan', 'nama_kecamatan')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('Semua Kecamatan')
                    ->native(false)
                    ->indicator('Kecamatan'),

                Tables\Filters\SelectFilter::make('tipe')
                    ->label('Tipe')
                    ->options([
                        'Desa' => 'Desa',
                        'Kelurahan' => 'Kelurahan',
                    ])
                    ->native(false)
                    ->multiple()
                    ->placeholder('Semua Tipe')
                    ->indicator('Tipe'),

                Tables\Filters\TernaryFilter::make('has_kelompok')
                    ->label('Memiliki Kelompok Tani')
                    ->placeholder('Semua Desa/Kelurahan')
                    ->trueLabel('Dengan Kelompok Tani')
                    ->falseLabel('Tanpa Kelompok Tani')
                    ->queries(
                        true: fn (Builder $query) => $query->has('kelompokTanis'),
                        false: fn (Builder $query) => $query->doesntHave('kelompokTanis'),
                    )
                    ->native(false),

                Tables\Filters\TernaryFilter::make('kode_desa')
                    ->label('Memiliki Kode')
                    ->placeholder('Semua')
                    ->trueLabel('Dengan Kode')
                    ->falseLabel('Tanpa Kode')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('kode_desa'),
                        false: fn (Builder $query) => $query->whereNull('kode_desa'),
                    )
                    ->native(false),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)
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
                        ->modalHeading('Hapus Desa/Kelurahan')
                        ->modalDescription('Apakah Anda yakin ingin menghapus desa/kelurahan ini? Data kelompok tani terkait mungkin terpengaruh.')
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
                        ->modalHeading('Hapus Desa/Kelurahan Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua desa/kelurahan yang dipilih? Data terkait mungkin terpengaruh.')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Data Desa/Kelurahan')
            ->emptyStateDescription('Mulai dengan menambahkan desa atau kelurahan pertama Anda.')
            ->emptyStateIcon('heroicon-o-home-modern')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Desa/Kelurahan')
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
                Infolists\Components\Section::make('Informasi Wilayah Administrasi')
                    ->icon('heroicon-o-map')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('kecamatan.kabupaten.nama_kabupaten')
                                    ->label('Kabupaten')
                                    ->icon('heroicon-o-map-pin')
                                    ->iconColor('primary')
                                    ->badge()
                                    ->color('primary')
                                    ->size('lg')
                                    ->copyable()
                                    ->url(fn (Desa $record) =>
                                        $record->kecamatan?->kabupaten
                                            ? route('filament.admin.resources.kabupatens.view', $record->kecamatan->kabupaten)
                                            : null
                                    ),

                                Infolists\Components\TextEntry::make('kecamatan.nama_kecamatan')
                                    ->label('Kecamatan')
                                    ->icon('heroicon-o-building-office')
                                    ->iconColor('success')
                                    ->badge()
                                    ->color('success')
                                    ->size('lg')
                                    ->copyable()
                                    ->url(fn (Desa $record) =>
                                        $record->kecamatan
                                            ? route('filament.admin.resources.kecamatans.view', $record->kecamatan)
                                            : null
                                    ),

                                Infolists\Components\TextEntry::make('nama_desa')
                                    ->label('Desa/Kelurahan')
                                    ->icon('heroicon-o-home-modern')
                                    ->iconColor('warning')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->copyable()
                                    ->color('warning'),
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('tipe')
                                    ->label('Tipe Wilayah')
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
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('kode_desa')
                                    ->label('Kode Desa/Kelurahan')
                                    ->icon('heroicon-o-hashtag')
                                    ->badge()
                                    ->color('warning')
                                    ->default('-')
                                    ->size('lg'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Statistik & Data Terkait')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('kelompok_tanis_count')
                                    ->label('Jumlah Kelompok Tani')
                                    ->state(fn (Desa $record): int => $record->kelompokTanis()->count())
                                    ->badge()
                                    ->icon('heroicon-o-user-group')
                                    ->color('success')
                                    ->suffix(' Kelompok')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('total_anggota')
                                    ->label('Total Anggota Kelompok')
                                    ->state(fn (Desa $record): int => $record->kelompokTanis()->sum('jumlah_anggota'))
                                    ->badge()
                                    ->icon('heroicon-o-users')
                                    ->color('info')
                                    ->suffix(' Anggota')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('bantuan_count')
                                    ->label('Total Bantuan Tersalurkan')
                                    ->state(fn (Desa $record): int =>
                                        \App\Models\SebaranBantuan::whereHas('kelompokTani', function($q) use ($record) {
                                            $q->where('desa_id', $record->id);
                                        })->count()
                                    )
                                    ->badge()
                                    ->icon('heroicon-o-gift')
                                    ->color('warning')
                                    ->suffix(' Bantuan')
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
            RelationManagers\KelompokTaniRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDesas::route('/'),
            'create' => Pages\CreateDesa::route('/create'),
            'view' => Pages\ViewDesa::route('/{record}'),
            'edit' => Pages\EditDesa::route('/{record}/edit'),
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
            $count < 20 => 'warning',
            default => 'success',
        };
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Kecamatan' => $record->kecamatan?->nama_kecamatan,
            'Kabupaten' => $record->kecamatan?->kabupaten?->nama_kabupaten,
            'Tipe' => $record->tipe,
        ];
    }
}

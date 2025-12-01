<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\KelompokTani;
use Filament\Resources\Resource;
use Dotswan\MapPicker\Fields\Map;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KelompokTaniResource\Pages;
use App\Filament\Resources\KelompokTaniResource\RelationManagers;
use Illuminate\Support\Str;

class KelompokTaniResource extends Resource
{
    protected static ?string $model = KelompokTani::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Kelompok Tani';

    protected static ?string $modelLabel = 'Kelompok Tani';

    protected static ?string $pluralModelLabel = 'Data Kelompok Tani';

    protected static ?string $navigationGroup = 'Data Utama';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'nama_kelompok';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Wilayah')
                    ->description('Pilih lokasi administrasi kelompok tani')
                    ->icon('heroicon-o-map')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('kabupaten_id')
                                    ->label('Kabupaten')
                                    ->options(\App\Models\Kabupaten::pluck('nama_kabupaten', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('kecamatan_id', null);
                                        $set('desa_id', null);
                                    })
                                    ->placeholder('Pilih Kabupaten')
                                    ->prefixIcon('heroicon-o-map-pin')
                                    ->native(false),

                                Forms\Components\Select::make('kecamatan_id')
                                    ->label('Kecamatan')
                                    ->options(fn (Get $get) =>
                                        $get('kabupaten_id')
                                            ? \App\Models\Kecamatan::where('kabupaten_id', $get('kabupaten_id'))
                                                ->pluck('nama_kecamatan', 'id')
                                            : []
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('desa_id', null);
                                    })
                                    ->disabled(fn (Get $get): bool => !$get('kabupaten_id'))
                                    ->placeholder(fn (Get $get): string =>
                                        $get('kabupaten_id') ? 'Pilih Kecamatan' : 'Pilih Kabupaten dulu'
                                    )
                                    ->prefixIcon('heroicon-o-building-office')
                                    ->native(false),

                                Forms\Components\Select::make('desa_id')
                                    ->relationship('desa', 'nama_desa')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Desa/Kelurahan')
                                    ->options(fn (Get $get) =>
                                        $get('kecamatan_id')
                                            ? \App\Models\Desa::where('kecamatan_id', $get('kecamatan_id'))
                                                ->pluck('nama_desa', 'id')
                                            : []
                                    )
                                    ->disabled(fn (Get $get): bool => !$get('kecamatan_id'))
                                    ->placeholder(fn (Get $get): string =>
                                        $get('kecamatan_id') ? 'Pilih Desa/Kelurahan' : 'Pilih Kecamatan dulu'
                                    )
                                    ->prefixIcon('heroicon-o-home-modern')
                                    ->native(false)
                                    ->helperText('Lokasi desa/kelurahan kelompok tani'),
                            ]),
                    ]),

                Forms\Components\Section::make('Data Kelompok Tani')
                    ->description('Informasi utama kelompok tani')
                    ->icon('heroicon-o-user-group')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('nama_kelompok')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Kelompok Tani')
                            ->placeholder('Contoh: Tani Makmur Jaya')
                            ->helperText('Nama resmi kelompok tani yang terdaftar')
                            ->prefixIcon('heroicon-o-user-group')
                            ->autocomplete(false)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                // Auto-generate profil jika kosong
                                if ($state && !$get('profil')) {
                                    $jumlah = $get('jumlah_anggota') ?? 0;
                                    $set('profil', "Kelompok Tani {$state} adalah kelompok tani dengan {$jumlah} anggota yang aktif dalam kegiatan pertanian.");
                                }
                            })
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('jumlah_anggota')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(1)
                            ->maxValue(999)
                            ->label('Jumlah Anggota')
                            ->placeholder('0')
                            ->helperText('Total anggota aktif dalam kelompok')
                            ->prefixIcon('heroicon-o-users')
                            ->suffix(' Orang')
                            ->live()
                            ->columnSpan(1),

                        Forms\Components\Placeholder::make('kategori_kelompok')
                            ->label('Kategori Kelompok')
                            ->content(function (Get $get): string {
                                $jumlah = $get('jumlah_anggota') ?? 0;
                                return match(true) {
                                    $jumlah == 0 => '⚠️ Belum ada anggota',
                                    $jumlah <= 10 => '📊 Kelompok Kecil (1-10 anggota)',
                                    $jumlah <= 30 => '📈 Kelompok Sedang (11-30 anggota)',
                                    $jumlah > 30 => '🏆 Kelompok Besar (>30 anggota)',
                                };
                            })
                            ->columnSpan(1)
                            ->visible(fn (?KelompokTani $record) => $record === null),

                        Forms\Components\RichEditor::make('profil')
                            ->label('Profil Kelompok')
                            ->columnSpanFull()
                            ->placeholder('Deskripsikan profil kelompok: visi, misi, kegiatan, prestasi...')
                            ->helperText('Profil atau deskripsi singkat tentang kelompok tani')
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'italic',
                                'orderedList',
                                'redo',
                                'undo',
                            ])
                            ->maxLength(1000),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Lokasi Kelompok Tani')
                    ->description('Tandai lokasi kelompok tani di peta')
                    ->icon('heroicon-o-map-pin')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Map::make('location')
                            ->label('Pilih Lokasi di Peta')
                            ->columnSpanFull()
                            ->helperText('🎯 Klik pada peta atau gunakan tombol "My Location" untuk menentukan lokasi kelompok tani')
                            // Basic Configuration
                            ->defaultLocation(latitude: -4.760453342794058, longitude: 122.53024395961829)
                            ->draggable(true)
                            ->clickable(true)
                            ->zoom(12)
                            ->minZoom(5)
                            ->maxZoom(18)
                            ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png")
                            ->detectRetina(true)
                            // Marker Configuration
                            ->showMarker(true)
                            ->markerColor("#18872e")
                            ->markerIconSize([32, 32])
                            // Controls
                            ->showFullscreenControl(true)
                            ->showZoomControl(true)
                            // Location Features
                            ->showMyLocationButton(true)
                            ->liveLocation(true, true, 5000)
                            // State Management
                            ->afterStateUpdated(function (Set $set, ?array $state): void {
                                if ($state && isset($state['lat'], $state['lng'])) {
                                    $set('latitude', $state['lat']);
                                    $set('longitude', $state['lng']);
                                }
                            })
                            ->afterStateHydrated(function ($state, $record, Set $set): void {
                                if ($record && $record->latitude && $record->longitude) {
                                    $set('location', [
                                        'lat' => $record->latitude,
                                        'lng' => $record->longitude,
                                    ]);
                                }
                            })
                            ->extraControl([
                                'zoomDelta' => 1,
                                'zoomSnap' => 0.25,
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('latitude')
                                    ->label('Latitude (Lintang)')
                                    ->numeric()
                                    ->step('any')
                                    ->helperText('Otomatis terisi dari lokasi peta')
                                    ->placeholder('-4.760453')
                                    ->prefixIcon('heroicon-o-arrow-down-circle')
                                    ->disabled()
                                    ->dehydrated(true),

                                Forms\Components\TextInput::make('longitude')
                                    ->label('Longitude (Bujur)')
                                    ->numeric()
                                    ->step('any')
                                    ->helperText('Otomatis terisi dari lokasi peta')
                                    ->placeholder('122.530243')
                                    ->prefixIcon('heroicon-o-map-pin')
                                    ->disabled()
                                    ->dehydrated(true),
                            ]),

                        Forms\Components\Placeholder::make('map_info')
                            ->label('ℹ️ Tips Peta')
                            ->content('Anda dapat mengklik langsung di peta, drag marker, atau gunakan tombol "My Location" untuk menandai lokasi kelompok tani. Koordinat akan otomatis tersimpan.')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Informasi Tambahan')
                    ->description('Data statistik dan riwayat')
                    ->icon('heroicon-o-information-circle')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('wilayah_lengkap')
                            ->label('Wilayah Lengkap')
                            ->content(fn (?KelompokTani $record): string =>
                                $record
                                    ? "{$record->desa->nama_desa}, Kec. {$record->desa->kecamatan->nama_kecamatan}, Kab. {$record->desa->kecamatan->kabupaten->nama_kabupaten}"
                                    : 'Wilayah akan tampil setelah data disimpan'
                            ),

                        Forms\Components\Placeholder::make('total_anggota_detail')
                            ->label('Detail Anggota')
                            ->content(fn (?KelompokTani $record): string =>
                                $record
                                    ? number_format($record->kelompokTaniAnggotas()->count()) . ' anggota terdaftar dalam sistem'
                                    : 'Detail anggota akan tampil setelah data disimpan'
                            )
                            ->visible(fn (?KelompokTani $record) => $record !== null),

                        Forms\Components\Placeholder::make('bantuan_diterima')
                            ->label('Total Bantuan Diterima')
                            ->content(fn (?KelompokTani $record): string =>
                                $record
                                    ? number_format($record->sebaranBantuans()->count()) . ' bantuan'
                                    : '-'
                            )
                            ->visible(fn (?KelompokTani $record) => $record !== null),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Terdaftar Sejak')
                            ->content(fn (?KelompokTani $record): string =>
                                $record?->created_at?->format('d F Y') . ' (' . $record?->created_at?->diffForHumans() . ')' ?? '-'
                            )
                            ->visible(fn (?KelompokTani $record) => $record !== null),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->content(fn (?KelompokTani $record): string =>
                                $record?->updated_at?->diffForHumans() ?? '-'
                            )
                            ->visible(fn (?KelompokTani $record) => $record !== null),
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

                Tables\Columns\TextColumn::make('desa.kecamatan.kabupaten.nama_kabupaten')
                    ->label('Kabupaten')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-map-pin')
                    ->iconColor('primary')
                    ->toggleable()
                    ->tooltip('Kabupaten'),

                Tables\Columns\TextColumn::make('desa.kecamatan.nama_kecamatan')
                    ->searchable()
                    ->sortable()
                    ->label('Kecamatan')
                    ->icon('heroicon-o-building-office')
                    ->iconColor('success')
                    ->weight('medium')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('desa.nama_desa')
                    ->searchable()
                    ->sortable()
                    ->label('Desa/Kelurahan')
                    ->icon('heroicon-o-home-modern')
                    ->iconColor('warning')
                    ->description(fn (KelompokTani $record): string =>
                        $record->desa->tipe ?? ''
                    )
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('nama_kelompok')
                    ->searchable()
                    ->label('Nama Kelompok Tani')
                    ->icon('heroicon-o-user-group')
                    ->iconColor('success')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nama kelompok tersalin!')
                    ->description(fn (KelompokTani $record): string =>
                        Str::limit(strip_tags($record->profil), 50) ?: 'Belum ada profil'
                    )
                    ->wrap()
                    ->tooltip('Klik untuk menyalin'),

                Tables\Columns\TextColumn::make('jumlah_anggota')
                    ->numeric()
                    ->sortable()
                    ->label('Anggota')
                    ->badge()
                    ->color(fn ($state): string => match(true) {
                        $state == 0 => 'gray',
                        $state <= 10 => 'warning',
                        $state <= 30 => 'info',
                        $state > 30 => 'success',
                    })
                    ->icon('heroicon-o-users')
                    ->suffix(' Orang')
                    ->alignCenter()
                    ->tooltip(fn ($state): string => match(true) {
                        $state == 0 => 'Belum ada anggota',
                        $state <= 10 => 'Kelompok kecil',
                        $state <= 30 => 'Kelompok sedang',
                        $state > 30 => 'Kelompok besar',
                    }),

                Tables\Columns\TextColumn::make('anggota_count')
                    ->label('Anggota Detail')
                    ->counts('kelompokTaniAnggotas')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-identification')
                    ->suffix(' Detail')
                    ->alignCenter()
                    ->tooltip('Jumlah anggota dengan data lengkap')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('sebaranBantuans_count')
                    ->label('Bantuan')
                    ->counts('sebaranBantuans')
                    ->badge()
                    ->color(fn ($state): string => match(true) {
                        $state == 0 => 'gray',
                        $state <= 2 => 'warning',
                        $state <= 5 => 'info',
                        $state > 5 => 'success',
                    })
                    ->icon('heroicon-o-gift')
                    ->suffix(' Bantuan')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn ($state): string => $state . ' bantuan diterima'),

                Tables\Columns\IconColumn::make('has_location')
                    ->label('Lokasi')
                    ->boolean()
                    ->state(fn (KelompokTani $record): bool =>
                        $record->latitude && $record->longitude
                    )
                    ->trueIcon('heroicon-o-map-pin')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->alignCenter()
                    ->tooltip(fn (KelompokTani $record): string =>
                        $record->latitude && $record->longitude
                            ? "Lokasi: {$record->latitude}, {$record->longitude}"
                            : 'Lokasi belum ditandai'
                    ),

                Tables\Columns\TextColumn::make('latitude')
                    ->numeric(decimalPlaces: 6)
                    ->sortable()
                    ->label('Lintang')
                    ->default('-')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),

                Tables\Columns\TextColumn::make('longitude')
                    ->numeric(decimalPlaces: 6)
                    ->sortable()
                    ->label('Bujur')
                    ->default('-')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->label('Terdaftar')
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
                    ->since(),
            ])
            ->defaultSort('nama_kelompok', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('kabupaten')
                    ->label('Kabupaten')
                    ->relationship('desa.kecamatan.kabupaten', 'nama_kabupaten')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('Semua Kabupaten')
                    ->native(false)
                    ->indicator('Kabupaten'),

                Tables\Filters\SelectFilter::make('kecamatan')
                    ->label('Kecamatan')
                    ->relationship('desa.kecamatan', 'nama_kecamatan')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('Semua Kecamatan')
                    ->native(false)
                    ->indicator('Kecamatan'),

                Tables\Filters\SelectFilter::make('desa_id')
                    ->label('Desa/Kelurahan')
                    ->relationship('desa', 'nama_desa')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('Semua Desa')
                    ->native(false)
                    ->indicator('Desa'),

                Tables\Filters\Filter::make('jumlah_anggota')
                    ->form([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('anggota_min')
                                ->label('Minimal Anggota')
                                ->numeric()
                                ->placeholder('0')
                                ->prefixIcon('heroicon-o-chevron-double-up'),
                            Forms\Components\TextInput::make('anggota_max')
                                ->label('Maksimal Anggota')
                                ->numeric()
                                ->placeholder('100')
                                ->prefixIcon('heroicon-o-chevron-double-down'),
                        ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['anggota_min'],
                                fn (Builder $query, $value) => $query->where('jumlah_anggota', '>=', $value)
                            )
                            ->when(
                                $data['anggota_max'],
                                fn (Builder $query, $value) => $query->where('jumlah_anggota', '<=', $value)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['anggota_min'] ?? null) {
                            $indicators[] = 'Min: ' . $data['anggota_min'] . ' anggota';
                        }
                        if ($data['anggota_max'] ?? null) {
                            $indicators[] = 'Max: ' . $data['anggota_max'] . ' anggota';
                        }
                        return $indicators;
                    }),

                Tables\Filters\TernaryFilter::make('has_location')
                    ->label('Lokasi Ditandai')
                    ->placeholder('Semua')
                    ->trueLabel('Dengan Lokasi')
                    ->falseLabel('Tanpa Lokasi')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('latitude')->whereNotNull('longitude'),
                        false: fn (Builder $query) => $query->whereNull('latitude')->orWhereNull('longitude'),
                    )
                    ->native(false),

                Tables\Filters\TernaryFilter::make('has_bantuan')
                    ->label('Penerima Bantuan')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah Menerima')
                    ->falseLabel('Belum Menerima')
                    ->queries(
                        true: fn (Builder $query) => $query->has('sebaranBantuans'),
                        false: fn (Builder $query) => $query->doesntHave('sebaranBantuans'),
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
                    Tables\Actions\Action::make('viewMap')
                        ->label('Lihat di Peta')
                        ->icon('heroicon-o-map')
                        ->color('success')
                        ->url(fn (KelompokTani $record): string =>
                            $record->latitude && $record->longitude
                                ? "https://www.google.com/maps?q={$record->latitude},{$record->longitude}"
                                : '#'
                        )
                        ->openUrlInNewTab()
                        ->visible(fn (KelompokTani $record): bool =>
                            $record->latitude && $record->longitude
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Kelompok Tani')
                        ->modalDescription('Apakah Anda yakin ingin menghapus kelompok tani ini? Data anggota dan bantuan yang diterima akan terpengaruh.')
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
                        ->modalHeading('Hapus Kelompok Tani Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua kelompok tani yang dipilih? Data terkait akan terpengaruh.')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Kelompok Tani')
            ->emptyStateDescription('Mulai dengan menambahkan kelompok tani pertama Anda.')
            ->emptyStateIcon('heroicon-o-user-group')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Kelompok Tani')
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
                Infolists\Components\Section::make('Informasi Kelompok Tani')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('nama_kelompok')
                                    ->label('Nama Kelompok Tani')
                                    ->icon('heroicon-o-user-group')
                                    ->iconColor('success')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->copyable()
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('jumlah_anggota')
                                    ->label('Jumlah Anggota')
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-o-users')
                                    ->suffix(' Orang')
                                    ->size('lg'),
                            ]),

                        Infolists\Components\TextEntry::make('wilayah_lengkap')
                            ->label('Wilayah')
                            ->state(fn (KelompokTani $record): string =>
                                "{$record->desa->nama_desa} ({$record->desa->tipe}), Kec. {$record->desa->kecamatan->nama_kecamatan}, Kab. {$record->desa->kecamatan->kabupaten->nama_kabupaten}"
                            )
                            ->icon('heroicon-o-map')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('profil')
                            ->label('Profil Kelompok')
                            ->html()
                            ->default('Belum ada profil')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Lokasi Geografis')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('latitude')
                                    ->label('Latitude (Lintang)')
                                    ->numeric(decimalPlaces: 6)
                                    ->icon('heroicon-o-arrow-down-circle')
                                    ->copyable()
                                    ->default('-'),

                                Infolists\Components\TextEntry::make('longitude')
                                    ->label('Longitude (Bujur)')
                                    ->numeric(decimalPlaces: 6)
                                    ->icon('heroicon-o-map-pin')
                                    ->copyable()
                                    ->default('-'),

                                Infolists\Components\TextEntry::make('maps_link')
                                    ->label('Link Google Maps')
                                    ->state(fn (KelompokTani $record): string =>
                                        $record->latitude && $record->longitude
                                            ? "Lihat di Maps"
                                            : '-'
                                    )
                                    ->url(fn (KelompokTani $record): ?string =>
                                        $record->latitude && $record->longitude
                                            ? "https://www.google.com/maps?q={$record->latitude},{$record->longitude}"
                                            : null
                                    )
                                    ->openUrlInNewTab()
                                    ->icon('heroicon-o-map')
                                    ->color('success'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Statistik Kelompok')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('anggota_detail_count')
                                    ->label('Anggota Terdaftar')
                                    ->state(fn (KelompokTani $record): int => $record->kelompokTaniAnggotas()->count())
                                    ->badge()
                                    ->icon('heroicon-o-identification')
                                    ->color('info')
                                    ->suffix(' Anggota')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('total_lahan')
                                    ->label('Total Luas Lahan')
                                    ->state(fn (KelompokTani $record): float => $record->kelompokTaniAnggotas()->sum('luas_lahan'))
                                    ->numeric(decimalPlaces: 2)
                                    ->badge()
                                    ->icon('heroicon-o-map')
                                    ->color('warning')
                                    ->suffix(' Hektar')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('bantuan_count')
                                    ->label('Bantuan Diterima')
                                    ->state(fn (KelompokTani $record): int => $record->sebaranBantuans()->count())
                                    ->badge()
                                    ->icon('heroicon-o-gift')
                                    ->color('success')
                                    ->suffix(' Bantuan')
                                    ->size('lg'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Riwayat Data')
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Terdaftar pada')
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
            RelationManagers\KelompokTaniAnggotaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKelompokTanis::route('/'),
            'create' => Pages\CreateKelompokTani::route('/create'),
            'view' => Pages\ViewKelompokTani::route('/{record}'),
            'edit' => Pages\EditKelompokTani::route('/{record}/edit'),
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
            'Desa' => $record->desa->nama_desa,
            'Kecamatan' => $record->desa->kecamatan->nama_kecamatan,
            'Anggota' => $record->jumlah_anggota . ' orang',
        ];
    }
}

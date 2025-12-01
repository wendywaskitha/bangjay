<?php

namespace App\Filament\Resources\DesaResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\KelompokTani;
use Dotswan\MapPicker\Fields\Map;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\RelationManagers\RelationManager;

class KelompokTaniRelationManager extends RelationManager
{
    protected static string $relationship = 'kelompokTanis';

    protected static ?string $title = 'Daftar Kelompok Tani';

    protected static ?string $icon = 'heroicon-o-user-group';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kelompok Tani')
                    ->description('Data utama kelompok tani')
                    ->icon('heroicon-o-user-group')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('nama_kelompok')
                            ->label('Nama Kelompok Tani')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Tani Makmur Jaya')
                            ->helperText('Nama resmi kelompok tani yang terdaftar')
                            ->prefixIcon('heroicon-o-user-group')
                            ->autocomplete(false)
                            ->live(onBlur: true)
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('jumlah_anggota')
                                    ->label('Jumlah Anggota')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(999)
                                    ->default(0)
                                    ->helperText('Total anggota aktif dalam kelompok')
                                    ->prefixIcon('heroicon-o-users')
                                    ->suffix(' Orang')
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        // Auto-update profil jika kosong
                                        if ($state && $state > 0) {
                                            $set('profil', $set('profil') ?? "Kelompok tani dengan {$state} anggota aktif.");
                                        }
                                    }),

                                Forms\Components\Placeholder::make('info_anggota')
                                    ->label('Kategori Kelompok')
                                    ->content(function (Forms\Get $get): string {
                                        $jumlah = $get('jumlah_anggota') ?? 0;
                                        return match(true) {
                                            $jumlah == 0 => '⚠️ Belum ada anggota',
                                            $jumlah <= 10 => '📊 Kelompok Kecil',
                                            $jumlah <= 30 => '📈 Kelompok Sedang',
                                            $jumlah > 30 => '🏆 Kelompok Besar',
                                        };
                                    })
                                    ->visible(fn (?KelompokTani $record) => $record === null),
                            ]),

                        Forms\Components\RichEditor::make('profil')
                            ->label('Profil Kelompok')
                            ->columnSpanFull()
                            ->placeholder('Deskripsikan profil singkat kelompok tani: visi, misi, kegiatan utama, prestasi...')
                            ->helperText('Profil atau deskripsi singkat tentang kelompok tani ini')
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'italic',
                                'orderedList',
                                'redo',
                                'undo',
                            ])
                            ->maxLength(1000),
                    ]),

                Forms\Components\Section::make('Lokasi Kelompok Tani')
                    ->description('Tandai lokasi kelompok tani di peta atau masukkan koordinat manual')
                    ->icon('heroicon-o-map-pin')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Map::make('location')
                            ->label('Pilih Lokasi di Peta')
                            ->columnSpanFull()
                            ->defaultLocation(
                                latitude: -4.0667,
                                longitude: 122.1333
                            )
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('latitude', $state['lat']);
                                $set('longitude', $state['lng']);
                            })
                            ->afterStateHydrated(function ($state, $record, callable $set) {
                                if ($record && $record->latitude && $record->longitude) {
                                    $set('location', [
                                        'lat' => $record->latitude,
                                        'lng' => $record->longitude,
                                    ]);
                                }
                            })
                            ->liveLocation(true, true, 5000)
                            ->showMarker()
                            ->markerColor("#22c55eff")
                            ->showFullscreenControl()
                            ->showZoomControl()
                            ->draggable()
                            ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png")
                            ->zoom(15)
                            ->detectRetina()
                            ->showMyLocationButton()
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
                                    ->helperText('Koordinat lintang lokasi')
                                    ->placeholder('-4.0667')
                                    ->prefixIcon('heroicon-o-arrow-down-circle')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set, Forms\Get $get) {
                                        $lng = $get('longitude');
                                        if ($state && $lng) {
                                            $set('location', ['lat' => floatval($state), 'lng' => floatval($lng)]);
                                        }
                                    }),

                                Forms\Components\TextInput::make('longitude')
                                    ->label('Longitude (Bujur)')
                                    ->numeric()
                                    ->step('any')
                                    ->helperText('Koordinat bujur lokasi')
                                    ->placeholder('122.1333')
                                    ->prefixIcon('heroicon-o-map-pin')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set, Forms\Get $get) {
                                        $lat = $get('latitude');
                                        if ($lat && $state) {
                                            $set('location', ['lat' => floatval($lat), 'lng' => floatval($state)]);
                                        }
                                    }),
                            ]),

                        Forms\Components\Placeholder::make('koordinat_info')
                            ->label('ℹ️ Tips Lokasi')
                            ->content('Anda dapat mengklik langsung di peta untuk menandai lokasi, atau isi koordinat secara manual. Gunakan tombol "My Location" untuk lokasi Anda saat ini.')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Informasi Tambahan')
                    ->description('Data statistik dan riwayat')
                    ->icon('heroicon-o-information-circle')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('wilayah_lengkap')
                            ->label('Wilayah')
                            ->content(fn (?KelompokTani $record): string =>
                                $record
                                    ? "{$record->desa->nama_desa}, Kec. {$record->desa->kecamatan->nama_kecamatan}, Kab. {$record->desa->kecamatan->kabupaten->nama_kabupaten}"
                                    : 'Wilayah otomatis terisi dari desa terpilih'
                            ),

                        Forms\Components\Placeholder::make('bantuan_diterima')
                            ->label('Total Bantuan Diterima')
                            ->content(fn (?KelompokTani $record): string =>
                                $record
                                    ? number_format($record->sebaranBantuans()->count()) . ' Bantuan'
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
                    ->columns(2)
                    ->visible(fn ($operation) => $operation === 'edit' || $operation === 'view'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_kelompok')
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->rowIndex()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('nama_kelompok')
                    ->label('Nama Kelompok Tani')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user-group')
                    ->iconColor('success')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nama kelompok tani berhasil disalin!')
                    ->copyMessageDuration(1500)
                    ->description(fn (KelompokTani $record): string =>
                        Str::limit(strip_tags($record->profil), 50) ?: 'Belum ada profil'
                    )
                    ->tooltip('Klik untuk menyalin'),

                Tables\Columns\TextColumn::make('jumlah_anggota')
                    ->label('Anggota')
                    ->numeric()
                    ->sortable()
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
                    ->tooltip(fn ($state): string => $state . ' bantuan telah diterima'),

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
                            ? 'Lokasi sudah ditandai'
                            : 'Lokasi belum ditandai'
                    ),

                Tables\Columns\TextColumn::make('latitude')
                    ->label('Lintang')
                    ->numeric(decimalPlaces: 6)
                    ->sortable()
                    ->default('-')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),

                Tables\Columns\TextColumn::make('longitude')
                    ->label('Bujur')
                    ->numeric(decimalPlaces: 6)
                    ->sortable()
                    ->default('-')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-calendar')
                    ->tooltip(fn ($state): string => $state->diffForHumans()),
            ])
            ->filters([
                Tables\Filters\Filter::make('jumlah_anggota')
                    ->form([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('jumlah_anggota_min')
                                ->label('Minimal Anggota')
                                ->numeric()
                                ->minValue(1)
                                ->placeholder('0')
                                ->prefixIcon('heroicon-o-chevron-double-up'),
                            Forms\Components\TextInput::make('jumlah_anggota_max')
                                ->label('Maksimal Anggota')
                                ->numeric()
                                ->minValue(1)
                                ->placeholder('100')
                                ->prefixIcon('heroicon-o-chevron-double-down'),
                        ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['jumlah_anggota_min'],
                                fn (Builder $query, $value) => $query->where('jumlah_anggota', '>=', $value)
                            )
                            ->when(
                                $data['jumlah_anggota_max'],
                                fn (Builder $query, $value) => $query->where('jumlah_anggota', '<=', $value)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['jumlah_anggota_min'] ?? null) {
                            $indicators[] = 'Anggota min: ' . $data['jumlah_anggota_min'];
                        }
                        if ($data['jumlah_anggota_max'] ?? null) {
                            $indicators[] = 'Anggota max: ' . $data['jumlah_anggota_max'];
                        }
                        return $indicators;
                    }),

                Tables\Filters\TernaryFilter::make('has_location')
                    ->label('Lokasi Ditandai')
                    ->placeholder('Semua Kelompok')
                    ->trueLabel('Dengan Lokasi')
                    ->falseLabel('Tanpa Lokasi')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('latitude')->whereNotNull('longitude'),
                        false: fn (Builder $query) => $query->whereNull('latitude')->orWhereNull('longitude'),
                    )
                    ->native(false),

                Tables\Filters\TernaryFilter::make('has_bantuan')
                    ->label('Penerima Bantuan')
                    ->placeholder('Semua Kelompok')
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Kelompok Tani')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Tambah Kelompok Tani Baru')
                    ->modalWidth('4xl')
                    ->successNotificationTitle('Kelompok tani berhasil ditambahkan')
                    ->color('primary')
                    ->slideOver(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info')
                        ->icon('heroicon-o-eye')
                        ->modalWidth('4xl')
                        ->slideOver(),
                    Tables\Actions\EditAction::make()
                        ->color('warning')
                        ->icon('heroicon-o-pencil')
                        ->modalHeading('Edit Kelompok Tani')
                        ->modalWidth('4xl')
                        ->slideOver(),
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
                        ->modalDescription('Apakah Anda yakin ingin menghapus data kelompok tani ini? Data terkait seperti anggota dan bantuan yang diterima akan terpengaruh.')
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
                        ->modalHeading('Hapus Kelompok Tani Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua kelompok tani yang dipilih? Data terkait akan terpengaruh.')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Kelompok Tani')
            ->emptyStateDescription('Tambahkan kelompok tani untuk desa/kelurahan ini.')
            ->emptyStateIcon('heroicon-o-user-group')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Kelompok Tani')
                    ->icon('heroicon-o-plus')
                    ->modalWidth('4xl')
                    ->slideOver(),
            ])
            ->defaultSort('nama_kelompok', 'asc')
            ->striped()
            ->paginated([10, 25, 50]);
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SebaranBantuanResource\Pages;
use App\Filament\Resources\SebaranBantuanResource\RelationManagers;
use App\Models\SebaranBantuan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Str;

class SebaranBantuanResource extends Resource
{
    protected static ?string $model = SebaranBantuan::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationLabel = 'Sebaran Bantuan';

    protected static ?string $modelLabel = 'Sebaran Bantuan';

    protected static ?string $pluralModelLabel = 'Data Sebaran Bantuan';

    protected static ?string $navigationGroup = 'Data Utama';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'kelompokTani.nama_kelompok';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Penerima Bantuan')
                    ->description('Pilih kelompok tani penerima bantuan')
                    ->icon('heroicon-o-user-group')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\Select::make('kabupaten_id')
                                    ->label('Kabupaten')
                                    ->options(\App\Models\Kabupaten::pluck('nama_kabupaten', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set) {
                                        $set('kecamatan_id', null);
                                        $set('desa_id', null);
                                        $set('kelompok_tani_id', null);
                                    })
                                    ->placeholder('Filter Kabupaten')
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
                                    ->afterStateUpdated(function (Forms\Set $set) {
                                        $set('desa_id', null);
                                        $set('kelompok_tani_id', null);
                                    })
                                    ->disabled(fn (Get $get): bool => !$get('kabupaten_id'))
                                    ->placeholder('Filter Kecamatan')
                                    ->prefixIcon('heroicon-o-building-office')
                                    ->native(false),

                                Forms\Components\Select::make('desa_id')
                                    ->label('Desa/Kelurahan')
                                    ->options(fn (Get $get) =>
                                        $get('kecamatan_id')
                                            ? \App\Models\Desa::where('kecamatan_id', $get('kecamatan_id'))
                                                ->pluck('nama_desa', 'id')
                                            : []
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set) {
                                        $set('kelompok_tani_id', null);
                                    })
                                    ->disabled(fn (Get $get): bool => !$get('kecamatan_id'))
                                    ->placeholder('Filter Desa')
                                    ->prefixIcon('heroicon-o-home-modern')
                                    ->native(false),

                                Forms\Components\Select::make('kelompok_tani_id')
                                    ->relationship('kelompokTani', 'nama_kelompok')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Kelompok Tani')
                                    ->placeholder('Pilih Kelompok Tani')
                                    ->helperText('Kelompok tani yang akan menerima bantuan')
                                    ->prefixIcon('heroicon-o-user-group')
                                    ->native(false)
                                    ->options(function (Get $get) {
                                        $query = \App\Models\KelompokTani::query();

                                        if ($desaId = $get('desa_id')) {
                                            $query->where('desa_id', $desaId);
                                        } elseif ($kecamatanId = $get('kecamatan_id')) {
                                            $query->whereHas('desa', function($q) use ($kecamatanId) {
                                                $q->where('kecamatan_id', $kecamatanId);
                                            });
                                        } elseif ($kabupatenId = $get('kabupaten_id')) {
                                            $query->whereHas('desa.kecamatan', function($q) use ($kabupatenId) {
                                                $q->where('kabupaten_id', $kabupatenId);
                                            });
                                        }

                                        return $query->pluck('nama_kelompok', 'id');
                                    })
                                    ->getOptionLabelFromRecordUsing(fn ($record) =>
                                        $record->nama_kelompok . ' - ' . $record->desa->nama_desa
                                    )
                                    ->createOptionForm([
                                        Forms\Components\Select::make('desa_id')
                                            ->relationship('desa', 'nama_desa')
                                            ->required()
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\TextInput::make('nama_kelompok')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('jumlah_anggota')
                                            ->numeric()
                                            ->default(0),
                                    ])
                                    ->createOptionModalHeading('Tambah Kelompok Tani Baru'),
                            ]),

                        Forms\Components\Placeholder::make('info_kelompok')
                            ->label('Info Kelompok Terpilih')
                            ->content(function (Get $get): string {
                                $kelompokId = $get('kelompok_tani_id');
                                if (!$kelompokId) {
                                    return 'Pilih kelompok tani untuk melihat informasi';
                                }

                                $kelompok = \App\Models\KelompokTani::find($kelompokId);
                                if (!$kelompok) return '-';

                                return "📍 {$kelompok->desa->nama_desa}, Kec. {$kelompok->desa->kecamatan->nama_kecamatan}\n" .
                                       "👥 {$kelompok->jumlah_anggota} Anggota\n" .
                                       "🎁 " . $kelompok->sebaranBantuans()->count() . " Bantuan Sebelumnya";
                            })
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Detail Penetapan Bantuan')
                    ->description('Informasi penetapan dan catatan bantuan')
                    ->icon('heroicon-o-calendar')
                    ->collapsible()
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_penetapan')
                            ->label('Tanggal Penetapan')
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->helperText('Tanggal penetapan sebaran bantuan')
                            ->prefixIcon('heroicon-o-calendar-days')
                            ->maxDate(now())
                            ->closeOnDateSelection()
                            ->columnSpan(1),

                        Forms\Components\Placeholder::make('info_tanggal')
                            ->label('Periode')
                            ->content(function (Get $get): string {
                                $tanggal = $get('tanggal_penetapan');
                                if (!$tanggal) return '-';

                                $date = \Carbon\Carbon::parse($tanggal);
                                return "📅 " . $date->translatedFormat('l, d F Y') . "\n" .
                                       "⏰ " . $date->diffForHumans();
                            })
                            ->columnSpan(1)
                            ->visible(fn (?SebaranBantuan $record) => $record === null),

                        Forms\Components\RichEditor::make('catatan')
                            ->label('Catatan')
                            ->columnSpanFull()
                            ->placeholder('Tambahkan catatan: sumber anggaran, keterangan khusus, kondisi bantuan...')
                            ->helperText('Catatan atau keterangan tambahan mengenai bantuan ini')
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
                    ->columns(2),

                Forms\Components\Section::make('Informasi Tambahan')
                    ->description('Data statistik dan riwayat')
                    ->icon('heroicon-o-information-circle')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('wilayah_lengkap')
                            ->label('Wilayah Lengkap')
                            ->content(fn (?SebaranBantuan $record): string =>
                                $record
                                    ? "{$record->kelompokTani->desa->nama_desa}, Kec. {$record->kelompokTani->desa->kecamatan->nama_kecamatan}, Kab. {$record->kelompokTani->desa->kecamatan->kabupaten->nama_kabupaten}"
                                    : 'Wilayah akan tampil setelah data disimpan'
                            ),

                        Forms\Components\Placeholder::make('jumlah_jenis_bantuan')
                            ->label('Jumlah Jenis Bantuan')
                            ->content(fn (?SebaranBantuan $record): string =>
                                $record
                                    ? number_format($record->jenisBantuans()->count()) . ' jenis bantuan'
                                    : 'Tambahkan jenis bantuan setelah data disimpan'
                            )
                            ->visible(fn (?SebaranBantuan $record) => $record !== null),

                        Forms\Components\Placeholder::make('total_anggota_penerima')
                            ->label('Total Anggota Penerima')
                            ->content(fn (?SebaranBantuan $record): string =>
                                $record
                                    ? number_format($record->kelompokTani->jumlah_anggota) . ' anggota'
                                    : '-'
                            )
                            ->visible(fn (?SebaranBantuan $record) => $record !== null),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Dicatat pada')
                            ->content(fn (?SebaranBantuan $record): string =>
                                $record?->created_at?->format('d F Y, H:i') . ' (' . $record?->created_at?->diffForHumans() . ')' ?? '-'
                            )
                            ->visible(fn (?SebaranBantuan $record) => $record !== null),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->content(fn (?SebaranBantuan $record): string =>
                                $record?->updated_at?->diffForHumans() ?? '-'
                            )
                            ->visible(fn (?SebaranBantuan $record) => $record !== null),
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

                Tables\Columns\TextColumn::make('kelompokTani.desa.kecamatan.kabupaten.nama_kabupaten')
                    ->label('Kabupaten')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-map-pin')
                    ->iconColor('primary')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('kelompokTani.desa.kecamatan.nama_kecamatan')
                    ->searchable()
                    ->sortable()
                    ->label('Kecamatan')
                    ->icon('heroicon-o-building-office')
                    ->iconColor('success')
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('kelompokTani.desa.nama_desa')
                    ->searchable()
                    ->sortable()
                    ->label('Desa/Kelurahan')
                    ->icon('heroicon-o-home-modern')
                    ->iconColor('warning')
                    ->description(fn (SebaranBantuan $record): string =>
                        $record->kelompokTani->desa->tipe ?? ''
                    ),

                Tables\Columns\TextColumn::make('kelompokTani.nama_kelompok')
                    ->searchable()
                    ->sortable()
                    ->label('Kelompok Tani')
                    ->icon('heroicon-o-user-group')
                    ->iconColor('success')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nama kelompok tersalin!')
                    ->description(fn (SebaranBantuan $record): string =>
                        $record->kelompokTani->jumlah_anggota . ' anggota'
                    )
                    ->tooltip('Klik untuk menyalin'),

                Tables\Columns\TextColumn::make('jenis_bantuans_count')
                    ->label('Jenis Bantuan')
                    ->counts('jenisBantuans')
                    ->badge()
                    ->color(fn ($state): string => match(true) {
                        $state == 0 => 'gray',
                        $state == 1 => 'warning',
                        $state <= 3 => 'info',
                        $state > 3 => 'success',
                    })
                    ->icon('heroicon-o-gift')
                    ->suffix(' Jenis')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn ($state): string => $state . ' jenis bantuan diterima'),

                Tables\Columns\TextColumn::make('bantuan_detail')
                    ->label('Detail Bantuan')
                    ->state(function (SebaranBantuan $record): string {
                        $bantuans = $record->jenisBantuans()
                            ->with('kategoriBantuan')
                            ->get()
                            ->map(fn($b) => $b->kategoriBantuan->nama_kategori ?? 'Umum')
                            ->unique()
                            ->take(3);

                        return $bantuans->isEmpty()
                            ? '-'
                            : $bantuans->join(', ');
                    })
                    ->badge()
                    ->separator(',')
                    ->color('info')
                    ->icon('heroicon-o-tag')
                    ->tooltip('Kategori bantuan yang diterima')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tanggal_penetapan')
                    ->date('d M Y')
                    ->sortable()
                    ->label('Tanggal Penetapan')
                    ->icon('heroicon-o-calendar-days')
                    ->iconColor('primary')
                    ->tooltip(fn ($state): string =>
                        $state ? \Carbon\Carbon::parse($state)->translatedFormat('l, d F Y') : '-'
                    )
                    ->default('-'),

                Tables\Columns\TextColumn::make('tanggal_penetapan')
                    ->label('Status Waktu')
                    ->badge()
                    ->color(function ($state): string {
                        if (!$state) return 'gray';
                        $diff = now()->diffInDays(\Carbon\Carbon::parse($state));
                        return match(true) {
                            $diff <= 30 => 'success',
                            $diff <= 180 => 'info',
                            $diff <= 365 => 'warning',
                            default => 'gray',
                        };
                    })
                    ->formatStateUsing(function ($state): string {
                        if (!$state) return 'Belum Ditetapkan';
                        return \Carbon\Carbon::parse($state)->diffForHumans();
                    })
                    ->icon('heroicon-o-clock')
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->label('Dicatat')
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
            ->defaultSort('tanggal_penetapan', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('kabupaten')
                    ->label('Kabupaten')
                    ->relationship('kelompokTani.desa.kecamatan.kabupaten', 'nama_kabupaten')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('Semua Kabupaten')
                    ->native(false)
                    ->indicator('Kabupaten'),

                Tables\Filters\SelectFilter::make('kecamatan')
                    ->label('Kecamatan')
                    ->relationship('kelompokTani.desa.kecamatan', 'nama_kecamatan')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('Semua Kecamatan')
                    ->native(false)
                    ->indicator('Kecamatan'),

                Tables\Filters\SelectFilter::make('desa')
                    ->label('Desa/Kelurahan')
                    ->relationship('kelompokTani.desa', 'nama_desa')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('Semua Desa')
                    ->native(false)
                    ->indicator('Desa'),

                Tables\Filters\Filter::make('tanggal_penetapan')
                    ->form([
                        Forms\Components\DatePicker::make('penetapan_dari')
                            ->label('Dari Tanggal')
                            ->native(false)
                            ->placeholder('Pilih tanggal mulai'),
                        Forms\Components\DatePicker::make('penetapan_sampai')
                            ->label('Sampai Tanggal')
                            ->native(false)
                            ->placeholder('Pilih tanggal akhir'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['penetapan_dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_penetapan', '>=', $date),
                            )
                            ->when(
                                $data['penetapan_sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_penetapan', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['penetapan_dari'] ?? null) {
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['penetapan_dari'])->format('d M Y');
                        }
                        if ($data['penetapan_sampai'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['penetapan_sampai'])->format('d M Y');
                        }
                        return $indicators;
                    }),

                Tables\Filters\Filter::make('tahun_ini')
                    ->label('Tahun Ini')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereYear('tanggal_penetapan', now()->year)
                    )
                    ->toggle(),

                Tables\Filters\TernaryFilter::make('has_jenis_bantuan')
                    ->label('Jenis Bantuan')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah Ada Jenis')
                    ->falseLabel('Belum Ada Jenis')
                    ->queries(
                        true: fn (Builder $query) => $query->has('jenisBantuans'),
                        false: fn (Builder $query) => $query->doesntHave('jenisBantuans'),
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
                    Tables\Actions\Action::make('viewLocation')
                        ->label('Lihat Lokasi')
                        ->icon('heroicon-o-map')
                        ->color('success')
                        ->url(fn (SebaranBantuan $record): string =>
                            $record->kelompokTani->latitude && $record->kelompokTani->longitude
                                ? "https://www.google.com/maps?q={$record->kelompokTani->latitude},{$record->kelompokTani->longitude}"
                                : '#'
                        )
                        ->openUrlInNewTab()
                        ->visible(fn (SebaranBantuan $record): bool =>
                            $record->kelompokTani->latitude && $record->kelompokTani->longitude
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Sebaran Bantuan')
                        ->modalDescription('Apakah Anda yakin ingin menghapus data sebaran bantuan ini? Data jenis bantuan terkait akan terpengaruh.')
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
                        ->modalHeading('Hapus Sebaran Bantuan Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua sebaran bantuan yang dipilih? Data terkait akan terpengaruh.')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Sebaran Bantuan')
            ->emptyStateDescription('Mulai dengan mencatat sebaran bantuan kepada kelompok tani.')
            ->emptyStateIcon('heroicon-o-document-check')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Sebaran Bantuan')
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
                Infolists\Components\Section::make('Informasi Penerima Bantuan')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Infolists\Components\TextEntry::make('kelompokTani.nama_kelompok')
                            ->label('Nama Kelompok Tani')
                            ->icon('heroicon-o-user-group')
                            ->iconColor('success')
                            ->size('lg')
                            ->weight('bold')
                            ->copyable()
                            ->color('success'),

                        Infolists\Components\TextEntry::make('wilayah')
                            ->label('Wilayah')
                            ->state(fn (SebaranBantuan $record): string =>
                                "{$record->kelompokTani->desa->nama_desa} ({$record->kelompokTani->desa->tipe}), " .
                                "Kec. {$record->kelompokTani->desa->kecamatan->nama_kecamatan}, " .
                                "Kab. {$record->kelompokTani->desa->kecamatan->kabupaten->nama_kabupaten}"
                            )
                            ->icon('heroicon-o-map')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('kelompokTani.jumlah_anggota')
                            ->label('Jumlah Anggota')
                            ->badge()
                            ->color('primary')
                            ->icon('heroicon-o-users')
                            ->suffix(' Anggota'),

                        Infolists\Components\TextEntry::make('tanggal_penetapan')
                            ->label('Tanggal Penetapan')
                            ->date('d F Y')
                            ->icon('heroicon-o-calendar-days')
                            ->badge()
                            ->color('info'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Detail Bantuan')
                    ->icon('heroicon-o-gift')
                    ->schema([
                        Infolists\Components\TextEntry::make('jenis_bantuans_count')
                            ->label('Jumlah Jenis Bantuan')
                            ->state(fn (SebaranBantuan $record): int => $record->jenisBantuans()->count())
                            ->badge()
                            ->icon('heroicon-o-gift')
                            ->color('success')
                            ->suffix(' Jenis Bantuan')
                            ->size('lg'),

                        Infolists\Components\TextEntry::make('catatan')
                            ->label('Catatan')
                            ->html()
                            ->default('Tidak ada catatan')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Lokasi Kelompok Tani')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('kelompokTani.latitude')
                                    ->label('Latitude')
                                    ->numeric(decimalPlaces: 6)
                                    ->icon('heroicon-o-map-pin')
                                    ->copyable()
                                    ->default('-'),

                                Infolists\Components\TextEntry::make('kelompokTani.longitude')
                                    ->label('Longitude')
                                    ->numeric(decimalPlaces: 6)
                                    ->icon('heroicon-o-map-pin')
                                    ->copyable()
                                    ->default('-'),

                                Infolists\Components\TextEntry::make('maps_link')
                                    ->label('Link Google Maps')
                                    ->state(fn (SebaranBantuan $record): string =>
                                        $record->kelompokTani->latitude && $record->kelompokTani->longitude
                                            ? "Lihat di Maps"
                                            : '-'
                                    )
                                    ->url(fn (SebaranBantuan $record): ?string =>
                                        $record->kelompokTani->latitude && $record->kelompokTani->longitude
                                            ? "https://www.google.com/maps?q={$record->kelompokTani->latitude},{$record->kelompokTani->longitude}"
                                            : null
                                    )
                                    ->openUrlInNewTab()
                                    ->icon('heroicon-o-map')
                                    ->color('success'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Riwayat Data')
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Dicatat pada')
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
            RelationManagers\JenisBantuanRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSebaranBantuans::route('/'),
            'create' => Pages\CreateSebaranBantuan::route('/create'),
            'view' => Pages\ViewSebaranBantuan::route('/{record}'),
            'edit' => Pages\EditSebaranBantuan::route('/{record}/edit'),
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
            $count < 50 => 'warning',
            default => 'success',
        };
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Kelompok' => $record->kelompokTani->nama_kelompok,
            'Desa' => $record->kelompokTani->desa->nama_desa,
            'Jenis Bantuan' => $record->jenisBantuans()->count() . ' jenis',
        ];
    }
}

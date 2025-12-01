<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\JenisBantuan;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\JenisBantuanResource\Pages;
use App\Filament\Resources\JenisBantuanResource\RelationManagers;

class JenisBantuanResource extends Resource
{
    protected static ?string $model = JenisBantuan::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = 'Jenis Bantuan';

    protected static ?string $modelLabel = 'Jenis Bantuan';

    protected static ?string $pluralModelLabel = 'Data Jenis Bantuan';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'nama_bantuan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Bantuan')
                    ->description('Data utama jenis bantuan pertanian')
                    ->icon('heroicon-o-gift')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Select::make('kategori_bantuan_id')
                            ->relationship('kategoriBantuan', 'nama_kategori')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Kategori Bantuan')
                            ->placeholder('Pilih Kategori Bantuan')
                            ->helperText('Pilih kategori: Alsintan, Bibit, dll')
                            ->prefixIcon('heroicon-o-folder')
                            ->native(false)
                            ->live()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nama_kategori')
                                    ->label('Nama Kategori')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique()
                                    ->placeholder('Contoh: Alsintan'),
                                Forms\Components\Textarea::make('deskripsi')
                                    ->label('Deskripsi')
                                    ->rows(2),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),
                            ])
                            ->createOptionModalHeading('Tambah Kategori Bantuan Baru')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('nama_bantuan')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Bantuan')
                            ->placeholder('Contoh: Traktor Roda 4 Quick G1000')
                            ->helperText('Nama lengkap jenis bantuan yang diberikan')
                            ->prefixIcon('heroicon-o-gift')
                            ->autocomplete(false)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('periode_tahun')
                            ->label('Periode Tahun')
                            ->required()
                            ->options(function () {
                                $currentYear = now()->year;
                                $years = range($currentYear + 2, $currentYear - 10);
                                return array_combine($years, $years);
                            })
                            ->default(now()->year)
                            ->searchable()
                            ->native(false)
                            ->helperText('Tahun anggaran/periode bantuan')
                            ->prefixIcon('heroicon-o-calendar')
                            ->live()
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->label('Status Aktif')
                            ->helperText('Aktifkan untuk menampilkan di katalog bantuan')
                            ->default(true)
                            ->inline(false)
                            ->onColor('success')
                            ->offColor('gray')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail Bantuan')
                    ->description('Informasi tambahan tentang bantuan')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\RichEditor::make('deskripsi')
                            ->label('Deskripsi Lengkap')
                            ->columnSpanFull()
                            ->placeholder('Deskripsikan detail bantuan: spesifikasi, manfaat, persyaratan, dll...')
                            ->helperText('Penjelasan detail tentang jenis bantuan ini')
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'h2',
                                'h3',
                                'italic',
                                'orderedList',
                                'redo',
                                'undo',
                            ])
                            ->maxLength(2000),
                    ]),

                Forms\Components\Section::make('Informasi Tambahan')
                    ->description('Data statistik dan riwayat')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('kategori_info')
                            ->label('Kategori')
                            ->content(
                                fn(?JenisBantuan $record): string =>
                                $record?->kategoriBantuan?->nama_kategori ?? 'Kategori akan tampil setelah dipilih'
                            ),

                        Forms\Components\Placeholder::make('jumlah_tersalurkan')
                            ->label('Total Tersalurkan')
                            ->content(
                                fn(?JenisBantuan $record): string =>
                                $record
                                    ? number_format($record->sebaranBantuans()->count()) . ' Kelompok Tani'
                                    : '-'
                            )
                            ->visible(fn(?JenisBantuan $record) => $record !== null),

                        Forms\Components\Placeholder::make('total_penerima')
                            ->label('Total Anggota Penerima')
                            ->content(function (?JenisBantuan $record): string {
                                if (!$record) return '-';

                                $totalAnggota = \App\Models\KelompokTani::whereHas('sebaranBantuans', function ($q) use ($record) {
                                    $q->whereHas('jenisBantuans', function ($q2) use ($record) {
                                        $q2->where('jenis_bantuan_id', $record->id);
                                    });
                                })->sum('jumlah_anggota');

                                return number_format($totalAnggota) . ' Anggota';
                            })
                            ->visible(fn(?JenisBantuan $record) => $record !== null),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Terdaftar Sejak')
                            ->content(
                                fn(?JenisBantuan $record): string =>
                                $record?->created_at?->format('d F Y') . ' (' . $record?->created_at?->diffForHumans() . ')' ?? '-'
                            )
                            ->visible(fn(?JenisBantuan $record) => $record !== null),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->content(
                                fn(?JenisBantuan $record): string =>
                                $record?->updated_at?->diffForHumans() ?? '-'
                            )
                            ->visible(fn(?JenisBantuan $record) => $record !== null),
                    ])
                    ->columns(3)
                    ->visible(fn($operation) => $operation === 'edit' || $operation === 'view'),
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

                Tables\Columns\TextColumn::make('kategoriBantuan.nama_kategori')
                    ->searchable()
                    ->sortable()
                    ->label('Kategori')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Alsintan' => 'info',
                        'Bibit' => 'success',
                        'Pupuk' => 'warning',
                        default => 'primary',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Alsintan' => 'heroicon-o-wrench-screwdriver',
                        'Bibit' => 'heroicon-o-sparkles',
                        'Pupuk' => 'heroicon-o-beaker',
                        default => 'heroicon-o-tag',
                    })
                    ->tooltip('Kategori bantuan'),

                Tables\Columns\TextColumn::make('nama_bantuan')
                    ->searchable()
                    ->label('Nama Bantuan')
                    ->icon('heroicon-o-gift')
                    ->iconColor('success')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nama bantuan tersalin!')
                    ->description(
                        fn(JenisBantuan $record): string =>
                        Str::limit(strip_tags($record->deskripsi), 60) ?: 'Belum ada deskripsi'
                    )
                    ->wrap()
                    ->tooltip('Klik untuk menyalin'),

                Tables\Columns\TextColumn::make('periode_tahun')
                    ->sortable()
                    ->label('Tahun')
                    ->badge()
                    ->color(fn($state): string => match (true) {
                        $state < now()->year => 'gray',
                        $state == now()->year => 'success',
                        $state > now()->year => 'warning',
                        default => 'primary',
                    })
                    ->icon('heroicon-o-calendar')
                    ->alignCenter()
                    ->tooltip(fn($state): string => match (true) {
                        $state < now()->year => 'Periode lalu',
                        $state == now()->year => 'Periode berjalan',
                        $state > now()->year => 'Periode mendatang',
                        default => 'Tahun anggaran',
                    }),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status')
                    ->onColor('success')
                    ->offColor('gray')
                    ->alignCenter()
                    ->tooltip(
                        fn(bool $state): string =>
                        $state ? 'Aktif - Klik untuk nonaktifkan' : 'Nonaktif - Klik untuk aktifkan'
                    )
                    ->beforeStateUpdated(function ($record, $state) {
                        // Log atau notifikasi jika perlu
                    })
                    ->afterStateUpdated(function ($record, $state) {
                        \Filament\Notifications\Notification::make()
                            ->title('Status berhasil diubah')
                            ->body($state ? 'Bantuan diaktifkan' : 'Bantuan dinonaktifkan')
                            ->success()
                            ->send();
                    }),

                Tables\Columns\TextColumn::make('sebaran_count')
                    ->label('Tersalurkan')
                    ->state(function (JenisBantuan $record): int {
                        return $record->sebaranBantuans()->count();
                    })
                    ->badge()
                    ->color(fn($state): string => match (true) {
                        $state == 0 => 'gray',
                        $state <= 5 => 'warning',
                        $state <= 10 => 'info',
                        $state > 10 => 'success',
                    })
                    ->icon('heroicon-o-users')
                    ->suffix(' Kelompok')
                    ->alignCenter()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->withCount('sebaranBantuans')->orderBy('sebaran_bantuans_count', $direction);
                    })
                    ->tooltip(fn($state): string => $state . ' kelompok tani telah menerima bantuan ini'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->label('Dibuat')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-calendar')
                    ->tooltip(fn($state): string => $state->diffForHumans()),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d M Y, H:i')
                    ->label('Diperbarui')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock')
                    ->tooltip(fn($state): string => $state->diffForHumans())
                    ->since(),
            ])
            ->defaultSort('periode_tahun', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('kategori_bantuan_id')
                    ->label('Kategori')
                    ->relationship('kategoriBantuan', 'nama_kategori')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('Semua Kategori')
                    ->native(false)
                    ->indicator('Kategori'),

                Tables\Filters\SelectFilter::make('periode_tahun')
                    ->label('Tahun')
                    ->options(function () {
                        $years = \App\Models\JenisBantuan::distinct()
                            ->pluck('periode_tahun', 'periode_tahun')
                            ->sortDesc();
                        return $years;
                    })
                    ->multiple()
                    ->placeholder('Semua Tahun')
                    ->native(false)
                    ->indicator('Tahun'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('has_sebaran')
                    ->label('Sudah Tersalurkan')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah Tersalurkan')
                    ->falseLabel('Belum Tersalurkan')
                    ->queries(
                        true: fn(Builder $query) => $query->has('sebaranBantuans'),
                        false: fn(Builder $query) => $query->doesntHave('sebaranBantuans'),
                    )
                    ->native(false),

                Tables\Filters\Filter::make('periode_berjalan')
                    ->label('Periode Berjalan')
                    ->query(fn(Builder $query): Builder => $query->where('periode_tahun', now()->year))
                    ->toggle()
                    ->default(false),
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
                    Tables\Actions\Action::make('toggle_active')
                        ->label(
                            fn(JenisBantuan $record): string =>
                            $record->is_active ? 'Nonaktifkan' : 'Aktifkan'
                        )
                        ->icon(
                            fn(JenisBantuan $record): string =>
                            $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle'
                        )
                        ->color(
                            fn(JenisBantuan $record): string =>
                            $record->is_active ? 'gray' : 'success'
                        )
                        ->requiresConfirmation()
                        ->action(function (JenisBantuan $record) {
                            $record->update(['is_active' => !$record->is_active]);
                            \Filament\Notifications\Notification::make()
                                ->title('Status berhasil diubah')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Jenis Bantuan')
                        ->modalDescription('Apakah Anda yakin ingin menghapus jenis bantuan ini? Data sebaran bantuan terkait akan terpengaruh.')
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
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan Terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update(['is_active' => true]);
                            \Filament\Notifications\Notification::make()
                                ->title('Bantuan berhasil diaktifkan')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan Terpilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update(['is_active' => false]);
                            \Filament\Notifications\Notification::make()
                                ->title('Bantuan berhasil dinonaktifkan')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Jenis Bantuan Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua jenis bantuan yang dipilih? Data terkait akan terpengaruh.')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Jenis Bantuan')
            ->emptyStateDescription('Mulai dengan menambahkan jenis bantuan pertama Anda.')
            ->emptyStateIcon('heroicon-o-gift')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Jenis Bantuan')
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
                Infolists\Components\Section::make('Informasi Bantuan')
                    ->icon('heroicon-o-gift')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('kategoriBantuan.nama_kategori')
                                    ->label('Kategori Bantuan')
                                    ->icon('heroicon-o-folder')
                                    ->badge()
                                    ->color('primary')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('periode_tahun')
                                    ->label('Periode Tahun')
                                    ->icon('heroicon-o-calendar')
                                    ->badge()
                                    ->color(fn($state): string => match (true) {
                                        $state < now()->year => 'gray',
                                        $state == now()->year => 'success',
                                        $state > now()->year => 'warning',
                                        default => 'primary',
                                    })
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('is_active')
                                    ->label('Status')
                                    ->badge()
                                    ->formatStateUsing(
                                        fn(bool $state): string =>
                                        $state ? 'Aktif' : 'Nonaktif'
                                    )
                                    ->color(
                                        fn(bool $state): string =>
                                        $state ? 'success' : 'gray'
                                    )
                                    ->icon(
                                        fn(bool $state): string =>
                                        $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'
                                    )
                                    ->size('lg'),
                            ]),

                        Infolists\Components\TextEntry::make('nama_bantuan')
                            ->label('Nama Bantuan')
                            ->icon('heroicon-o-gift')
                            ->iconColor('success')
                            ->size('lg')
                            ->weight('bold')
                            ->copyable()
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('deskripsi')
                            ->label('Deskripsi')
                            ->html()
                            ->default('-')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Statistik Penyaluran')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('sebaran_count')
                                    ->label('Total Kelompok Penerima')
                                    ->state(
                                        fn(JenisBantuan $record): int =>
                                        $record->sebaranBantuans()->count()
                                    )
                                    ->badge()
                                    ->icon('heroicon-o-users')
                                    ->color('success')
                                    ->suffix(' Kelompok')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('total_anggota')
                                    ->label('Total Anggota Penerima')
                                    ->state(function (JenisBantuan $record): int {
                                        $kelompokIds = DB::table('sebaran_bantuan_jenis')
                                            ->where('jenis_bantuan_id', $record->id)
                                            ->join('sebaran_bantuans', 'sebaran_bantuan_jenis.sebaran_bantuan_id', '=', 'sebaran_bantuans.id')
                                            ->pluck('sebaran_bantuans.kelompok_tani_id')
                                            ->unique();

                                        return \App\Models\KelompokTani::whereIn('id', $kelompokIds)
                                            ->sum('jumlah_anggota');
                                    })
                                    ->badge()
                                    ->icon('heroicon-o-user-group')
                                    ->color('info')
                                    ->suffix(' Anggota')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('wilayah_count')
                                    ->label('Jumlah Kecamatan Tercover')
                                    ->state(function (JenisBantuan $record): int {
                                        $kelompokIds = DB::table('sebaran_bantuan_jenis')
                                            ->where('jenis_bantuan_id', $record->id)
                                            ->join('sebaran_bantuans', 'sebaran_bantuan_jenis.sebaran_bantuan_id', '=', 'sebaran_bantuans.id')
                                            ->pluck('sebaran_bantuans.kelompok_tani_id')
                                            ->unique();

                                        return \App\Models\KelompokTani::whereIn('id', $kelompokIds)
                                            ->with('desa.kecamatan')
                                            ->get()
                                            ->pluck('desa.kecamatan.id')
                                            ->unique()
                                            ->count();
                                    })
                                    ->badge()
                                    ->icon('heroicon-o-map')
                                    ->color('warning')
                                    ->suffix(' Kecamatan')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJenisBantuans::route('/'),
            'create' => Pages\CreateJenisBantuan::route('/create'),
            'view' => Pages\ViewJenisBantuan::route('/{record}'),
            'edit' => Pages\EditJenisBantuan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $active = static::getModel()::where('is_active', true)->count();
        $total = static::getModel()::count();
        return "{$active}/{$total}";
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $active = static::getModel()::where('is_active', true)->count();
        $total = static::getModel()::count();

        if ($total == 0) return 'gray';

        $percentage = ($active / $total) * 100;
        return match (true) {
            $percentage < 50 => 'danger',
            $percentage < 80 => 'warning',
            default => 'success',
        };
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Kategori' => $record->kategoriBantuan?->nama_kategori,
            'Tahun' => $record->periode_tahun,
            'Status' => $record->is_active ? 'Aktif' : 'Nonaktif',
        ];
    }
}

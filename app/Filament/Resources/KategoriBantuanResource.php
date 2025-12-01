<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\KategoriBantuan;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KategoriBantuanResource\Pages;
use App\Filament\Resources\KategoriBantuanResource\RelationManagers;

class KategoriBantuanResource extends Resource
{
    protected static ?string $model = KategoriBantuan::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = 'Kategori Bantuan';

    protected static ?string $modelLabel = 'Kategori Bantuan';

    protected static ?string $pluralModelLabel = 'Data Kategori Bantuan';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'nama_kategori';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kategori Bantuan')
                    ->description('Data utama kategori bantuan pertanian')
                    ->icon('heroicon-o-folder')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('nama_kategori')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Alsintan, Bibit, Pupuk')
                            ->helperText('Nama kategori bantuan yang akan digunakan')
                            ->prefixIcon('heroicon-o-tag')
                            ->autocomplete(false)
                            ->unique(ignoreRecord: true)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                // Auto-generate deskripsi jika kosong
                                if ($state && !$get('deskripsi')) {
                                    $deskripsi = match(strtolower($state)) {
                                        'alsintan' => 'Bantuan Alat dan Mesin Pertanian untuk meningkatkan produktivitas dan efisiensi kerja petani.',
                                        'bibit' => 'Bantuan bibit unggul berkualitas untuk meningkatkan hasil panen dan ketahanan pangan.',
                                        'pupuk' => 'Bantuan pupuk untuk meningkatkan kesuburan tanah dan hasil produksi pertanian.',
                                        default => "Kategori bantuan {$state} untuk mendukung program pertanian dan kesejahteraan petani.",
                                    };
                                    $set('deskripsi', $deskripsi);
                                }
                            })
                            ->columnSpan(2),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->required()
                            ->default(true)
                            ->helperText('Aktifkan untuk menampilkan di form jenis bantuan')
                            ->inline(false)
                            ->onColor('success')
                            ->offColor('gray')
                            ->columnSpan(1),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Detail Kategori')
                    ->description('Informasi tambahan tentang kategori')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\RichEditor::make('deskripsi')
                            ->label('Deskripsi')
                            ->columnSpanFull()
                            ->placeholder('Deskripsikan kategori bantuan: tujuan, manfaat, contoh jenis bantuan...')
                            ->helperText('Penjelasan detail tentang kategori bantuan ini')
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
                    ->description('Data statistik dan penggunaan')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('jumlah_jenis_bantuan')
                            ->label('Jumlah Jenis Bantuan')
                            ->content(fn (?KategoriBantuan $record): string =>
                                $record
                                    ? number_format($record->jenisBantuans()->count()) . ' Jenis Bantuan'
                                    : '-'
                            )
                            ->visible(fn (?KategoriBantuan $record) => $record !== null),

                        Forms\Components\Placeholder::make('jenis_bantuan_aktif')
                            ->label('Jenis Bantuan Aktif')
                            ->content(fn (?KategoriBantuan $record): string =>
                                $record
                                    ? number_format($record->jenisBantuans()->where('is_active', true)->count()) . ' Aktif'
                                    : '-'
                            )
                            ->visible(fn (?KategoriBantuan $record) => $record !== null),

                        Forms\Components\Placeholder::make('total_tersalurkan')
                            ->label('Total Bantuan Tersalurkan')
                            ->content(function (?KategoriBantuan $record): string {
                                if (!$record) return '-';

                                $total = DB::table('sebaran_bantuan_jenis')
                                    ->join('jenis_bantuans', 'sebaran_bantuan_jenis.jenis_bantuan_id', '=', 'jenis_bantuans.id')
                                    ->where('jenis_bantuans.kategori_bantuan_id', $record->id)
                                    ->distinct('sebaran_bantuan_jenis.sebaran_bantuan_id')
                                    ->count('sebaran_bantuan_jenis.sebaran_bantuan_id');

                                return number_format($total) . ' Kelompok';
                            })
                            ->visible(fn (?KategoriBantuan $record) => $record !== null),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Dibuat pada')
                            ->content(fn (?KategoriBantuan $record): string =>
                                $record?->created_at?->format('d F Y') . ' (' . $record?->created_at?->diffForHumans() . ')' ?? '-'
                            )
                            ->visible(fn (?KategoriBantuan $record) => $record !== null),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->content(fn (?KategoriBantuan $record): string =>
                                $record?->updated_at?->diffForHumans() ?? '-'
                            )
                            ->visible(fn (?KategoriBantuan $record) => $record !== null),
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

                Tables\Columns\TextColumn::make('nama_kategori')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match(strtolower($state)) {
                        'alsintan' => 'info',
                        'bibit' => 'success',
                        'pupuk' => 'warning',
                        default => 'primary',
                    })
                    ->icon(fn (string $state): string => match(strtolower($state)) {
                        'alsintan' => 'heroicon-o-wrench-screwdriver',
                        'bibit' => 'heroicon-o-sparkles',
                        'pupuk' => 'heroicon-o-beaker',
                        default => 'heroicon-o-tag',
                    })
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nama kategori tersalin!')
                    ->description(fn (KategoriBantuan $record): string =>
                        Str::limit(strip_tags($record->deskripsi), 60) ?: 'Belum ada deskripsi'
                    )
                    ->wrap()
                    ->tooltip('Klik untuk menyalin'),

                Tables\Columns\TextColumn::make('jenis_bantuans_count')
                    ->label('Jenis Bantuan')
                    ->counts('jenisBantuans')
                    ->badge()
                    ->color(fn ($state): string => match(true) {
                        $state == 0 => 'gray',
                        $state <= 5 => 'warning',
                        $state <= 10 => 'info',
                        $state > 10 => 'success',
                    })
                    ->icon('heroicon-o-gift')
                    ->suffix(' Jenis')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn ($state): string => $state . ' jenis bantuan terdaftar'),

                Tables\Columns\TextColumn::make('jenis_bantuan_aktif')
                    ->label('Aktif')
                    ->state(function (KategoriBantuan $record): int {
                        return $record->jenisBantuans()->where('is_active', true)->count();
                    })
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->suffix(' Aktif')
                    ->alignCenter()
                    ->tooltip('Jumlah jenis bantuan yang aktif'),

                Tables\Columns\TextColumn::make('tersalurkan_count')
                    ->label('Tersalurkan')
                    ->state(function (KategoriBantuan $record): int {
                        return DB::table('sebaran_bantuan_jenis')
                            ->join('jenis_bantuans', 'sebaran_bantuan_jenis.jenis_bantuan_id', '=', 'jenis_bantuans.id')
                            ->where('jenis_bantuans.kategori_bantuan_id', $record->id)
                            ->distinct('sebaran_bantuan_jenis.sebaran_bantuan_id')
                            ->count('sebaran_bantuan_jenis.sebaran_bantuan_id');
                    })
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-users')
                    ->suffix(' Kelompok')
                    ->alignCenter()
                    ->tooltip('Total kelompok yang menerima bantuan kategori ini'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status')
                    ->onColor('success')
                    ->offColor('gray')
                    ->alignCenter()
                    ->tooltip(fn (bool $state): string =>
                        $state ? 'Aktif - Klik untuk nonaktifkan' : 'Nonaktif - Klik untuk aktifkan'
                    )
                    ->afterStateUpdated(function ($record, $state) {
                        \Filament\Notifications\Notification::make()
                            ->title('Status berhasil diubah')
                            ->body($state ? 'Kategori diaktifkan' : 'Kategori dinonaktifkan')
                            ->success()
                            ->send();
                    }),

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
            ->defaultSort('nama_kategori', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('has_jenis_bantuan')
                    ->label('Memiliki Jenis Bantuan')
                    ->placeholder('Semua Kategori')
                    ->trueLabel('Dengan Jenis Bantuan')
                    ->falseLabel('Tanpa Jenis Bantuan')
                    ->queries(
                        true: fn (Builder $query) => $query->has('jenisBantuans'),
                        false: fn (Builder $query) => $query->doesntHave('jenisBantuans'),
                    )
                    ->native(false),

                Tables\Filters\Filter::make('populer')
                    ->label('Kategori Populer')
                    ->query(function (Builder $query): Builder {
                        return $query->has('jenisBantuans', '>=', 5);
                    })
                    ->toggle(),
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
                        ->label(fn (KategoriBantuan $record): string =>
                            $record->is_active ? 'Nonaktifkan' : 'Aktifkan'
                        )
                        ->icon(fn (KategoriBantuan $record): string =>
                            $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle'
                        )
                        ->color(fn (KategoriBantuan $record): string =>
                            $record->is_active ? 'gray' : 'success'
                        )
                        ->requiresConfirmation()
                        ->action(function (KategoriBantuan $record) {
                            $record->update(['is_active' => !$record->is_active]);
                            \Filament\Notifications\Notification::make()
                                ->title('Status berhasil diubah')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Kategori Bantuan')
                        ->modalDescription('Apakah Anda yakin ingin menghapus kategori ini? Data jenis bantuan terkait akan terpengaruh.')
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
                                ->title('Kategori berhasil diaktifkan')
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
                                ->title('Kategori berhasil dinonaktifkan')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Kategori Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua kategori yang dipilih? Data terkait akan terpengaruh.')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Kategori Bantuan')
            ->emptyStateDescription('Mulai dengan menambahkan kategori bantuan pertama seperti Alsintan, Bibit, atau Pupuk.')
            ->emptyStateIcon('heroicon-o-folder')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Kategori Bantuan')
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
                Infolists\Components\Section::make('Informasi Kategori')
                    ->icon('heroicon-o-folder')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('nama_kategori')
                                    ->label('Nama Kategori')
                                    ->badge()
                                    ->color(fn (string $state): string => match(strtolower($state)) {
                                        'alsintan' => 'info',
                                        'bibit' => 'success',
                                        'pupuk' => 'warning',
                                        default => 'primary',
                                    })
                                    ->icon(fn (string $state): string => match(strtolower($state)) {
                                        'alsintan' => 'heroicon-o-wrench-screwdriver',
                                        'bibit' => 'heroicon-o-sparkles',
                                        'pupuk' => 'heroicon-o-beaker',
                                        default => 'heroicon-o-tag',
                                    })
                                    ->size('lg')
                                    ->weight('bold')
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('is_active')
                                    ->label('Status')
                                    ->badge()
                                    ->formatStateUsing(fn (bool $state): string =>
                                        $state ? 'Aktif' : 'Nonaktif'
                                    )
                                    ->color(fn (bool $state): string =>
                                        $state ? 'success' : 'gray'
                                    )
                                    ->icon(fn (bool $state): string =>
                                        $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'
                                    )
                                    ->size('lg'),
                            ]),

                        Infolists\Components\TextEntry::make('deskripsi')
                            ->label('Deskripsi')
                            ->html()
                            ->default('Belum ada deskripsi')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Statistik Kategori')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('total_jenis')
                                    ->label('Total Jenis Bantuan')
                                    ->state(fn (KategoriBantuan $record): int => $record->jenisBantuans()->count())
                                    ->badge()
                                    ->icon('heroicon-o-gift')
                                    ->color('info')
                                    ->suffix(' Jenis')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('jenis_aktif')
                                    ->label('Jenis Bantuan Aktif')
                                    ->state(fn (KategoriBantuan $record): int => $record->jenisBantuans()->where('is_active', true)->count())
                                    ->badge()
                                    ->icon('heroicon-o-check-circle')
                                    ->color('success')
                                    ->suffix(' Aktif')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('tersalurkan')
                                    ->label('Total Tersalurkan')
                                    ->state(function (KategoriBantuan $record): int {
                                        return DB::table('sebaran_bantuan_jenis')
                                            ->join('jenis_bantuans', 'sebaran_bantuan_jenis.jenis_bantuan_id', '=', 'jenis_bantuans.id')
                                            ->where('jenis_bantuans.kategori_bantuan_id', $record->id)
                                            ->distinct('sebaran_bantuan_jenis.sebaran_bantuan_id')
                                            ->count('sebaran_bantuan_jenis.sebaran_bantuan_id');
                                    })
                                    ->badge()
                                    ->icon('heroicon-o-users')
                                    ->color('primary')
                                    ->suffix(' Kelompok')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('periode')
                                    ->label('Periode Tahun')
                                    ->state(function (KategoriBantuan $record): string {
                                        $tahun = $record->jenisBantuans()
                                            ->distinct('periode_tahun')
                                            ->pluck('periode_tahun')
                                            ->sort()
                                            ->values();

                                        if ($tahun->isEmpty()) return '-';
                                        if ($tahun->count() == 1) return $tahun->first();
                                        return $tahun->first() . ' - ' . $tahun->last();
                                    })
                                    ->badge()
                                    ->icon('heroicon-o-calendar')
                                    ->color('warning')
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
            'index' => Pages\ListKategoriBantuans::route('/'),
            'create' => Pages\CreateKategoriBantuan::route('/create'),
            'view' => Pages\ViewKategoriBantuan::route('/{record}'),
            'edit' => Pages\EditKategoriBantuan::route('/{record}/edit'),
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
        return match(true) {
            $percentage < 50 => 'danger',
            $percentage < 80 => 'warning',
            default => 'success',
        };
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        $jenisCount = $record->jenisBantuans()->count();

        return [
            'Jenis Bantuan' => "{$jenisCount} jenis",
            'Status' => $record->is_active ? 'Aktif' : 'Nonaktif',
        ];
    }
}
